<?php

namespace App\Http\Controllers;

use App\Models\AddOn;
use App\Models\Booking;
use App\Models\BookingAddOn;
use App\Models\Package;
use App\Models\Schedule;
use App\Models\User;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected WhatsAppNotificationService $waService
    ) {}

    public function index(): View
    {
        $packages = Package::where('is_active', true)->orderBy('price', 'asc')->get();
        $addOns = AddOn::where('is_active', true)->get();

        $today = Carbon::today()->toDateString();

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

        return view('customer.packages', compact('packages', 'addOns', 'inStudio', 'callingNext', 'activeQueues', 'waitingCount'));
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
            'package_id' => ['required', 'exists:packages,id'],
            'schedule_id' => ['required', 'exists:schedules,id'],
            'studio_backdrop' => ['nullable', 'string', 'max:80'],
            'participants_count' => ['nullable', 'integer', 'min:1', 'max:12'],
            'name' => ['nullable', 'string', 'max:120'],
            'phone_whatsapp' => ['nullable', 'string', 'max:25'],
            'notes' => ['nullable', 'string', 'max:255'],
            'add_ons' => ['nullable', 'array'],
            'add_ons.*' => ['exists:add_ons,id'],
        ]);

        $user = Auth::user();

        // If user is guest, automatically log in or register customer
        if (! $user) {
            $user = User::firstOrCreate(
                ['email' => 'hananaeylaa@gmail.com'],
                [
                    'name' => $validated['name'] ?? 'Hana Naila Rahmadina',
                    'phone_whatsapp' => $validated['phone_whatsapp'] ?? '081234567890',
                    'password' => bcrypt('password'),
                    'role' => 'customer',
                    'is_active' => true,
                ]
            );
            Auth::login($user);
        }

        try {
            $booking = DB::transaction(function () use ($validated, $user) {
                // 1. Lock the selected schedule slot for update to avoid race conditions (Anti-Double Booking)
                $schedule = Schedule::where('id', $validated['schedule_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                // 2. Count active bookings in this exact slot
                $activeBookedCount = Booking::where('schedule_id', $schedule->id)
                    ->whereNotIn('status', ['cancelled'])
                    ->count();

                if ($activeBookedCount >= $schedule->capacity) {
                    throw new \RuntimeException('Maaf, kuota slot waktu ini baru saja penuh terisi oleh pemesan lain. Silakan pilih slot waktu lain.');
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

                // 4. Generate sequential atomic queue number for this date (e.g. KTH-001)
                $dailyBookingsCount = Booking::whereHas('schedule', function ($q) use ($schedule) {
                    $q->whereDate('date', $schedule->date);
                })->count();

                $queueNumber = sprintf('KTH-%03d', $dailyBookingsCount + 1);

                // 5. Create Booking Record
                $newBooking = Booking::create([
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'schedule_id' => $schedule->id,
                    'booking_date' => Carbon::now(),
                    'booking_type' => 'online',
                    'participants_count' => $validated['participants_count'] ?? $package->max_participants,
                    'status' => 'waiting_verification',
                    'queue_number' => $queueNumber,
                    'studio_backdrop' => $validated['studio_backdrop'] ?? 'Electric Blue Backdrop',
                    'package_price' => $packagePrice,
                    'add_ons_total' => $addOnsTotal,
                    'total_price' => $totalPrice,
                    'notes' => $validated['notes'] ?? null,
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

            // 7. Dispatch WhatsApp notification (fail-safe)
            $this->waService->sendBookingConfirmation($booking);

            return redirect()->route('payment.checkout', $booking->id)
                ->with('success', "Reservasi berhasil dibuat! Nomor antrean Anda adalah {$booking->queue_number}. Silakan lakukan pembayaran via QRIS.");
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem saat membuat booking: '.$e->getMessage());
        }
    }

    public function history(Request $request): View
    {
        $userId = Auth::id() ?? 1;

        $query = Booking::with(['package', 'schedule', 'payment', 'bookingAddOns.addOn'])
            ->where('user_id', $userId)
            ->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->get();

        return view('customer.history', compact('bookings'));
    }

    public function ticket(Booking $booking): View
    {
        $this->authorizeBookingAccess($booking);
        $booking->load(['user', 'package', 'schedule', 'payment', 'bookingAddOns.addOn']);

        return view('customer.ticket', compact('booking'));
    }

    protected function authorizeBookingAccess(Booking $booking): void
    {
        $user = Auth::user();
        if ($user && ($user->isAdmin() || $user->isCashier())) {
            return;
        }

        if (! $user || $user->id !== $booking->user_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat tiket booking ini.');
        }
    }
}
