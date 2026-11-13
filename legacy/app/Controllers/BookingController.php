<?php
// File: app/Controllers/BookingController.php
// Tugas 2.3: Logika Backend PHP untuk menyimpan data form booking ke tabel bookings dengan status 'Menunggu Pembayaran'

declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/database.php';

class BookingController {
    public function createBooking(int $userId, int $packageId, int $scheduleId, string $studioBackdrop = 'Electric Blue Backdrop'): array {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            // 1. Kunci baris jadwal (FOR UPDATE) untuk mencegah double-booking saat klik bersamaan
            $stmtSchedule = $pdo->prepare("
                SELECT id, date, start_time, end_time, capacity
                FROM schedules
                WHERE id = :schedule_id
                FOR UPDATE
            ");
            $stmtSchedule->execute(['schedule_id' => $scheduleId]);
            $schedule = $stmtSchedule->fetch();

            if (!$schedule) {
                throw new RuntimeException('Jadwal slot tidak ditemukan.');
            }

            // 2. Hitung booking aktif pada schedule_id tersebut
            $stmtCount = $pdo->prepare("
                SELECT COUNT(*)
                FROM bookings
                WHERE schedule_id = :schedule_id
                  AND status NOT IN ('cancelled', 'Ditolak')
            ");
            $stmtCount->execute(['schedule_id' => $scheduleId]);
            $currentBooked = (int) $stmtCount->fetchColumn();

            if ($currentBooked >= (int) $schedule['capacity']) {
                throw new RuntimeException('Kuota pada slot jadwal ini sudah penuh.');
            }

            // 3. Buat nomor antrean otomatis (Format: KTH-001)
            $stmtSeq = $pdo->prepare("
                SELECT COUNT(*) FROM bookings b
                INNER JOIN schedules s ON b.schedule_id = s.id
                WHERE s.date = :slot_date
            ");
            $stmtSeq->execute(['slot_date' => $schedule['date']]);
            $queueNumber = sprintf('KTH-%03d', (int) $stmtSeq->fetchColumn() + 1);

            // 4. Insert ke tabel bookings dengan status 'Menunggu Pembayaran'
            $stmtInsert = $pdo->prepare("
                INSERT INTO bookings (
                    user_id,
                    package_id,
                    schedule_id,
                    booking_date,
                    status,
                    queue_number,
                    studio_backdrop
                ) VALUES (
                    :user_id,
                    :package_id,
                    :schedule_id,
                    NOW(3),
                    'Menunggu Pembayaran',
                    :queue_number,
                    :studio_backdrop
                )
            ");
            $stmtInsert->execute([
                'user_id'         => $userId,
                'package_id'      => $packageId,
                'schedule_id'     => $scheduleId,
                'queue_number'    => $queueNumber,
                'studio_backdrop' => $studioBackdrop,
            ]);

            $bookingId = (int) $pdo->lastInsertId();
            $pdo->commit();

            return [
                'success'      => true,
                'booking_id'   => $bookingId,
                'queue_number' => $queueNumber,
                'status'       => 'Menunggu Pembayaran',
            ];
        } catch (Throwable $e) {
            $pdo->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
