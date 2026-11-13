<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function checkout(Booking $booking): View
    {
        $booking->load(['package', 'schedule', 'user', 'payment']);

        return view('customer.checkout_payment', compact('booking'));
    }

    public function uploadProof(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate([
            'proof_image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // Max 5MB
        ]);

        $file = $request->file('proof_image');
        $fileName = sprintf('qris_booking_%d_%d.%s', $booking->id, time(), $file->getClientOriginalExtension());
        $targetPath = 'uploads/payments/' . $fileName;

        // Save file directly to public directory for easy preview
        $file->move(public_path('uploads/payments'), $fileName);

        DB::transaction(function () use ($booking, $targetPath) {
            Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'proof_image_path' => $targetPath,
                    'payment_status' => 'pending',
                    'created_at' => Carbon::now(),
                ]
            );

            $booking->update([
                'status' => 'waiting_verification',
            ]);
        });

        return redirect()->route('booking.history')
            ->with('success', 'Bukti pembayaran berhasil diunggah! Status: Menunggu Verifikasi Kasir.');
    }
}

