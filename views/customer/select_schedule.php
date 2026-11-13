<?php
// File: views/customer/select_schedule.php
// Tugas 2.2: Halaman Pemilihan Jadwal (Mengambil data dari tabel schedules & menampilkan slot tersedia berdasarkan capacity)

declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getConnection();
$packageId = (int) ($_GET['package_id'] ?? 2);
$selectedDate = $_GET['date'] ?? date('Y-m-d');

$stmtPkg = $pdo->prepare("SELECT * FROM packages WHERE id = :id AND is_active = 1 LIMIT 1");
$stmtPkg->execute(['id' => $packageId]);
$package = $stmtPkg->fetch() ?: [
    'id'          => 2,
    'name'        => 'Kothak Signature Box (2-4 Orang)',
    'duration'    => 15,
    'price'       => 35000,
    'description' => 'Sesi photobox 15 menit, 2 cetak 4R, soft file HD + GIF.'
];

$stmtSchedules = $pdo->prepare("
    SELECT 
        s.id,
        s.date,
        s.start_time,
        s.end_time,
        s.capacity,
        COUNT(b.id) AS total_booked,
        (s.capacity - COUNT(b.id)) AS remaining_quota
    FROM schedules s
    LEFT JOIN bookings b 
        ON b.schedule_id = s.id 
        AND b.status NOT IN ('cancelled', 'Ditolak')
    WHERE s.date = :selected_date
    GROUP BY s.id, s.date, s.start_time, s.end_time, s.capacity
    ORDER BY s.start_time ASC
");
$stmtSchedules->execute(['selected_date' => $selectedDate]);
$schedules = $stmtSchedules->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pilih Jadwal Slot — KOTHaK Cafe &amp; Photo</title>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
</head>
<body class="bg-[#F5F4EE] text-slate-900 font-sans min-h-screen p-6">
  <div class="max-w-4xl mx-auto border-2 border-slate-900 bg-white p-6 shadow-[6px_6px_0px_#1D4ED8]">
    <div class="border-b-2 border-slate-900 pb-4 mb-6 flex items-center justify-between">
      <div>
        <span class="text-xs font-extrabold uppercase tracking-widest text-blue-700">LANGKAH 2 &bull; PEMILIHAN JADWAL STUDIO</span>
        <h1 class="text-2xl font-extrabold mt-1"><?= htmlspecialchars($package['name']) ?></h1>
      </div>
      <div class="text-right font-mono font-black text-xl text-blue-700">
        Rp <?= number_format((float) $package['price'], 0, ',', '.') ?>
      </div>
    </div>

    <form action="/public/process_booking.php" method="POST" class="space-y-6">
      <input type="hidden" name="package_id" value="<?= (int) $package['id'] ?>" />

      <div>
        <label class="block text-xs font-extrabold uppercase tracking-wider mb-2">Pilih Slot Waktu Tersedia (<?= htmlspecialchars($selectedDate) ?>):</label>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          <?php foreach ($schedules as $slot): ?>
            <?php $rem = (int) $slot['remaining_quota']; $isAvail = $rem > 0; ?>
            <label class="border-2 border-slate-900 p-3 block <?= $isAvail ? 'bg-white hover:bg-blue-50 cursor-pointer' : 'bg-slate-200 opacity-60 cursor-not-allowed' ?>">
              <input type="radio" name="schedule_id" value="<?= (int) $slot['id'] ?>" <?= $isAvail ? 'required' : 'disabled' ?> class="mr-1.5 accent-blue-700" />
              <span class="font-mono font-extrabold text-xs">
                <?= substr($slot['start_time'], 0, 5) ?> - <?= substr($slot['end_time'], 0, 5) ?> WIB
              </span>
              <div class="text-[11px] font-bold uppercase mt-1 <?= $isAvail ? 'text-emerald-700' : 'text-rose-600' ?>">
                <?= $isAvail ? "Tersedia: {$rem}/{$slot['capacity']} Kuota" : 'PENUH (0 Slot)' ?>
              </div>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white border-2 border-slate-900 py-3.5 px-6 text-xs font-extrabold uppercase tracking-widest shadow-[4px_4px_0px_#0F172A]">
        Konfirmasi Jadwal &amp; Simpan Booking ('Menunggu Pembayaran') ➔
      </button>
    </form>
  </div>
</body>
</html>
