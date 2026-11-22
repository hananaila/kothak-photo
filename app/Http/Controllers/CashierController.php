<?php

namespace App\Http\Controllers;

use App\Models\Booking;
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
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CashierController extends Controller
{
    public function __construct(
        protected WhatsAppNotificationService $waService,
        protected SmartQueueService $smartQueueService
    ) {}

    public function dashboard(): Response
    {
        $today = Carbon::today()->toDateString();

        // 1. Jalankan auto-advance sesi antrean yang sudah mencapai/melewati 15 menit
        $this->smartQueueService->autoAdvanceExpiredSessions();

        $activePackages = Package::where('is_active', true)->orderBy('id', 'asc')->get();

        $colorPalette = ['#1E3A8A', '#2D2DB0', '#854D0E', '#047857', '#9333EA', '#D97706'];
        $booths = [];
        $i = 0;

        foreach ($activePackages as $pkg) {
            $name = $pkg->name;
            $prefix = match ($name) {
                'Flower Garden Booth' => 'F',
                'Lift Booth' => 'L',
                'Vintage Booth' => 'V',
                default => strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 1) ?: 'B'),
            };

            // 1. Sesi yang sedang aktif atau dipanggil di booth ini
            $activeBooking = Booking::with(['user', 'package', 'schedule'])
                ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
                ->where('studio_backdrop', $name)
                ->whereIn('status', ['calling', 'in_studio'])
                ->first();

            $durationMinutes = $pkg->duration ?: 15;
            $remainingSeconds = 0;
            $confirmationSeconds = 0;
            $elapsedSeconds = 0;
            $isCalling = false;

            if ($activeBooking) {
                if ($activeBooking->status === 'calling') {
                    $isCalling = true;
                    $callStart = $activeBooking->called_at ?: $activeBooking->updated_at;
                    $callEnd = $callStart->copy()->addSeconds(30);
                    $confirmationSeconds = max(0, (int) Carbon::now()->diffInSeconds($callEnd, false));
                } else {
                    $sessionStart = $activeBooking->called_at ?: $activeBooking->updated_at;
                    $sessionEnd = $sessionStart->copy()->addMinutes($durationMinutes);
                    $remainingSeconds = max(0, (int) Carbon::now()->diffInSeconds($sessionEnd, false));
                    $elapsedSeconds = max(0, (int) Carbon::now()->diffInSeconds($sessionStart));
                }
            }

            // 2. Daftar antrean menunggu di booth ini
            $waitingList = Booking::with(['user', 'package', 'schedule'])
                ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
                ->where('studio_backdrop', $name)
                ->whereIn('status', ['waiting_verification', 'confirmed', 'checked_in'])
                ->orderBy('id', 'asc')
                ->get();

            // 3. Riwayat sesi selesai / tidak hadir hari ini di booth ini
            $finishedList = Booking::with(['user', 'package', 'schedule'])
                ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
                ->where('studio_backdrop', $name)
                ->whereIn('status', ['completed', 'cancelled', 'no_show'])
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();

            $booths[] = [
                'name' => $name,
                'prefix' => $prefix,
                'description' => $pkg->description,
                'color' => $colorPalette[$i % count($colorPalette)],
                'active_booking' => $activeBooking,
                'is_calling' => $isCalling,
                'confirmation_seconds' => $confirmationSeconds,
                'in_studio' => ($activeBooking && $activeBooking->status === 'in_studio') ? $activeBooking : null,
                'calling' => ($activeBooking && $activeBooking->status === 'calling') ? $activeBooking : null,
                'duration_minutes' => $durationMinutes,
                'remaining_seconds' => $remainingSeconds,
                'elapsed_seconds' => $elapsedSeconds,
                'next' => $waitingList->first(),
                'waiting' => $waitingList,
                'finished' => $finishedList,
                'waiting_count' => $waitingList->count(),
                'total_today' => Booking::whereHas('schedule', fn ($q) => $q->whereDate('date', $today))->where('studio_backdrop', $name)->count(),
            ];
            $i++;
        }

        // Data pendukung kompatibilitas
        $pendingPayments = Payment::with(['booking.user', 'booking.package', 'booking.schedule'])
            ->where('payment_status', 'pending')
            ->orderBy('id', 'desc')
            ->get();
        $allPayments = Payment::with(['booking.user', 'booking.package', 'booking.schedule'])
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();
        $todayBookings = Booking::with(['user', 'package', 'schedule'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
            ->orderBy('id', 'asc')
            ->get();
        $inStudio = Booking::whereHas('schedule', fn ($q) => $q->whereDate('date', $today))->where('status', 'in_studio')->first();
        $nextQueue = Booking::whereHas('schedule', fn ($q) => $q->whereDate('date', $today))->whereIn('status', ['checked_in', 'confirmed'])->first();
        $packages = Package::where('is_active', true)->get();
        $todaySchedules = Schedule::whereDate('date', $today)->where('is_active', true)->orderBy('start_time')->get();

        return response()
            ->view('cashier.dashboard', compact(
                'booths',
                'today',
                'pendingPayments',
                'allPayments',
                'todayBookings',
                'inStudio',
                'nextQueue',
                'packages',
                'todaySchedules'
            ))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function verifyPayment(Request $request, Booking $booking): RedirectResponse
    {
        $decision = $request->input('decision'); // accept | reject
        $rejectionNote = $request->input('rejection_note');
        $cashierId = Auth::id() ?? 2;

        DB::transaction(function () use ($booking, $decision, $cashierId, $rejectionNote) {
            $isAccept = $decision === 'accept';
            $paymentStatus = $isAccept ? 'verified' : 'rejected';
            $bookingStatus = $isAccept ? 'confirmed' : 'cancelled';

            $booking->payment()->updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'payment_status' => $paymentStatus,
                    'verified_by' => $cashierId,
                    'verified_at' => Carbon::now(),
                    'rejection_note' => $isAccept ? null : ($rejectionNote ?: 'Bukti pembayaran tidak terbaca atau nominal tidak sesuai.'),
                ]
            );

            $booking->update([
                'status' => $bookingStatus,
            ]);
        });

        if ($decision === 'accept') {
            $this->waService->sendPaymentVerified($booking);

            return back()->with('success', "Pembayaran nomor antrean {$booking->queue_number} ({$booking->user->name}) BERHASIL DITERIMA (Lunas). Notifikasi WhatsApp telah disiapkan.");
        }

        return back()->with('success', "Pembayaran antrean {$booking->queue_number} telah DITOLAK.");
    }

    public function checkIn(Booking $booking): RedirectResponse
    {
        if (! in_array($booking->status, ['confirmed', 'waiting_verification'])) {
            return back()->with('error', 'Status antrean ini belum memenuhi syarat untuk Check-in.');
        }

        $booking->update([
            'status' => 'checked_in',
            'checked_in_at' => Carbon::now(),
        ]);

        // Send Queue Near alert if ahead count is low
        $this->waService->sendQueueNearNotification($booking, 1);

        return back()->with('success', "Pelanggan nomor {$booking->queue_number} ({$booking->user->name}) BERHASIL CHECK-IN! Status kini siap dipanggil masuk ke studio.");
    }

    public function callNext(): RedirectResponse
    {
        $today = Carbon::today()->toDateString();

        return DB::transaction(function () use ($today) {
            // Selesaikan sesi saat ini jika ada yang in_studio
            $current = Booking::whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
                ->where('status', 'in_studio')
                ->first();

            if ($current) {
                $current->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                ]);
            }

            // Panggil antrean berikutnya (prioritaskan yang sudah check-in)
            $next = Booking::whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
                ->whereIn('status', ['checked_in', 'confirmed'])
                ->orderByRaw("CASE WHEN status = 'checked_in' THEN 1 ELSE 2 END")
                ->orderBy('id', 'asc')
                ->first();

            if ($next) {
                $next->update([
                    'status' => 'in_studio',
                    'called_at' => Carbon::now(),
                ]);

                // Kirim notifikasi WA bahwa giliran telah tiba
                $this->waService->sendQueueCalledNotification($next);

                return back()->with('success', "Memanggil antrean {$next->queue_number} ({$next->user->name}) ke Photobox Studio.");
            }

            return back()->with('info', 'Semua antrean hari ini telah selesai atau belum ada pelanggan berikutnya yang Check-in.');
        });
    }

    public function callSpecific(Booking $booking): JsonResponse|RedirectResponse
    {
        $today = Carbon::today()->toDateString();

        return DB::transaction(function () use ($booking, $today) {
            // Selesaikan yang sedang in_studio/calling di booth yang sama (jika ada yang berbeda)
            $currentQuery = Booking::whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
                ->whereIn('status', ['calling', 'in_studio']);

            if ($booking->studio_backdrop) {
                $currentQuery->where('studio_backdrop', $booking->studio_backdrop);
            }

            $current = $currentQuery->first();

            if ($current && $current->id !== $booking->id) {
                $current->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                ]);
            }

            // Pelanggan langsung dimasukkan ke dalam bilik foto (mulai 15 menit)
            $booking->update([
                'status' => 'in_studio',
                'called_at' => Carbon::now(),
                'checked_in_at' => $booking->checked_in_at ?: Carbon::now(),
            ]);

            $this->waService->sendQueueCalledNotification($booking);

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'booking_id' => $booking->id,
                    'status' => $booking->status,
                    'queue_number' => $booking->queue_number,
                    'studio_backdrop' => $booking->studio_backdrop,
                ]);
            }

            return back()->with('success', "Nomor antrean {$booking->queue_number} ({$booking->user->name}) telah MASUK ke {$booking->studio_backdrop}. Sesi 15 menit dimulai.");
        });
    }

    public function completeSession(Booking $booking): JsonResponse|RedirectResponse
    {
        $cashierId = Auth::id();
        $now = Carbon::now();
        $today = Carbon::today()->toDateString();

        return DB::transaction(function () use ($booking, $cashierId, $now, $today) {
            $booking->update([
                'status' => 'completed',
                'completed_at' => $now,
            ]);

            if ($booking->payment) {
                $booking->payment->update([
                    'payment_status' => 'verified',
                    'verified_by' => $cashierId,
                    'verified_at' => $now,
                ]);
            } else {
                Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $booking->total_price,
                    'payment_method' => 'cash',
                    'proof_image_path' => 'pay_on_site',
                    'payment_status' => 'verified',
                    'verified_by' => $cashierId,
                    'verified_at' => $now,
                ]);
            }

            // Otomatis panggil antrean berikutnya dengan status 'calling' (konfirmasi 30 detik untuk kasir)
            $next = Booking::with('user')
                ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
                ->where('studio_backdrop', $booking->studio_backdrop)
                ->whereIn('status', ['waiting_verification', 'confirmed', 'checked_in'])
                ->orderBy('id', 'asc')
                ->first();

            if ($next) {
                $next->update([
                    'status' => 'calling',
                    'called_at' => $now,
                    'checked_in_at' => $next->checked_in_at ?: $now,
                ]);

                try {
                    $this->waService->sendQueueCalledNotification($next);
                } catch (\Throwable $e) {
                }

                if (request()->expectsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => true,
                        'booking_id' => $booking->id,
                        'status' => 'completed',
                        'queue_number' => $booking->queue_number,
                        'studio_backdrop' => $booking->studio_backdrop,
                        'next_id' => $next->id,
                        'next_status' => 'calling',
                        'next_queue_number' => $next->queue_number,
                    ]);
                }

                return back()->with('success', "Sesi {$booking->queue_number} selesai. Antrean berikutnya {$next->queue_number} dipanggil (Konfirmasi 30 detik).");
            }

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'booking_id' => $booking->id,
                    'status' => 'completed',
                    'queue_number' => $booking->queue_number,
                    'studio_backdrop' => $booking->studio_backdrop,
                    'next_id' => null,
                ]);
            }

            return back()->with('success', "Sesi photobox nomor antrean {$booking->queue_number} di {$booking->studio_backdrop} telah SELESAI.");
        });
    }

    public function markNoShow(Booking $booking): JsonResponse|RedirectResponse
    {
        $now = Carbon::now();
        $today = Carbon::today()->toDateString();
        $wasActive = in_array($booking->status, ['calling', 'in_studio']);
        $backdrop = $booking->studio_backdrop;

        $booking->update([
            'status' => 'no_show',
        ]);

        // Jika antrean yang ditandai tidak hadir sedang dipanggil/aktif, otomatis panggil antrean berikutnya (konfirmasi 30s)
        if ($wasActive && $backdrop) {
            $next = Booking::with('user')
                ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
                ->where('studio_backdrop', $backdrop)
                ->whereIn('status', ['waiting_verification', 'confirmed', 'checked_in'])
                ->orderBy('id', 'asc')
                ->first();

            if ($next) {
                $next->update([
                    'status' => 'calling',
                    'called_at' => $now,
                    'checked_in_at' => $next->checked_in_at ?: $now,
                ]);

                try {
                    $this->waService->sendQueueCalledNotification($next);
                } catch (\Throwable $e) {
                }

                if (request()->expectsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => true,
                        'booking_id' => $booking->id,
                        'status' => 'no_show',
                        'queue_number' => $booking->queue_number,
                        'studio_backdrop' => $backdrop,
                        'next_id' => $next->id,
                        'next_status' => 'calling',
                        'next_queue_number' => $next->queue_number,
                    ]);
                }

                return back()->with('info', "Antrean {$booking->queue_number} ditandai Tidak Hadir. Memanggil antrean berikutnya {$next->queue_number} (Konfirmasi 30 detik).");
            }
        }

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'booking_id' => $booking->id,
                'status' => 'no_show',
                'queue_number' => $booking->queue_number,
                'studio_backdrop' => $backdrop,
                'next_id' => null,
            ]);
        }

        return back()->with('info', "Nomor antrean {$booking->queue_number} ({$booking->user->name}) telah ditandai TIDAK HADIR.");
    }

    /**
     * Store Walk-in Booking directly from Cashier desk (FR-04, FR-08).
     */
    public function storeWalkIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:25'],
            'package_id' => ['required', 'exists:packages,id'],
            'schedule_id' => ['required', 'exists:schedules,id'],
            'studio_backdrop' => ['nullable', 'string', 'max:80'],
            'payment_method' => ['required', 'in:cash,qris_static'],
        ]);

        $cashierId = Auth::id() ?? 2;

        return DB::transaction(function () use ($validated, $cashierId) {
            // Find or create customer
            $customer = User::firstOrCreate(
                ['phone_whatsapp' => $validated['customer_phone']],
                [
                    'name' => $validated['customer_name'],
                    'email' => 'walkin_'.time().'_'.rand(100, 999).'@kothakphoto.id',
                    'password' => Hash::make('walkin123'),
                    'role' => 'customer',
                    'is_active' => true,
                ]
            );

            $package = Package::findOrFail($validated['package_id']);
            $schedule = Schedule::lockForUpdate()->findOrFail($validated['schedule_id']);

            $backdrop = $validated['studio_backdrop'] ?? 'Flower Garden Booth';
            $prefix = match ($backdrop) {
                'Flower Garden Booth' => 'F',
                'Lift Booth' => 'L',
                'Vintage Booth' => 'V',
                default => 'KTH',
            };

            // Queue number per booth
            $dailyBoothCount = Booking::where('studio_backdrop', $backdrop)
                ->whereHas('schedule', fn ($q) => $q->whereDate('date', $schedule->date))
                ->count();
            $queueNumber = sprintf('%s-%03d', $prefix, $dailyBoothCount + 1);

            $booking = Booking::create([
                'user_id' => $customer->id,
                'package_id' => $package->id,
                'schedule_id' => $schedule->id,
                'booking_date' => Carbon::now(),
                'booking_type' => 'walk_in',
                'participants_count' => $package->max_participants,
                'status' => 'checked_in', // Walk-in is already on site!
                'queue_number' => $queueNumber,
                'studio_backdrop' => $validated['studio_backdrop'] ?? 'Flower Garden Booth',
                'package_price' => $package->price,
                'add_ons_total' => 0,
                'total_price' => $package->price,
                'checked_in_at' => Carbon::now(),
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $package->price,
                'payment_method' => $validated['payment_method'],
                'proof_image_path' => 'uploads/payments/demo_qris_sample.png',
                'payment_status' => 'verified',
                'verified_by' => $cashierId,
                'verified_at' => Carbon::now(),
            ]);

            return back()->with('success', "Pelanggan Walk-In {$customer->name} berhasil didaftarkan dengan nomor antrean {$queueNumber}! Status: Siap Masuk Antrean.");
        });
    }
}
