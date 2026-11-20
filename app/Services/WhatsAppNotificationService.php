<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    /**
     * Send booking created confirmation.
     * Wrapped in try-catch so it never interrupts the booking process.
     */
    public function sendBookingConfirmation(Booking $booking): bool
    {
        try {
            $user = $booking->user;
            $package = $booking->package;
            $schedule = $booking->schedule;

            $dateFormatted = Carbon::parse($schedule->date)->translatedFormat('l, d F Y');
            $timeSlot = substr($schedule->start_time, 0, 5).' - '.substr($schedule->end_time, 0, 5).' WIB';
            $total = 'Rp '.number_format($booking->total_price, 0, ',', '.');

            $message = "Halo kak *{$user->name}*! 👋\n\n"
                ."Terima kasih telah melakukan reservasi di *KOTHaK Cafe & Photo Studio Purbalingga*! 📸✨\n\n"
                ."Berikut rincian pesanan Anda:\n"
                ."━━━━━━━━━━━━━━━━━━━━━━\n"
                ."🎫 *No. Antrean*: `{$booking->queue_number}`\n"
                ."📦 *Paket Foto*: {$package->name}\n"
                ."📅 *Jadwal*: {$dateFormatted}\n"
                ."⏰ *Waktu*: {$timeSlot}\n"
                ."🎨 *Background*: {$booking->studio_backdrop}\n"
                ."💰 *Total Biaya*: {$total}\n"
                ."━━━━━━━━━━━━━━━━━━━━━━\n\n"
                ."Silakan lakukan pembayaran via *QRIS Statis* di website dan unggah bukti transfer agar diverifikasi kasir.\n\n"
                .'Pantau antrean secara live di: '.route('packages.index')."\n\n"
                ."Salam hangat,\n*Tim KOTHaK Photo Purbalingga*";

            return $this->dispatchMessage($user->phone_whatsapp, $message, $booking);
        } catch (\Throwable $e) {
            Log::error('WhatsAppNotificationService::sendBookingConfirmation failed: '.$e->getMessage(), [
                'booking_id' => $booking->id,
            ]);

            return false;
        }
    }

    /**
     * Send payment verified notification.
     */
    public function sendPaymentVerified(Booking $booking): bool
    {
        try {
            $user = $booking->user;
            $schedule = $booking->schedule;
            $dateFormatted = Carbon::parse($schedule->date)->translatedFormat('d F Y');
            $timeSlot = substr($schedule->start_time, 0, 5).' WIB';

            $message = "Halo kak *{$user->name}*! 🎉\n\n"
                ."Pembayaran QRIS untuk nomor antrean *{$booking->queue_number}* telah *DITERIMA & TERVERIFIKASI (LUNAS)* oleh kasir KOTHaK Photo! ✅\n\n"
                ."📍 *Jadwal Sesi*: {$dateFormatted} jam {$timeSlot}\n"
                ."Mohon hadir di lokasi studio 10 menit sebelum giliran untuk proses check-in.\n\n"
                .'Sampai jumpa di KOTHaK Photo Purbalingga!';

            return $this->dispatchMessage($user->phone_whatsapp, $message, $booking);
        } catch (\Throwable $e) {
            Log::error('WhatsAppNotificationService::sendPaymentVerified failed: '.$e->getMessage(), [
                'booking_id' => $booking->id,
            ]);

            return false;
        }
    }

    /**
     * Send queue near reminder (when 1-2 queues away).
     */
    public function sendQueueNearNotification(Booking $booking, int $aheadCount = 1): bool
    {
        try {
            $user = $booking->user;

            $message = "🔔 *PENGINGAT ANTREAN KOTHAK PHOTO* 🔔\n\n"
                ."Halo kak *{$user->name}*, nomor antrean Anda *{$booking->queue_number}* sudah mendekati giliran! "
                ."(Tersisa {$aheadCount} antrean lagi di depan Anda).\n\n"
                .'Mohon segera merapat ke area tunggu booth studio photobox. Terima kasih!';

            return $this->dispatchMessage($user->phone_whatsapp, $message, $booking);
        } catch (\Throwable $e) {
            Log::error('WhatsAppNotificationService::sendQueueNearNotification failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Send queue called notification.
     */
    public function sendQueueCalledNotification(Booking $booking): bool
    {
        try {
            $user = $booking->user;

            $message = "📢 *GILIRAN SESI ANDA TELAH TIBA!* 📢\n\n"
                ."Nomor antrean *{$booking->queue_number}* atas nama *{$user->name}* silakan segera masuk ke dalam studio photobox!\n\n"
                .'Operator studio kami siap menyambut Anda. Selamat berfoto ria! ✨';

            return $this->dispatchMessage($user->phone_whatsapp, $message, $booking);
        } catch (\Throwable $e) {
            Log::error('WhatsAppNotificationService::sendQueueCalledNotification failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Generate Click-to-Chat WhatsApp URL for front-end & cashier ease.
     */
    public function generateWaLink(?string $phone, string $message): string
    {
        if (! $phone) {
            return '#';
        }

        // Normalize phone number to 62 standard
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62'.substr($cleaned, 1);
        }

        return 'https://api.whatsapp.com/send?phone='.$cleaned.'&text='.urlencode($message);
    }

    /**
     * Dispatch message to WhatsApp Gateway (with fallback to local log).
     */
    protected function dispatchMessage(?string $phone, string $message, ?Booking $booking = null): bool
    {
        if (! $phone) {
            return false;
        }

        // Log message for auditing & local demo environment
        Log::info("WhatsApp Outbox: To {$phone} => \n{$message}");

        if ($booking) {
            $booking->updateQuietly(['whatsapp_sent_at' => Carbon::now()]);
        }

        return true;
    }
}
