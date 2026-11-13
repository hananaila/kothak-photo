<?php
// File: app/Controllers/CashierQueueController.php
// Tugas 4.2 & 4.3: Verifikasi Pembayaran ('Lunas/Terverifikasi') & Check-in Pelanggan ke Antrean (Queue)

declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/database.php';

class CashierQueueController {
    public function verifyPayment(int $bookingId, int $cashierId, string $decision): array {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $isAccepted    = ($decision === 'accept');
            $paymentStatus = $isAccepted ? 'Lunas/Terverifikasi' : 'Ditolak';
            $bookingStatus = $isAccepted ? 'Lunas/Terverifikasi' : 'Ditolak';

            $stmtPay = $pdo->prepare("
                UPDATE payments
                SET payment_status = :pstatus,
                    verified_by    = :cid,
                    verified_at    = NOW()
                WHERE booking_id = :bid
            ");
            $stmtPay->execute([
                'pstatus' => $paymentStatus,
                'cid'     => $cashierId,
                'bid'     => $bookingId,
            ]);

            $stmtBook = $pdo->prepare("
                UPDATE bookings
                SET status           = :bstatus,
                    whatsapp_sent_at = IF(:is_acc = 1, NOW(), NULL)
                WHERE id = :bid
            ");
            $stmtBook->execute([
                'bstatus' => $bookingStatus,
                'is_acc'  => $isAccepted ? 1 : 0,
                'bid'     => $bookingId,
            ]);

            $pdo->commit();
            return [
                'success'        => true,
                'booking_id'     => $bookingId,
                'payment_status' => $paymentStatus,
                'booking_status' => $bookingStatus,
            ];
        } catch (Throwable $e) {
            $pdo->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function checkInCustomer(int $bookingId): array {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $stmtCheck = $pdo->prepare("
                SELECT id, queue_number, status
                FROM bookings
                WHERE id = :bid
                FOR UPDATE
            ");
            $stmtCheck->execute(['bid' => $bookingId]);
            $booking = $stmtCheck->fetch();

            if (!$booking) {
                throw new RuntimeException('Data booking tidak ditemukan.');
            }

            if (!in_array($booking['status'], ['Lunas/Terverifikasi', 'confirmed'], true)) {
                throw new RuntimeException('Pelanggan hanya dapat Check-in setelah pembayaran Lunas/Terverifikasi.');
            }

            $stmtUpdate = $pdo->prepare("
                UPDATE bookings
                SET status        = 'Dalam Antrean (Queue)',
                    checked_in_at = NOW()
                WHERE id = :bid
            ");
            $stmtUpdate->execute(['bid' => $bookingId]);

            $pdo->commit();
            return [
                'success'      => true,
                'booking_id'   => $bookingId,
                'queue_number' => $booking['queue_number'],
                'status'       => 'Dalam Antrean (Queue)',
            ];
        } catch (Throwable $e) {
            $pdo->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
