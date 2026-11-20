<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\User;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CashierController extends Controller
{
    public function __construct(
        protected WhatsAppNotificationService $waService
    ) {}

    public function dashboard(): View
    {
        $today = Carbon::today()->toDateString();

        // 1. Pending payments needing verification
        $pendingPayments = Payment::with(['booking.user', 'booking.package', 'booking.schedule', 'booking.bookingAddOns.addOn'])
            ->where('payment_status', 'pending')
            ->orderBy('id', 'desc')
            ->get();

        // All recent payments for history
        $allPayments = Payment::with(['booking.user', 'booking.package', 'booking.schedule'])
            ->orderBy('id', 'desc')
            ->take(15)
            ->get();

        // 2. Today's bookings for check-in & studio queue
        $todayBookings = Booking::with(['user', 'package', 'schedule', 'payment'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
            ->orderBy('schedule_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // 3. Current active session in studio
        $inStudio = Booking::with(['user', 'package', 'schedule'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
            ->where('status', 'in_studio')
            ->first();

        // 4. Next in line
        $nextQueue = Booking::with(['user', 'package', 'schedule'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
            ->whereIn('status', ['checked_in', 'confirmed'])
            ->orderBy('id', 'asc')
            ->first();

        // Data for Walk-in Booking Modal (FR-04, FR-08)
        $packages = Package::where('is_active', true)->get();
        $todaySchedules = Schedule::whereDate('date', $today)->where('is_active', true)->orderBy('start_time')->get();

        return view('cashier.dashboard', compact(
            'pendingPayments',
            'allPayments',
            'todayBookings',
            'inStudio',
            'nextQueue',
            'today',
            'packages',
            'todaySchedules'
        ));
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

                return back()->with('success', "📢 MEMANGGIL ANTREAN {$next->queue_number} ({$next->user->name}) ke Photobox Studio!");
            }

            return back()->with('info', 'Semua antrean hari ini telah selesai atau belum ada pelanggan berikutnya yang Check-in.');
        });
    }

    public function callSpecific(Booking $booking): RedirectResponse
    {
        $today = Carbon::today()->toDateString();

        return DB::transaction(function () use ($booking, $today) {
            // Selesaikan yang sedang in_studio
            $current = Booking::whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
                ->where('status', 'in_studio')
                ->first();

            if ($current && $current->id !== $booking->id) {
                $current->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                ]);
            }

            $booking->update([
                'status' => 'in_studio',
                'called_at' => Carbon::now(),
                'checked_in_at' => $booking->checked_in_at ?: Carbon::now(),
            ]);

            $this->waService->sendQueueCalledNotification($booking);

            return back()->with('success', "📢 MEMANGGIL ANTREAN {$booking->queue_number} ({$booking->user->name}) MASUK KE STUDIO!");
        });
    }

    public function completeSession(Booking $booking): RedirectResponse
    {
        $booking->update([
            'status' => 'completed',
            'completed_at' => Carbon::now(),
        ]);

        return back()->with('success', "Sesi photobox nomor antrean {$booking->queue_number} telah SELESAI.");
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

            // Queue number
            $dailyBookingsCount = Booking::whereHas('schedule', fn ($q) => $q->whereDate('date', $schedule->date))->count();
            $queueNumber = sprintf('KTH-%03d', $dailyBookingsCount + 1);

            $booking = Booking::create([
                'user_id' => $customer->id,
                'package_id' => $package->id,
                'schedule_id' => $schedule->id,
                'booking_date' => Carbon::now(),
                'booking_type' => 'walk_in',
                'participants_count' => $package->max_participants,
                'status' => 'checked_in', // Walk-in is already on site!
                'queue_number' => $queueNumber,
                'studio_backdrop' => $validated['studio_backdrop'] ?? 'Electric Blue Backdrop',
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
