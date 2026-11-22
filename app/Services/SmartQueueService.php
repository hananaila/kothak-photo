<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SmartQueueService
{
    public function __construct(
        protected WhatsAppNotificationService $waService
    ) {}

    /**
     * Otomatis memajukan antrean pintar:
     * 1. Status 'calling' (Konfirmasi Kehadiran 30 detik untuk kasir):
     *    Jika sudah 30 detik dan kasir tidak klik 'Tidak Hadir', otomatis masuk ke 'in_studio' (mulai 15 menit).
     * 2. Status 'in_studio' (Sesi Foto 15 menit):
     *    Jika durasi 15 menit sudah habis, sesi selesai ('completed') dan antrean berikutnya dipanggil ('calling' 30 detik).
     *
     * @return array Ringkasan pembaruan bilik
     */
    public function autoAdvanceExpiredSessions(): array
    {
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();
        $advanced = [];

        // --- TAHAP 1: KONFIRMASI 30 DETIK HABIS -> AUTO MASUK KE 'IN_STUDIO' (MULAI 15 MENIT) ---
        $callingSessions = Booking::with(['package', 'schedule', 'user'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
            ->where('status', 'calling')
            ->get();

        foreach ($callingSessions as $calling) {
            $callTime = $calling->called_at ?: $calling->updated_at;
            if ($callTime && $callTime->copy()->addSeconds(30)->lessThanOrEqualTo($now)) {
                // Konfirmasi 30 detik telah berlalu tanpa ditandai 'Tidak Hadir' -> Auto Masuk Bilik
                $calling->update([
                    'status' => 'in_studio',
                    'called_at' => $now, // Reset waktu masuk ke saat ini agar durasi 15 menit dihitung murni dari 15:00
                ]);

                $advanced[] = [
                    'booth' => $calling->studio_backdrop,
                    'action' => 'auto_enter_studio',
                    'booking' => $calling->queue_number,
                ];
            }
        }

        // --- TAHAP 2: SESI 15 MENIT HABIS -> AUTO SELESAI & PANGGIL BERIKUTNYA KE 'CALLING' (30 DETIK) ---
        $activeSessions = Booking::with(['package', 'schedule', 'user'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
            ->where('status', 'in_studio')
            ->get();

        foreach ($activeSessions as $current) {
            $durationMinutes = $current->package?->duration ?: 15;
            $startTime = $current->called_at ?: $current->updated_at;

            if (! $startTime) {
                continue;
            }

            // Hitung apakah sudah mencapai atau melebihi batas waktu sesi (15 menit)
            $endTime = $startTime->copy()->addMinutes($durationMinutes);

            if ($now->greaterThanOrEqualTo($endTime)) {
                DB::transaction(function () use ($current, $today, $now, &$advanced) {
                    // 1. Selesaikan sesi foto saat ini
                    $current->update([
                        'status' => 'completed',
                        'completed_at' => $now,
                    ]);

                    // Otomatis verifikasi pembayaran jika ada
                    if ($current->payment && $current->payment->payment_status !== 'verified') {
                        $current->payment->update([
                            'payment_status' => 'verified',
                            'verified_at' => $now,
                        ]);
                    }

                    // 2. Ambil antrean berikutnya yang menunggu di bilik yang sama
                    $next = Booking::with('user')
                        ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
                        ->where('studio_backdrop', $current->studio_backdrop)
                        ->whereIn('status', ['waiting_verification', 'confirmed', 'checked_in'])
                        ->orderBy('id', 'asc')
                        ->first();

                    if ($next) {
                        // Masukkan ke tahap 'calling' untuk memberi waktu konfirmasi 30 detik bagi kasir
                        $next->update([
                            'status' => 'calling',
                            'called_at' => $now,
                            'checked_in_at' => $next->checked_in_at ?: $now,
                        ]);

                        // Kirim notifikasi WhatsApp / Push bahwa giliran telah tiba
                        try {
                            $this->waService->sendQueueCalledNotification($next);
                        } catch (\Throwable $e) {
                            Log::warning("Gagal mengirim WA auto-advance untuk booking #{$next->id}: {$e->getMessage()}");
                        }

                        $advanced[] = [
                            'booth' => $current->studio_backdrop,
                            'completed' => $current->queue_number,
                            'called_next' => $next->queue_number,
                        ];
                    } else {
                        $advanced[] = [
                            'booth' => $current->studio_backdrop,
                            'completed' => $current->queue_number,
                            'called_next' => null,
                        ];
                    }
                });
            }
        }

        return $advanced;
    }
}
