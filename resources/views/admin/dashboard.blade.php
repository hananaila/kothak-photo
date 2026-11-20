@extends('layouts.admin')

@section('page_title', 'Analitik & Pelaporan Studio (FR-09)')

@section('content')
<div class="space-y-6 sm:space-y-8 max-w-7xl">

  <!-- DATE FILTER & REPORT EXPORT BAR -->
  <div class="neo-box bg-white p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-center gap-3 text-xs">
      <div>
        <label for="start_date" class="block font-black uppercase text-slate-700 text-[10px]">Dari Tanggal:</label>
        <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="border-2 border-slate-900 px-3 py-1.5 font-mono font-bold bg-[#F8FAFC]" />
      </div>
      <div>
        <label for="end_date" class="block font-black uppercase text-slate-700 text-[10px]">Sampai Tanggal:</label>
        <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="border-2 border-slate-900 px-3 py-1.5 font-mono font-bold bg-[#F8FAFC]" />
      </div>
      <button type="submit" class="neo-btn bg-slate-900 text-white px-4 py-2 font-black uppercase text-xs mt-3">
        Filter Data ➔
      </button>
    </form>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.reports.csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="neo-btn bg-emerald-500 hover:bg-emerald-600 text-slate-900 px-4 py-2.5 text-xs font-black uppercase flex items-center gap-1.5">
        <span>📥</span> Unduh CSV
      </a>
      <a href="{{ route('admin.reports.print', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="neo-btn bg-white hover:bg-slate-50 text-slate-900 px-4 py-2.5 text-xs font-black uppercase flex items-center gap-1.5">
        <span>🖨️</span> Cetak / PDF
      </a>
    </div>
  </div>

  <!-- 4 METRIC CARDS (FR-09) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Card 1: Total Revenue -->
    <div class="neo-box bg-white p-4 sm:p-5">
      <div class="text-[10px] font-black uppercase text-slate-500 tracking-wider">TOTAL PENDAPATAN (PERIODE)</div>
      <div class="text-2xl sm:text-3xl font-black font-mono text-blue-700 mt-1">
        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
      </div>
      <div class="text-[11px] font-bold text-slate-600 mt-1 flex items-center justify-between">
        <span>Hari Ini:</span>
        <span class="font-mono text-emerald-700">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</span>
      </div>
    </div>

    <!-- Card 2: Completed Visits -->
    <div class="neo-box bg-white p-4 sm:p-5">
      <div class="text-[10px] font-black uppercase text-slate-500 tracking-wider">KUNJUNGAN / RESERVASI SELESAI</div>
      <div class="text-2xl sm:text-3xl font-black font-mono text-slate-900 mt-1">
        {{ $completedCount }} <span class="text-sm font-normal text-slate-500">/ {{ $totalBookings }} Total</span>
      </div>
      <div class="text-[11px] font-bold text-emerald-700 mt-1">
        ✓ Sesi Berhasil Diselesaikan
      </div>
    </div>

    <!-- Card 3: Success Verification Rate -->
    <div class="neo-box bg-white p-4 sm:p-5">
      <div class="text-[10px] font-black uppercase text-slate-500 tracking-wider">TINGKAT VERIFIKASI QRIS</div>
      <div class="text-2xl sm:text-3xl font-black font-mono text-emerald-600 mt-1">
        {{ $successRate }}%
      </div>
      <div class="text-[11px] font-bold text-slate-600 mt-1">
        Transaksi QRIS Lolos Verifikasi Kasir
      </div>
    </div>

    <!-- Card 4: Avg Session Duration -->
    <div class="neo-box bg-white p-4 sm:p-5">
      <div class="text-[10px] font-black uppercase text-slate-500 tracking-wider">RATA-RATA DURASI SESI</div>
      <div class="text-2xl sm:text-3xl font-black font-mono text-amber-600 mt-1">
        {{ $avgDuration }} <span class="text-sm font-normal text-slate-500">Menit</span>
      </div>
      <div class="text-[11px] font-bold text-slate-600 mt-1">
        Standar Efisiensi Booth Studio
      </div>
    </div>
  </div>

  <!-- CHARTS ROW 1: BAR CHART & DONUT CHART (FR-09) -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- BAR CHART: Pendapatan Harian -->
    <div class="lg:col-span-8 neo-box bg-white p-5 sm:p-6">
      <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-4">
        <div>
          <span class="text-[10px] font-black uppercase text-blue-700">TREN PENDAPATAN</span>
          <h3 class="text-base sm:text-lg font-black text-slate-900">Grafik Pendapatan Harian (Bar Chart)</h3>
        </div>
        <span class="text-xs font-mono text-slate-500">IDR (Rupiah)</span>
      </div>
      <div class="h-64 sm:h-72">
        <canvas id="revenueBarChart"></canvas>
      </div>
    </div>

    <!-- DONUT CHART: Distribusi Paket Foto -->
    <div class="lg:col-span-4 neo-box bg-white p-5 sm:p-6 flex flex-col justify-between">
      <div>
        <div class="border-b-2 border-slate-900 pb-3 mb-4">
          <span class="text-[10px] font-black uppercase text-blue-700">POPULARITAS PAKET</span>
          <h3 class="text-base sm:text-lg font-black text-slate-900">Performa Paket (Donut Chart)</h3>
        </div>
        <div class="h-56 sm:h-64 flex items-center justify-center">
          <canvas id="packageDonutChart"></canvas>
        </div>
      </div>
      <div class="text-[11px] text-slate-500 text-center pt-3 border-t border-slate-200">
        Berdasarkan total reservasi paket periode terpilih.
      </div>
    </div>
  </div>

  <!-- CHARTS ROW 2: PEAK HOURS ANALYSIS (JAM SIBUK) -->
  <div class="neo-box bg-white p-5 sm:p-6">
    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-4">
      <div>
        <span class="text-[10px] font-black uppercase text-amber-700">ANALISIS OPERASIONAL STUDIO</span>
        <h3 class="text-base sm:text-lg font-black text-slate-900">Distribusi Jam Sibuk Photobox (Peak Hours Analysis)</h3>
      </div>
      <span class="text-xs font-bold text-slate-500">Pukul 10:00 - 21:00 WIB</span>
    </div>
    <div class="h-56 sm:h-64">
      <canvas id="busyHoursChart"></canvas>
    </div>
  </div>

  <!-- RECENT BOOKINGS & REPORT TABLE -->
  <div class="neo-box bg-white overflow-hidden">
    <div class="bg-slate-900 text-white px-5 py-3.5 font-black uppercase text-xs tracking-wider flex items-center justify-between">
      <span>Daftar Transaksi Periode Terpilih</span>
      <span class="font-mono text-slate-400 text-xs">{{ $recentBookings->count() }} Data Terbaru</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-[#F8FAFC] border-b-2 border-slate-900 uppercase font-black text-slate-700">
            <th class="p-3.5">Antrean</th>
            <th class="p-3.5">Waktu Reservasi</th>
            <th class="p-3.5">Pelanggan</th>
            <th class="p-3.5">Paket Foto</th>
            <th class="p-3.5">Total Biaya</th>
            <th class="p-3.5">Pembayaran</th>
            <th class="p-3.5">Status Booking</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-slate-900">
          @forelse($recentBookings as $b)
          <tr class="hover:bg-slate-50 transition-colors">
            <td class="p-3.5 font-mono font-black text-blue-700">
              {{ $b->queue_number }}
              <span class="text-[10px] font-mono text-slate-400">[{{ strtoupper($b->booking_type ?? 'online') }}]</span>
            </td>
            <td class="p-3.5 font-mono text-slate-600">
              {{ \Carbon\Carbon::parse($b->booking_date)->format('d/m/Y H:i') }}
            </td>
            <td class="p-3.5 font-bold text-slate-900">
              {{ $b->user?->name ?? 'Guest' }}
            </td>
            <td class="p-3.5">
              {{ $b->package?->name ?? '-' }}
            </td>
            <td class="p-3.5 font-mono font-black text-slate-900">
              Rp {{ number_format($b->total_price, 0, ',', '.') }}
            </td>
            <td class="p-3.5 font-mono font-bold">
              @if($b->payment?->payment_status === 'verified')
                <span class="text-emerald-700">✓ LUNAS</span>
              @elseif($b->payment?->payment_status === 'pending')
                <span class="text-amber-700">⏳ MENUNGGU</span>
              @else
                <span class="text-rose-700">✕ TOLAK</span>
              @endif
            </td>
            <td class="p-3.5">
              <span class="border px-2 py-0.5 text-[10px] font-black uppercase {{ $b->status_badge_class }}">
                {{ $b->status_label }}
              </span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="p-8 text-center text-slate-500 font-bold">
              Tidak ada data transaksi pada rentang tanggal ini.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

@push('scripts')
<script>
  // 1. Daily Revenue Bar Chart (Chart.js)
  const dailyLabels = {!! json_encode(array_column($dailyChartData, 'label')) !!};
  const dailyRevenues = {!! json_encode(array_column($dailyChartData, 'revenue')) !!};
  const dailyVisits = {!! json_encode(array_column($dailyChartData, 'visits')) !!};

  const ctxBar = document.getElementById('revenueBarChart').getContext('2d');
  new Chart(ctxBar, {
    type: 'bar',
    data: {
      labels: dailyLabels,
      datasets: [
        {
          label: 'Pendapatan (Rp)',
          data: dailyRevenues,
          backgroundColor: '#1D4ED8',
          borderColor: '#0F172A',
          borderWidth: 2,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: function(context) {
              return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'Rp ' + (value / 1000) + 'k';
            }
          }
        }
      }
    }
  });

  // 2. Package Popularity Donut Chart (Chart.js)
  const pkgNames = {!! json_encode(array_column($packageStats, 'name')) !!};
  const pkgCounts = {!! json_encode(array_column($packageStats, 'count')) !!};

  const ctxDonut = document.getElementById('packageDonutChart').getContext('2d');
  new Chart(ctxDonut, {
    type: 'doughnut',
    data: {
      labels: pkgNames,
      datasets: [
        {
          data: pkgCounts,
          backgroundColor: [
            '#1D4ED8',
            '#F59E0B',
            '#10B981',
            '#8B5CF6',
            '#EC4899',
            '#06B6D4',
          ],
          borderColor: '#0F172A',
          borderWidth: 2,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { boxWidth: 12, font: { size: 10 } }
        }
      }
    }
  });

  // 3. Busy Hours Analysis Chart (Chart.js)
  const hoursLabels = {!! json_encode(array_keys($hoursDistribution)) !!};
  const hoursCounts = {!! json_encode(array_values($hoursDistribution)) !!};

  const ctxHours = document.getElementById('busyHoursChart').getContext('2d');
  new Chart(ctxHours, {
    type: 'bar',
    data: {
      labels: hoursLabels,
      datasets: [
        {
          label: 'Jumlah Kunjungan Sesi',
          data: hoursCounts,
          backgroundColor: '#F59E0B',
          borderColor: '#0F172A',
          borderWidth: 2,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1 }
        }
      }
    }
  });
</script>
@endpush
@endsection
