<?php
// File: views/customer/transaction_history.php
// Tugas 3.4: Halaman Riwayat Transaksi Pelanggan

declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getConnection();
$userId = (int) ($_SESSION['user']['id'] ?? 1);

$stmtHistory = $pdo->prepare("
    SELECT 
        b.id AS booking_id,
        b.queue_number,
        b.booking_date,
        b.status AS booking_status,
        b.studio_backdrop,
        pkg.name AS package_name,
        pkg.price AS total_price,
        sch.date AS schedule_date,
        sch.start_time,
        sch.end_time,
        COALESCE(pay.payment_status, 'Belum Unggah Bukti') AS payment_status,
        pay.proof_image_path
    FROM bookings b
    INNER JOIN packages pkg ON b.package_id = pkg.id
    INNER JOIN schedules sch ON b.schedule_id = sch.id
    LEFT JOIN payments pay ON pay.booking_id = b.id
    WHERE b.user_id = :user_id
    ORDER BY b.booking_date DESC
");
$stmtHistory->execute(['user_id' => $userId]);
$transactions = $stmtHistory->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Riwayat Transaksi Saya — KOTHaK Cafe &amp; Photo</title>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
</head>
<body class="bg-[#F5F4EE] text-slate-900 font-sans min-h-screen p-6">
  <div class="max-w-6xl mx-auto">
    <h1 class="text-2xl font-extrabold mb-4">Riwayat Transaksi &amp; Status Booking Saya</h1>
    <div class="border-2 border-slate-900 bg-white shadow-[6px_6px_0px_#1D4ED8] overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-blue-700 text-white border-b-2 border-slate-900 uppercase font-extrabold">
            <th class="py-3 px-4">No. Antrean</th>
            <th class="py-3 px-4">Paket Foto</th>
            <th class="py-3 px-4">Tanggal &amp; Jam</th>
            <th class="py-3 px-4">Total Harga</th>
            <th class="py-3 px-4">Bukti Transfer</th>
            <th class="py-3 px-4">Status Pembayaran</th>
            <th class="py-3 px-4">Status Booking</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-slate-900">
          <?php foreach ($transactions as $trx): ?>
            <tr>
              <td class="py-3 px-4 font-mono font-black text-blue-700"><?= htmlspecialchars($trx['queue_number']) ?></td>
              <td class="py-3 px-4 font-bold"><?= htmlspecialchars($trx['package_name']) ?></td>
              <td class="py-3 px-4 font-mono">
                <?= htmlspecialchars($trx['schedule_date']) ?> &bull; <?= substr($trx['start_time'], 0, 5) ?>-<?= substr($trx['end_time'], 0, 5) ?>
              </td>
              <td class="py-3 px-4 font-mono font-bold">Rp <?= number_format((float) $trx['total_price'], 0, ',', '.') ?></td>
              <td class="py-3 px-4 font-mono text-[11px]"><?= htmlspecialchars($trx['proof_image_path'] ?? '-') ?></td>
              <td class="py-3 px-4 font-mono font-bold"><?= htmlspecialchars($trx['payment_status']) ?></td>
              <td class="py-3 px-4 font-bold uppercase"><?= htmlspecialchars($trx['booking_status']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
