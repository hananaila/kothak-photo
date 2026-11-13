<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CashierController extends Controller
{
    public function dashboard(): View
    {
        $today = Carbon::today()->toDateString();

        // 1. Pending / recent payments for verification
        $payments = Payment::with(['booking.user', 'booking.package', 'booking.schedule'])
            ->orderBy('id', 'desc')
            ->get();

        // 2. Today's bookings for arrival / check-in
        $todayBookings = Booking::with(['user', 'package', 'schedule', 'payment'])
            ->whereHas('schedule', fn($q) => $q->whereDate('date', $today))
            ->orderBy('schedule_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // 3. Current active session in studio
        $inStudio = Booking::with(['user', 'package', 'schedule'])
            ->whereHas('schedule', fn($q) => $q->whereDate('date', $today))
            ->where('status', 'in_studio')
            ->first();

        // 4. Next in line
        $nextQueue = Booking::with(['user', 'package', 'schedule'])
            ->whereHas('schedule', fn($q) => $q->whereDate('date', $today))
            ->whereIn('status', ['checked_in', 'confirmed'])
            ->orderBy('id', 'asc')
            ->first();

        return view('cashier.dashboard', compact('payments', 'todayBookings', 'inStudio', 'nextQueue', 'today'));
    }

    public function verifyPayment(Request $request, Booking $booking): RedirectResponse
    {
        $decision = $request->input('decision'); // accept | reject
        $cashierId = Auth::id() ?? 2;

        DB::transaction(function () use ($booking, $decision, $cashierId) {
            $isAccept = $decision === 'accept';
            $paymentStatus = $isAccept ? 'verified' : 'rejected';
            $bookingStatus = $isAccept ? 'confirmed' : 'cancelled';

            $booking->payment()->updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'payment_status' => $paymentStatus,
                    'verified_by' => $cashierId,
                    'verified_at' => Carbon::now(),
                ]
            );

            $booking->update([
                'status' => $bookingStatus,
                'whatsapp_sent_at' => $isAccept ? Carbon::now() : null,
            ]);
        });

        $msg = $decision === 'accept'
            ? "Pembayaran antrean {$booking->queue_number} berhasil DITERIMA (Lunas)."
            : "Pembayaran antrean {$booking->queue_number} DITOLAK.";

        return back()->with('success', $msg);
    }

    public function checkIn(Booking $booking): RedirectResponse
    {
        if (!in_array($booking->status, ['confirmed', 'waiting_verification'])) {
            return back()->with('error', 'Status booking tidak memenuhi syarat untuk Check-in.');
        }

        $booking->update([
            'status' => 'checked_in',
            'checked_in_at' => Carbon::now(),
        ]);

        return back()->with('success', "Pelanggan nomor {$booking->queue_number} ({$booking->user->name}) berhasil Check-in ke Antrean Studio!");
    }

    public function callNext(): RedirectResponse
    {
        $today = Carbon::today()->toDateString();

        return DB::transaction(function () use ($today) {
            // Selesaikan sesi saat ini jika ada yang in_studio
            $current = Booking::whereHas('schedule', fn($q) => $q->whereDate('date', $today))
                ->where('status', 'in_studio')
                ->first();

            if ($current) {
                $current->update(['status' => 'completed']);
            }

            // Panggil antrean berikutnya
            $next = Booking::whereHas('schedule', fn($q) => $q->whereDate('date', $today))
                ->whereIn('status', ['checked_in', 'confirmed'])
                ->orderBy('id', 'asc')
                ->first();

            if ($next) {
                $next->update(['status' => 'in_studio']);
                return back()->with('success', "Memanggil nomor antrean {$next->queue_number} ({$next->user->name}) ke Photobox Studio!");
            }

            return back()->with('info', "Semua antrean aktif hari ini telah selesai atau belum ada pelanggan yang Check-in.");
        });
    }
}
