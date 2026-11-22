<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Laporan Pendapatan & Kunjungan — KOTHaK Photo Studio</title>
  <style>
    body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; color: #000; line-height: 1.4; padding: 20px; }
    .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 12px; margin-bottom: 20px; }
    .header h1 { margin: 0; font-size: 18pt; text-transform: uppercase; }
    .header p { margin: 2px 0; font-size: 10pt; }
    .meta-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
    .meta-table td { padding: 4px 0; }
    .summary-boxes { display: flex; gap: 15px; margin-bottom: 25px; }
    .box { flex: 1; border: 1px solid #000; padding: 10px; text-align: center; }
    .box-title { font-size: 9pt; text-transform: uppercase; font-weight: bold; }
    .box-val { font-size: 14pt; font-weight: bold; margin-top: 5px; }
    table.data-table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10pt; }
    table.data-table th, table.data-table td { border: 1px solid #000; padding: 6px 8px; text-align: left; }
    table.data-table th { background-color: #f0f0f0; text-transform: uppercase; font-size: 9pt; }
    .signature-area { margin-top: 50px; display: flex; justify-content: flex-end; }
    .signature-box { width: 220px; text-align: center; }
    .signature-space { height: 70px; }
    @media print {
      .no-print { display: none; }
      body { padding: 0; }
    }
  </style>
</head>
<body>

  <div class="no-print" style="margin-bottom: 15px; background: #e8eef9; padding: 12px; border: 2px solid #1e3a8a; text-align: right;">
    <button onclick="window.print()" style="padding: 9px 18px; font-weight: bold; cursor: pointer; background: #1e3a8a; color: white; border: none; font-family: sans-serif; text-transform: uppercase; letter-spacing: 0.05em;">
      Cetak / Unduh Dokumen PDF
    </button>
  </div>

  <!-- KOP SURAT RESMI -->
  <div class="header">
    <h1>KOTHaK PHOTO STUDIO PURBALINGGA</h1>
    <p>Jl. AW Sumarmo No.39, Kembaran Kulon, Kec. Purbalingga, Kabupaten Purbalingga, Jawa Tengah 53319</p>
    <p>Telepon/WhatsApp: +62 813-2674-377 &bull; Website: kothakphoto.id &bull; NMID: ID2026KOTHAKPBG01</p>
  </div>

  <div style="text-align: center; margin-bottom: 20px;">
    <h2 style="margin: 0; font-size: 14pt; text-decoration: underline;">LAPORAN REKAPITULASI TRANSAKSI &amp; KUNJUNGAN</h2>
    <p style="margin: 3px 0; font-size: 10pt;">Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
  </div>

  <!-- RINGKASAN METRIK -->
  <div class="summary-boxes">
    <div class="box">
      <div class="box-title">Total Pendapatan</div>
      <div class="box-val">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
    </div>
    <div class="box">
      <div class="box-title">Kunjungan Selesai</div>
      <div class="box-val">{{ $completedCount }} Sesi</div>
    </div>
    <div class="box">
      <div class="box-title">Total Antrean</div>
      <div class="box-val">{{ $totalBookings }} Antrean</div>
    </div>
    <div class="box">
      <div class="box-title">Tingkat Kehadiran</div>
      <div class="box-val">{{ $attendanceRate }}%</div>
    </div>
  </div>

  <!-- TABEL TRANSAKSI -->
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 30px;">No</th>
        <th>No. Antrean</th>
        <th>Tanggal &amp; Jam</th>
        <th>Nama Pelanggan</th>
        <th>Bilik Booth</th>
        <th>Total (IDR)</th>
        <th>Status Sesi</th>
      </tr>
    </thead>
    <tbody>
      @php $no = 1; @endphp
      @forelse($bookings as $b)
      <tr>
        <td style="text-align: center;">{{ $no++ }}</td>
        <td style="font-family: monospace; font-weight: bold;">{{ $b->queue_number }}</td>
        <td>{{ \Carbon\Carbon::parse($b->booking_date)->format('d/m/Y H:i') }}</td>
        <td><strong>{{ $b->user?->name ?? 'Guest' }}</strong> ({{ $b->user?->phone_whatsapp ?? '-' }})</td>
        <td>{{ $b->studio_backdrop ?? '-' }}</td>
        <td style="text-align: right; font-family: monospace;">{{ number_format($b->total_price, 0, ',', '.') }}</td>
        <td>{{ $b->status_label }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="7" style="text-align: center;">Tidak ada transaksi pada periode ini.</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  <!-- TANDA TANGAN -->
  <div class="signature-area">
    <div class="signature-box">
      <div>Purbalingga, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
      <div>Manajer Studio KOTHaK Photo,</div>
      <div class="signature-space"></div>
      <div style="font-weight: bold; text-decoration: underline;">Admin Studio Kothak</div>
      <div style="font-size: 10pt;">NIP: KTH-PBG-2026-01</div>
    </div>
  </div>

</body>
</html>
