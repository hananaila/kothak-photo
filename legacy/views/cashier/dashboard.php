<?php
// File: views/cashier/dashboard.php
// Tugas 4.1, 4.2, & 4.3: Layout Dasbor Kasir Minimalis (Verifikasi Pembayaran & Manajemen Kedatangan / Check-in)

declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getConnection();
$today = date('Y-m-d');

$stmtPayments = $pdo->query("
    SELECT 
        b.id AS booking_id,
        b.queue_number,
        b.status AS booking_status,
        u.name AS customer_name,
        u.phone_whatsapp,
        pkg.name AS package_name,
        pkg.price AS total_price,
        sch.date AS schedule_date,
        sch.start_time,
        sch.end_time,
        pay.proof_image_path,
        pay.payment_status
    FROM payments pay
    INNER JOIN bookings b ON pay.booking_id = b.id
    INNER JOIN users u ON b.user_id = u.id
    INNER JOIN packages pkg ON b.package_id = pkg.id
    INNER JOIN schedules sch ON b.schedule_id = sch.id
    ORDER BY pay.created_at DESC
");
$paymentList = $stmtPayments->fetchAll();

$stmtToday = $pdo->prepare("
    SELECT 
        b.id AS booking_id,
        b.queue_number,
        b.status AS booking_status,
        b.checked_in_at,
        u.name AS customer_name,
        pkg.name AS package_name,
        sch.start_time,
        sch.end_time,
        COALESCE(pay.payment_status, 'Belum Bayar') AS payment_status
    FROM bookings b
    INNER JOIN users u ON b.user_id = u.id
    INNER JOIN packages pkg ON b.package_id = pkg.id
    INNER JOIN schedules sch ON b.schedule_id = sch.id
    LEFT JOIN payments pay ON pay.booking_id = b.id
    WHERE sch.date = :today
    ORDER BY sch.start_time ASC, b.queue_number ASC
");
$stmtToday->execute(['today' => $today]);
$todayBookings = $stmtToday->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dasbor Kasir — KOTHaK Cafe &amp; Photo</title>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
</head>
<body class="bg-[#F5F4EE] text-slate-900 font-sans min-h-screen p-6">
  <div class="max-w-7xl mx-auto space-y-8">
    <header class="border-b-2 border-slate-900 pb-4 flex items-center justify-between">
      <div>
        <span class="text-xs font-extrabold uppercase tracking-widest text-blue-700">CASHIER MODULE &bull; KOTHAK PHOTO</span>
        <h1 class="text-3xl font-extrabold mt-1">Dasbor Verifikasi &amp; Check-in Antrean</h1>
      </div>
    </header>

    <section class="border-2 border-slate-900 bg-white shadow-[6px_6px_0px_#1D4ED8]">
      <div class="bg-slate-900 text-white px-5 py-3 font-extrabold uppercase text-xs tracking-wider">
        1. Verifikasi Pembayaran Bukti Transfer QRIS
      </div>
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-[#F5F4EE] border-b-2 border-slate-900 uppercase font-extrabold">
            <th class="p-3">Antrean</th>
            <th class="p-3">Pelanggan</th>
            <th class="p-3">Bukti Transfer</th>
            <th class="p-3">Status</th>
            <th class="p-3 text-right">Keputusan Kasir</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-slate-900">
          <?php foreach ($paymentList as $row): ?>
            <tr>
              <td class="p-3 font-mono font-black text-blue-700"><?= htmlspecialchars($row['queue_number']) ?></td>
              <td class="p-3 font-bold"><?= htmlspecialchars($row['customer_name']) ?></td>
              <td class="p-3">
                <a href="/<?= htmlspecialchars($row['proof_image_path']) ?>" target="_blank" class="underline text-blue-700 font-mono">
                  Lihat Gambar Bukti
                </a>
              </td>
              <td class="p-3 font-mono font-bold"><?= htmlspecialchars($row['payment_status']) ?></td>
              <td class="p-3 text-right">
                <form action="/public/cashier_action.php" method="POST" class="inline-flex gap-1.5">
                  <input type="hidden" name="booking_id" value="<?= (int) $row['booking_id'] ?>" />
                  <button name="action" value="accept_payment" class="bg-emerald-500 border border-slate-900 px-3 py-1 font-extrabold uppercase">
                    Terima (Accept)
                  </button>
                  <button name="action" value="reject_payment" class="bg-rose-600 text-white border border-slate-900 px-3 py-1 font-extrabold uppercase">
                    Tolak (Reject)
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <section class="border-2 border-slate-900 bg-white shadow-[6px_6px_0px_#0F172A]">
      <div class="bg-blue-700 text-white px-5 py-3 font-extrabold uppercase text-xs tracking-wider">
        2. Manajemen Kedatangan Pelanggan Hari Ini (Check-in ke Antrean)
      </div>
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-[#F5F4EE] border-b-2 border-slate-900 uppercase font-extrabold">
            <th class="p-3">Antrean</th>
            <th class="p-3">Pelanggan</th>
            <th class="p-3">Slot Waktu</th>
            <th class="p-3">Status Booking</th>
            <th class="p-3 text-right">Aksi Kedatangan</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-slate-900">
          <?php foreach ($todayBookings as $item): ?>
            <tr>
              <td class="p-3 font-mono font-black text-blue-700"><?= htmlspecialchars($item['queue_number']) ?></td>
              <td class="p-3 font-bold"><?= htmlspecialchars($item['customer_name']) ?></td>
              <td class="p-3 font-mono"><?= substr($item['start_time'], 0, 5) ?> - <?= substr($item['end_time'], 0, 5) ?> WIB</td>
              <td class="p-3 font-bold"><?= htmlspecialchars($item['booking_status']) ?></td>
              <td class="p-3 text-right">
                <form action="/public/cashier_action.php" method="POST">
                  <input type="hidden" name="booking_id" value="<?= (int) $item['booking_id'] ?>" />
                  <button name="action" value="checkin_customer" class="bg-blue-700 text-white border border-slate-900 px-3 py-1 font-extrabold uppercase">
                    Check-in (Masuk Antrean)
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
  </div>
</body>
</html>
