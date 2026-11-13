<?php
// File: app/Controllers/PaymentController.php
// Tugas 3.3: Menyimpan file gambar bukti transfer ke public/uploads/payments/ dan menyimpan ke tabel payments ('Menunggu Verifikasi')

declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/database.php';

class PaymentController {
    public function uploadPaymentProof(int $bookingId, array $file): array {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Gagal mengunggah file bukti transfer.'];
        }

        $allowedMimeTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!isset($allowedMimeTypes[$mimeType])) {
            return ['success' => false, 'message' => 'Format file harus berupa gambar JPG, PNG, atau WEBP.'];
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'message' => 'Ukuran file bukti transfer maksimal 5 MB.'];
        }

        $uploadDir = __DIR__ . '/../../public/uploads/payments/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = $allowedMimeTypes[$mimeType];
        $fileName  = sprintf('qris_booking_%d_%d.%s', $bookingId, time(), $extension);
        $targetAbsolutePath = $uploadDir . $fileName;
        $dbRelativePath     = 'public/uploads/payments/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $targetAbsolutePath)) {
            return ['success' => false, 'message' => 'Gagal menyimpan file ke folder public/uploads/payments/.'];
        }

        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $stmtPayment = $pdo->prepare("
                INSERT INTO payments (
                    booking_id,
                    proof_image_path,
                    payment_status,
                    created_at
                ) VALUES (
                    :booking_id,
                    :proof_image_path,
                    'Menunggu Verifikasi',
                    NOW(3)
                )
                ON DUPLICATE KEY UPDATE
                    proof_image_path = VALUES(proof_image_path),
                    payment_status   = 'Menunggu Verifikasi',
                    created_at       = NOW(3)
            ");
            $stmtPayment->execute([
                'booking_id'       => $bookingId,
                'proof_image_path' => $dbRelativePath,
            ]);

            $stmtBooking = $pdo->prepare("
                UPDATE bookings
                SET status = 'waiting_verification'
                WHERE id = :booking_id
            ");
            $stmtBooking->execute(['booking_id' => $bookingId]);

            $pdo->commit();

            return [
                'success'          => true,
                'booking_id'       => $bookingId,
                'proof_image_path' => $dbRelativePath,
                'payment_status'   => 'Menunggu Verifikasi',
                'redirect_url'     => '/customer/transaction_history.php',
            ];
        } catch (Throwable $e) {
            $pdo->rollBack();
            return ['success' => false, 'message' => 'Kesalahan database: ' . $e->getMessage()];
        }
    }
}
