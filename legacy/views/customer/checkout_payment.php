<?php
// File: views/customer/checkout_payment.php
// Tugas 3.1 & 3.2: Halaman Checkout / Pembayaran (Menampilkan Paket, Tanggal, Jam, Total Harga, QRIS Statis & Form Upload Bukti)

declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getConnection();
$bookingId = (int) ($_GET['booking_id'] ?? 503);

$stmt = $pdo->prepare("
    SELECT 
        b.id AS booking_id,
        b.queue_number,
        b.status AS booking_status,
        b.studio_backdrop,
        p.name AS package_name,
        p.duration,
        p.price AS total_price,
        s.date AS schedule_date,
        s.start_time,
        s.end_time
    FROM bookings b
    INNER JOIN packages p ON b.package_id = p.id
    INNER JOIN schedules s ON b.schedule_id = s.id
    WHERE b.id = :booking_id
    LIMIT 1
");
$stmt->execute(['booking_id' => $bookingId]);
$order = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Checkout &amp; Pembayaran QRIS — KOTHaK Cafe &amp; Photo</title>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
</head>
<body class="bg-[#F5F4EE] text-slate-900 font-sans min-h-screen p-6">
  <div class="max-w-3xl mx-auto border-2 border-slate-900 bg-white p-6 shadow-[6px_6px_0px_#1D4ED8]">
    <div class="border-b-2 border-slate-900 pb-4 mb-6 flex items-center justify-between">
      <div>
        <span class="text-xs font-extrabold uppercase tracking-widest text-blue-700">LANGKAH 3 &bull; CHECKOUT &amp; PEMBAYARAN STUDIO</span>
        <h1 class="text-2xl font-extrabold mt-1">Konfirmasi Pembayaran QRIS Statis</h1>
      </div>
      <span class="bg-blue-700 text-white font-mono font-extrabold text-sm px-3 py-1 border-2 border-slate-900">
        <?= htmlspecialchars($order['queue_number'] ?? 'KTH-014') ?>
      </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
      <div class="bg-[#F5F4EE] border-2 border-slate-900 p-4 space-y-2.5 text-xs">
        <div class="font-extrabold uppercase tracking-wider text-blue-700 border-b border-slate-300 pb-1.5">
          RINCIAN PESANAN ANDA
        </div>
        <div class="flex justify-between">
          <span class="text-slate-600">Paket Foto:</span>
          <strong class="text-slate-900"><?= htmlspecialchars($order['package_name'] ?? 'Kothak Signature Box') ?></strong>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-600">Tanggal Sesi:</span>
          <strong class="font-mono"><?= htmlspecialchars($order['schedule_date'] ?? '2026-11-13') ?></strong>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-600">Jam Sesi:</span>
          <strong class="font-mono text-blue-700">
            <?= substr($order['start_time'] ?? '15:00:00', 0, 5) ?> - <?= substr($order['end_time'] ?? '15:30:00', 0, 5) ?> WIB
          </strong>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-600">Background Studio:</span>
          <strong><?= htmlspecialchars($order['studio_backdrop'] ?? 'Electric Blue Backdrop') ?></strong>
        </div>
        <div class="border-t-2 border-slate-900 pt-2.5 flex justify-between items-baseline">
          <span class="font-extrabold uppercase">Total Harga:</span>
          <span class="text-xl font-black font-mono text-blue-700">
            Rp <?= number_format((float) ($order['total_price'] ?? 35000), 0, ',', '.') ?>
          </span>
        </div>
      </div>

      <div class="space-y-4">
        <div class="border-2 border-slate-900 p-4 text-center bg-white">
          <div class="text-xs font-extrabold uppercase tracking-wider">KOTHaK CAFE &amp; PHOTO PURBALINGGA</div>
          <div class="text-[11px] font-mono text-blue-700 mb-2">NMID: ID2026KOTHAKPBG01</div>
          <img src="/public/assets/img/qris-kothak.png" alt="Barcode QRIS Statis Kothak Photo" class="mx-auto w-44 h-44 object-contain border-2 border-slate-900 p-2" />
        </div>

        <form action="/public/process_payment.php" method="POST" enctype="multipart/form-data" class="space-y-3">
          <input type="hidden" name="booking_id" value="<?= (int) ($order['booking_id'] ?? 503) ?>" />
          <label class="block text-xs font-extrabold uppercase tracking-wider">
            Unggah Gambar Bukti Transfer (JPG/PNG/WEBP) *
          </label>
          <input
            type="file"
            name="proof_image"
            accept="image/jpeg,image/png,image/webp"
            required
            class="block w-full text-xs border-2 border-slate-900 bg-white p-1.5 file:mr-3 file:py-1.5 file:px-3 file:border-2 file:border-slate-900 file:font-extrabold file:uppercase file:bg-[#F5F4EE]"
          />
          <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white border-2 border-slate-900 py-3 px-5 text-xs font-extrabold uppercase tracking-widest shadow-[4px_4px_0px_#0F172A]">
            Kirim Bukti Pembayaran ('Menunggu Verifikasi') ➔
          </button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
