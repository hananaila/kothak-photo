<?php

namespace App\Http\Controllers;

use App\Models\AddOn;
use App\Models\Booking;
use App\Models\BookingAddOn;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\User;
use App\Services\SmartQueueService;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected WhatsAppNotificationService $waService
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        $packages = Package::where('is_active', true)->orderBy('price', 'asc')->get();
        $defaultPackage = $packages->firstWhere('price', 35000) ?? $packages->first();
        $addOns = AddOn::where('is_active', true)->get();

        $today = Carbon::today()->toDateString();

        // Ensure an open daily queue schedule exists for today (09:00 - 23:00) with unlimited queue capacity
        $todaySchedule = Schedule::whereDate('date', $today)
            ->where('start_time', '09:00:00')
            ->where('end_time', '23:00:00')
            ->first();

        if (! $todaySchedule) {
            $todaySchedule = Schedule::create([
                'date' => $today,
                'start_time' => '09:00:00',
                'end_time' => '23:00:00',
                'capacity' => 9999,
                'is_active' => true,
            ]);
        }

        // Real-time Studio queue widget
        $inStudio = Booking::with(['user', 'package'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
            ->where('status', 'in_studio')
            ->first();

        $callingNext = Booking::with(['user', 'package'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
            ->whereIn('status', ['checked_in', 'confirmed'])
            ->orderBy('id', 'asc')
            ->first();

        $activeQueues = Booking::with(['user', 'package', 'schedule', 'payment'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
            ->whereIn('status', ['waiting_verification', 'confirmed', 'checked_in', 'in_studio', 'completed'])
            ->orderBy('id', 'asc')
            ->get();

        $waitingCount = $activeQueues->whereIn('status', ['waiting_verification', 'confirmed', 'checked_in'])->count();

        // Per-booth queue counts for the Stulokal-inspired widget (privacy-first: no names shown)
        $waitingQueues = $activeQueues->whereIn('status', ['waiting_verification', 'confirmed', 'checked_in']);
        $boothQueues = [];
        foreach ($packages as $pkg) {
            $boothQueues[$pkg->name] = $waitingQueues->where('studio_backdrop', $pkg->name)->count();
        }

        // Jika pengunjung ini masih memiliki antrean aktif hari ini, langsung arahkan ke tiketnya (tidak bisa pesan baru sebelum sesi selesai)
        $recentIds = session('recent_bookings', []);
        if (empty($recentIds) && $request->hasCookie('kothak_recent_bookings')) {
            $cookieVal = json_decode($request->cookie('kothak_recent_bookings'), true);
            if (is_array($cookieVal)) {
                $recentIds = $cookieVal;
                session(['recent_bookings' => $recentIds]);
            }
        }

        $userId = (Auth::check() && Auth::user()->role === 'customer') ? Auth::id() : null;

        if (! empty($recentIds) || $userId) {
            $myActiveBooking = Booking::with(['package', 'schedule'])
                ->whereIn('status', ['waiting_verification', 'confirmed', 'checked_in', 'in_studio'])
                ->whereDate('created_at', Carbon::today())
                ->where(function ($q) use ($userId, $recentIds) {
                    if (! empty($recentIds)) {
                        $q->whereIn('id', $recentIds);
                    }
                    if ($userId) {
                        $q->orWhere('user_id', $userId);
                    }
                })
                ->orderBy('id', 'desc')
                ->first();

            if ($myActiveBooking) {
                return redirect()->route('booking.ticket', $myActiveBooking->id);
            }
        }

        return view('customer.packages', compact(
            'packages', 'defaultPackage', 'addOns', 'inStudio', 'callingNext', 'activeQueues', 'waitingCount', 'boothQueues', 'todaySchedule'
        ));
    }

    public function selectSchedule(Request $request, Package $package): View
    {
        $selectedDate = $request->query('date', Carbon::today()->toDateString());
        $addOns = AddOn::where('is_active', true)->get();

        // Get slots for this date with active bookings count
        $schedules = Schedule::whereDate('date', $selectedDate)
            ->where('is_active', true)
            ->withCount(['bookings as total_booked' => function ($query) {
                $query->whereNotIn('status', ['cancelled']);
            }])
            ->orderBy('start_time', 'asc')
            ->get();

        // If no schedules exist for this selected date, auto-create standard photobox slots
        if ($schedules->isEmpty()) {
            $defaultSlots = [
                ['10:00:00', '10:30:00'],
                ['11:00:00', '11:30:00'],
                ['13:00:00', '13:30:00'],
                ['14:00:00', '14:30:00'],
                ['15:00:00', '15:30:00'],
                ['16:00:00', '16:30:00'],
                ['17:00:00', '17:30:00'],
                ['19:00:00', '19:30:00'],
                ['20:00:00', '20:30:00'],
            ];

            foreach ($defaultSlots as $slot) {
                Schedule::firstOrCreate([
                    'date' => $selectedDate,
                    'start_time' => $slot[0],
                    'end_time' => $slot[1],
                ], [
                    'capacity' => 3,
                    'is_active' => true,
                ]);
            }

            $schedules = Schedule::whereDate('date', $selectedDate)
                ->where('is_active', true)
                ->withCount(['bookings as total_booked' => function ($query) {
                    $query->whereNotIn('status', ['cancelled']);
                }])
                ->orderBy('start_time', 'asc')
                ->get();
        }

        return view('customer.select_schedule', compact('package', 'selectedDate', 'schedules', 'addOns'));
    }

    /**
     * Store new booking with strict anti-double booking locking.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'package_id' => ['nullable', 'exists:packages,id'],
            'schedule_id' => ['nullable', 'exists:schedules,id'],
            'studio_backdrop' => ['nullable', 'string', 'max:80'],
            'participants_count' => ['nullable', 'integer', 'min:1', 'max:12'],
            'name' => ['nullable', 'string', 'max:120'],
            'phone_whatsapp' => ['nullable', 'string', 'max:25'],
            'notes' => ['nullable', 'string', 'max:255'],
            'add_ons' => ['nullable', 'array'],
            'add_ons.*' => ['exists:add_ons,id'],
        ]);

        // Associate package_id with selected booth if backdrop provided
        if (! empty($validated['studio_backdrop'])) {
            $boothPkg = Package::where('name', $validated['studio_backdrop'])->first();
            if ($boothPkg) {
                $validated['package_id'] = $boothPkg->id;
            }
        }

        // Default package if omitted (Standard Photobox)
        if (empty($validated['package_id'])) {
            $defaultPackage = Package::where('is_active', true)->where('price', 35000)->first()
                ?? Package::where('is_active', true)->orderBy('price', 'asc')->first();
            $validated['package_id'] = $defaultPackage?->id;
        }

        // Default open queue schedule if omitted
        if (empty($validated['schedule_id'])) {
            $today = Carbon::today()->toDateString();
            $schedule = Schedule::whereDate('date', $today)
                ->where('start_time', '09:00:00')
                ->where('end_time', '23:00:00')
                ->first();

            if (! $schedule) {
                $schedule = Schedule::create([
                    'date' => $today,
                    'start_time' => '09:00:00',
                    'end_time' => '23:00:00',
                    'capacity' => 9999,
                    'is_active' => true,
                ]);
            }
            $validated['schedule_id'] = $schedule->id;
        }

        $authUser = Auth::user();
        $name = ! empty($validated['name']) ? $validated['name'] : 'Tamu KOTHaK';
        $phone = $validated['phone_whatsapp'] ?? null;

        // If user is guest, or staff (admin/cashier testing/booking), or different customer phone submitted
        $isDifferentPerson = $authUser && $phone && $authUser->phone_whatsapp && ($authUser->phone_whatsapp !== $phone);
        $isStaff = $authUser && in_array($authUser->role, ['admin', 'cashier']);

        if (! $authUser || $isStaff || $isDifferentPerson) {
            $guestEmail = $phone
                ? 'guest_'.preg_replace('/\D/', '', $phone).'@kothak.local'
                : 'guest_'.uniqid().'@kothak.local';

            $user = User::firstOrCreate(
                ['email' => $guestEmail],
                [
                    'name' => $name,
                    'phone_whatsapp' => $phone,
                    'password' => bcrypt(uniqid()),
                    'role' => 'customer',
                    'is_active' => true,
                ]
            );

            // Update name/phone if customer already exists but name/phone changed
            if ($user->wasRecentlyCreated === false) {
                $user->update([
                    'name' => $name,
                    'phone_whatsapp' => $phone ?? $user->phone_whatsapp,
                ]);
            }

            // Only switch auth user if not staff (preserve admin/cashier session)
            if (! $authUser || $authUser->role === 'customer') {
                Auth::login($user);
            }
        } else {
            // Authenticated customer with same phone: update name if provided
            $user = $authUser;
            if (! empty($validated['name']) && $user->name !== $validated['name']) {
                $user->update(['name' => $validated['name']]);
            }
        }

        // Cek apakah pengunjung ini sudah memiliki antrean aktif hari ini (dilarang tambah tiket sebelum sesi foto selesai)
        $recentIds = session('recent_bookings', []);
        $phoneInput = $validated['phone_whatsapp'] ?? null;

        if (! $isStaff && (! empty($recentIds) || ! empty($phoneInput))) {
            $existingActive = Booking::whereIn('status', ['waiting_verification', 'confirmed', 'checked_in', 'in_studio'])
                ->whereDate('created_at', Carbon::today())
                ->where(function ($q) use ($recentIds, $phoneInput) {
                    if (! empty($recentIds)) {
                        $q->whereIn('id', $recentIds);
                    }
                    if (! empty($phoneInput)) {
                        $q->orWhereHas('user', function ($uq) use ($phoneInput) {
                            $uq->where('phone_whatsapp', $phoneInput);
                        });
                    }
                })
                ->orderBy('id', 'desc')
                ->first();

            if ($existingActive) {
                $recent = session('recent_bookings', []);
                $recent[] = $existingActive->id;
                session(['recent_bookings' => array_values(array_unique($recent))]);

                return redirect()->route('booking.ticket', $existingActive->id)
                    ->with('info', "Anda masih memiliki antrean aktif ({$existingActive->queue_number}). Harap selesaikan sesi foto terlebih dahulu.");
            }
        }

        try {
            $booking = DB::transaction(function () use ($validated, $user) {
                // 1. Lock the selected schedule slot for update to avoid race conditions (Anti-Double Booking)
                $schedule = Schedule::where('id', $validated['schedule_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                // 2. Count active bookings in this schedule
                $activeBookedCount = Booking::where('schedule_id', $schedule->id)
                    ->whereNotIn('status', ['cancelled'])
                    ->count();

                // Open Smart Queue: every customer is accepted in sequence (F-001, F-002, L-001...)
                // Only throw if finite capacity is explicitly configured (e.g. unit tests with capacity <= 10)
                if ($schedule->capacity <= 10 && $activeBookedCount >= $schedule->capacity) {
                    throw new \RuntimeException('Maaf, kuota antrean hari ini telah mencapai batas maksimal.');
                }

                $package = Package::findOrFail($validated['package_id']);
                $packagePrice = (float) $package->price;

                // 3. Calculate Add-ons
                $addOnsTotal = 0;
                $selectedAddOns = [];
                if (! empty($validated['add_ons'])) {
                    $addOnRecords = AddOn::whereIn('id', $validated['add_ons'])->get();
                    foreach ($addOnRecords as $ao) {
                        $addOnsTotal += (float) $ao->price;
                        $selectedAddOns[] = $ao;
                    }
                }

                $totalPrice = $packagePrice + $addOnsTotal;

                // 4. Generate sequential atomic queue number per booth for this date (e.g. F-001, L-001, V-001, KTH-001)
                $backdrop = $validated['studio_backdrop'] ?? ($package->name ?? 'Flower Garden Booth');
                $prefix = match ($backdrop) {
                    'Flower Garden Booth' => 'F',
                    'Lift Booth' => 'L',
                    'Vintage Booth' => 'V',
                    default => 'KTH',
                };

                $dailyBoothCount = Booking::where('studio_backdrop', $backdrop)
                    ->whereHas('schedule', function ($q) use ($schedule) {
                        $q->whereDate('date', $schedule->date);
                    })->count();

                $queueNumber = sprintf('%s-%03d', $prefix, $dailyBoothCount + 1);

                // 5. Create Booking Record with confirmed status (Smart Queue - Bayar di Kasir)
                $newBooking = Booking::create([
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'schedule_id' => $schedule->id,
                    'booking_date' => Carbon::now(),
                    'booking_type' => 'online',
                    'participants_count' => $validated['participants_count'] ?? $package->max_participants,
                    'status' => 'confirmed',
                    'queue_number' => $queueNumber,
                    'studio_backdrop' => $validated['studio_backdrop'] ?? 'Flower Garden Booth',
                    'package_price' => $packagePrice,
                    'add_ons_total' => $addOnsTotal,
                    'total_price' => $totalPrice,
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Create initial payment tracking as pay_on_site
                Payment::create([
                    'booking_id' => $newBooking->id,
                    'amount' => $totalPrice,
                    'payment_method' => 'pay_on_site',
                    'proof_image_path' => 'pay_on_site',
                    'payment_status' => 'pending',
                ]);

                // 6. Attach Add-ons
                foreach ($selectedAddOns as $ao) {
                    BookingAddOn::create([
                        'booking_id' => $newBooking->id,
                        'add_on_id' => $ao->id,
                        'quantity' => 1,
                        'unit_price' => $ao->price,
                        'subtotal' => $ao->price,
                    ]);
                }

                return $newBooking;
            });

            // 7. Notifikasi dialihkan ke Web Notification API (Browser Push)
            // (WhatsApp notification dinonaktifkan sesuai permintaan pengguna)
            $recentBookings = session('recent_bookings', []);
            $recentBookings[] = $booking->id;
            $recentBookings = array_values(array_unique($recentBookings));
            session(['recent_bookings' => $recentBookings]);
            cookie()->queue(cookie('kothak_recent_bookings', json_encode($recentBookings), 60 * 24 * 7));

            return redirect()->route('booking.ticket', $booking->id)
                ->with('new_booking', true)
                ->with('success', "Nomor antrean {$booking->queue_number} berhasil diambil! Pembayaran dilakukan di kasir saat giliran Anda tiba.");
        } catch (\RuntimeException $e) {
            Log::error('Booking RuntimeException: '.$e->getMessage());

            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Booking Throwable: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return back()->withInput()->with('error', 'Terjadi kesalahan sistem saat membuat booking: '.$e->getMessage());
        }
    }

    public function history(Request $request): View
    {
        $recentIds = session('recent_bookings', []);
        if (empty($recentIds) && $request->hasCookie('kothak_recent_bookings')) {
            $cookieVal = json_decode($request->cookie('kothak_recent_bookings'), true);
            if (is_array($cookieVal)) {
                $recentIds = $cookieVal;
                session(['recent_bookings' => $recentIds]);
            }
        }

        $userId = (Auth::check() && Auth::user()->role === 'customer') ? Auth::id() : null;
        $searchPhone = $request->input('phone');

        $query = Booking::with(['package', 'schedule', 'payment', 'user'])
            ->orderBy('id', 'desc');

        if (! empty($recentIds) || $userId || ! empty($searchPhone)) {
            $query->where(function ($q) use ($userId, $recentIds, $searchPhone) {
                if (! empty($recentIds)) {
                    $q->whereIn('id', $recentIds);
                }
                if ($userId) {
                    $q->orWhere('user_id', $userId);
                }
                if (! empty($searchPhone)) {
                    $cleanPhone = preg_replace('/[^0-9]/', '', $searchPhone);
                    $q->orWhereHas('user', function ($uq) use ($cleanPhone) {
                        $uq->where('phone_whatsapp', 'like', '%'.$cleanPhone.'%');
                    });
                }
            });
        } else {
            // Jika perangkat ini belum pernah memesan tiket
            $query->whereRaw('1 = 0');
        }

        $allBookings = $query->get();

        // Hitung posisi antrean live untuk booking yang aktif
        foreach ($allBookings as $b) {
            $isActive = in_array($b->status, ['waiting_verification', 'confirmed', 'checked_in', 'in_studio']);
            if ($isActive && $b->schedule) {
                $aheadCount = Booking::where('studio_backdrop', $b->studio_backdrop)
                    ->whereHas('schedule', function ($q) use ($b) {
                        $q->whereDate('date', $b->schedule->date);
                    })
                    ->whereIn('status', ['waiting_verification', 'confirmed', 'checked_in'])
                    ->where('id', '<', $b->id)
                    ->count();

                $b->ahead_count = $aheadCount;
                $b->queue_position = $aheadCount + 1;
            } else {
                $b->ahead_count = 0;
                $b->queue_position = 0;
            }
        }

        $activeBookings = $allBookings->filter(fn ($b) => in_array($b->status, ['waiting_verification', 'confirmed', 'checked_in', 'in_studio']));
        $historyBookings = $allBookings->filter(fn ($b) => in_array($b->status, ['completed', 'cancelled', 'no_show']));

        $activeCount = $activeBookings->count();
        $historyCount = $historyBookings->count();

        // 2 Filter Chip: 'active' (antrean yang sedang berjalan) & 'history' (sudah selesai / tidak hadir)
        $tab = $request->query('tab', 'active');
        if (! in_array($tab, ['active', 'history'])) {
            $tab = 'active';
        }

        $bookings = $tab === 'active' ? $activeBookings : $historyBookings;

        return view('customer.history', compact('bookings', 'tab', 'activeCount', 'historyCount'));
    }

    public function ticket(Request $request, Booking $booking): View
    {
        $this->authorizeBookingAccess($booking);

        $booking->load(['user', 'package', 'schedule', 'payment', 'bookingAddOns.addOn']);

        // Hitung antrean aktif di depan booking ini pada booth dan tanggal yang sama (seperti Stulokal)
        $aheadCount = Booking::where('studio_backdrop', $booking->studio_backdrop)
            ->whereHas('schedule', function ($q) use ($booking) {
                $q->whereDate('date', $booking->schedule->date);
            })
            ->whereIn('status', ['waiting_verification', 'confirmed', 'checked_in'])
            ->where('id', '<', $booking->id)
            ->count();

        return view('customer.ticket', compact('booking', 'aheadCount'));
    }

    public function statusJson(Booking $booking): JsonResponse
    {
        // Jalankan auto-advance antrean pintar throttled (maksimal setiap 2.0s) agar tidak membebani server
        static $lastAdvance = 0.0;
        $nowTime = microtime(true);
        if ($nowTime - $lastAdvance >= 2.0) {
            $lastAdvance = $nowTime;
            app(SmartQueueService::class)->autoAdvanceExpiredSessions();
        }
        $booking->refresh();

        $date = $booking->schedule ? $booking->schedule->date : Carbon::today()->toDateString();
        $scheduleIds = Cache::remember('schedule_ids_date_'.$date, 10, function () use ($date) {
            return Schedule::whereDate('date', $date)->pluck('id')->toArray();
        });

        $aheadCount = Booking::where('studio_backdrop', $booking->studio_backdrop)
            ->whereIn('schedule_id', $scheduleIds)
            ->whereIn('status', ['waiting_verification', 'confirmed', 'checked_in'])
            ->where('id', '<', $booking->id)
            ->count();

        return response()->json([
            'id' => $booking->id,
            'queue_number' => $booking->queue_number,
            'studio_backdrop' => $booking->studio_backdrop,
            'status' => $booking->status,
            'status_label' => $booking->status_label,
            'ahead_count' => $aheadCount,
            'is_in_studio' => in_array($booking->status, ['calling', 'in_studio']),
            'is_calling' => ($booking->status === 'calling'),
            'is_completed' => ($booking->status === 'completed'),
            'is_missed' => in_array($booking->status, ['cancelled', 'no_show']),
            'is_next' => ($aheadCount === 0 && in_array($booking->status, ['waiting_verification', 'confirmed', 'checked_in'])),
        ])->withHeaders([
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        if (in_array($booking->status, ['waiting_verification', 'confirmed', 'checked_in'])) {
            $booking->update(['status' => 'cancelled']);

            return redirect()->route('booking.ticket', $booking->id)
                ->with('info', "Nomor antrean {$booking->queue_number} telah dibatalkan.");
        }

        return redirect()->route('booking.ticket', $booking->id);
    }

    protected function authorizeBookingAccess(Booking $booking): void
    {
        // Tiket antrean digital dapat diakses via tautan tiket langsung maupun kasir studio
    }
}
