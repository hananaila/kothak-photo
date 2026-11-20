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

class PaymentController extends Controller
{
    public function checkout(Booking $booking): View
    {
        $this->authorizeBookingAccess($booking);
        $booking->load(['package', 'schedule', 'user', 'payment', 'bookingAddOns.addOn']);

        return view('customer.checkout_payment', compact('booking'));
    }

    public function uploadProof(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeBookingAccess($booking);

        $request->validate([
            'proof_image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // Max 5MB
        ]);

        $file = $request->file('proof_image');
        $fileName = sprintf('qris_booking_%d_%d.%s', $booking->id, time(), $file->getClientOriginalExtension());
        $targetDirectory = public_path('uploads/payments');

        if (! file_exists($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        $file->move($targetDirectory, $fileName);
        $savedPath = 'uploads/payments/'.$fileName;

        DB::transaction(function () use ($booking, $savedPath) {
            Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'amount' => $booking->total_price,
                    'payment_method' => 'qris_static',
                    'proof_image_path' => $savedPath,
                    'payment_status' => 'pending',
                    'rejection_note' => null,
                    'created_at' => Carbon::now(),
                ]
            );

            $booking->update([
                'status' => 'waiting_verification',
            ]);
        });

        return redirect()->route('booking.history')
            ->with('success', 'Bukti transfer QRIS berhasil diunggah! Kasir KOTHaK Photo akan segera memverifikasi transaksi Anda.');
    }

    protected function authorizeBookingAccess(Booking $booking): void
    {
        $user = Auth::user();
        if ($user && ($user->isAdmin() || $user->isCashier())) {
            return;
        }

        if (! $user || $user->id !== $booking->user_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk transaksi booking ini.');
        }
    }
}
