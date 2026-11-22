@extends('layouts.admin')

@section('page_title', 'Analitik & Pelaporan Studio')

@section('content')
<div class="space-y-6 sm:space-y-8 max-w-7xl">

  <!-- PERIOD FILTER & EXPORT BAR -->
  <div class="bg-white rounded-3xl border border-[#E7E2D8] p-5 shadow-soft space-y-4">
    <!-- Quick Filter Tabs & Action Buttons -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
      
      <!-- Quick Filter Pills -->
      <div>
        <label class="block font-heading font-bold uppercase text-slate-500 text-[10px] mb-1.5">PILIH PERIODE ANALITIK:</label>
        <div class="inline-flex p-1 bg-slate-100 rounded-2xl gap-1 text-xs font-heading font-bold">
          <a
            href="{{ route('admin.dashboard', ['period' => 'today']) }}"
            class="px-3.5 py-1.5 rounded-xl transition-all {{ ($currentPeriod ?? '') === 'today' ? 'bg-[#1E3A8A] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}"
          >
            Hari Ini
          </a>
          <a
            href="{{ route('admin.dashboard', ['period' => 'week']) }}"
            class="px-3.5 py-1.5 rounded-xl transition-all {{ ($currentPeriod ?? '') === 'week' ? 'bg-[#1E3A8A] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}"
          >
            Minggu Ini
          </a>
          <a
            href="{{ route('admin.dashboard', ['period' => 'month']) }}"
            class="px-3.5 py-1.5 rounded-xl transition-all {{ ($currentPeriod ?? '') === 'month' ? 'bg-[#1E3A8A] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}"
          >
            Bulan Ini
          </a>
          <a
            href="{{ route('admin.dashboard', ['period' => 'all']) }}"
            class="px-3.5 py-1.5 rounded-xl transition-all {{ ($currentPeriod ?? 'all') === 'all' ? 'bg-[#1E3A8A] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}"
          >
            Semua
          </a>
        </div>
      </div>

      <!-- Export Report Buttons -->
      <div class="flex items-center gap-2 self-start lg:self-end">
        <a href="{{ route('admin.reports.csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 text-xs font-heading font-bold uppercase flex items-center gap-1.5 rounded-xl shadow-xs transition-all">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
          <span>Ekspor CSV</span>
        </a>
        <a href="{{ route('admin.reports.print', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="bg-white hover:bg-slate-50 text-[#1A1A2E] border border-slate-200 px-3.5 py-2 text-xs font-heading font-bold uppercase flex items-center gap-1.5 rounded-xl shadow-xs transition-all">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
          <span>Cetak / PDF</span>
        </a>
      </div>
    </div>

    <!-- Collapsible/Inline Custom Date Picker -->
    <div class="pt-3 border-t border-slate-100">
      <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-center gap-3 text-xs">
        <input type="hidden" name="period" value="custom" />
        <div class="flex items-center gap-2">
          <span class="text-[11px] font-heading font-bold uppercase text-slate-500">Rentang Kustom:</span>
          <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-xl border border-slate-300 px-3 py-1 font-mono font-bold bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]" />
          <span class="text-slate-400 font-bold">&ndash;</span>
          <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-xl border border-slate-300 px-3 py-1 font-mono font-bold bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]" />
        </div>
        <button type="submit" class="bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200 px-3 py-1 font-heading font-bold uppercase text-[11px] rounded-xl transition-all">
          Terapkan
        </button>
        <span class="text-[11px] font-mono text-slate-400">
          (Aktif: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})
        </span>
      </form>
    </div>
  </div>

  <!-- 4 METRIC CARDS -->
  <div id="adminMetricCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Card 1: Total Revenue -->
    <div class="bg-white rounded-3xl border border-[#E7E2D8] p-5 shadow-soft">
      <div class="text-[10px] font-heading font-black uppercase text-[#1E3A8A] tracking-wider">TOTAL PENDAPATAN (PERIODE)</div>
      <div class="text-2xl sm:text-3xl font-black font-mono text-[#1E3A8A] mt-2">
        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
      </div>
      <div class="text-[11px] font-medium text-slate-600 mt-2 flex items-center justify-between font-sans pt-2 border-t border-[#F0ECE4]">
        <span>Hari Ini:</span>
        <span class="font-mono text-emerald-700 font-bold">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</span>
      </div>
    </div>

    <!-- Card 2: Completed Visits -->
    <div class="bg-white rounded-3xl border border-[#E7E2D8] p-5 shadow-soft">
      <div class="text-[10px] font-heading font-black uppercase text-slate-600 tracking-wider">KUNJUNGAN SELESAI</div>
      <div class="text-2xl sm:text-3xl font-black font-mono text-slate-900 mt-2">
        {{ $completedCount }} <span class="text-base text-slate-400 font-normal">/ {{ $totalBookings }} Total</span>
      </div>
      <div class="text-[11px] font-bold text-emerald-700 mt-2 pt-2 border-t border-[#F0ECE4]">
        Sesi Selesai Dilaksanakan
      </div>
    </div>

    <!-- Card 3: Attendance Rate -->
    <div class="bg-white rounded-3xl border border-[#E7E2D8] p-5 shadow-soft">
      <div class="text-[10px] font-heading font-black uppercase text-slate-600 tracking-wider">TINGKAT KEHADIRAN TAMU</div>
      <div class="text-2xl sm:text-3xl font-black font-mono text-[#1E3A8A] mt-2">
        {{ $attendanceRate }}%
      </div>
      <div class="text-[11px] font-medium text-slate-600 mt-2 flex items-center justify-between pt-2 border-t border-[#F0ECE4]">
        <span>Tidak Hadir / Lewat:</span>
        <span class="font-mono text-rose-600 font-bold">{{ $noShowCount }} Sesi</span>
      </div>
    </div>

    <!-- Card 4: Most Popular Booth -->
    <div class="bg-white rounded-3xl border border-[#E7E2D8] p-5 shadow-soft">
      <div class="text-[10px] font-heading font-black uppercase text-amber-700 tracking-wider">BOOTH PALING RAMAI</div>
      <div class="text-xl sm:text-2xl font-black font-heading text-amber-900 mt-2 truncate" title="{{ $topBoothName }}">
        {{ $topBoothName }}
      </div>
      <div class="text-[11px] font-medium text-slate-600 mt-2 flex items-center justify-between pt-2 border-t border-[#F0ECE4]">
        <span>Total Antrean:</span>
        <span class="font-mono text-[#1E3A8A] font-bold">{{ $topBoothCount }} Antrean</span>
      </div>
    </div>
  </div>

  <!-- CHARTS ROW: BAR CHART & DONUT CHART (PER PAKET FOTO) -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- BAR CHART: Pendapatan Harian -->
    <div class="lg:col-span-7 bg-white rounded-3xl border border-[#E7E2D8] p-6 shadow-soft flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between border-b border-[#F0ECE4] pb-3 mb-4">
          <div>
            <span class="text-[10px] font-heading font-black uppercase text-[#1E3A8A]">TREN PENDAPATAN</span>
            <h3 class="text-base sm:text-lg font-heading font-black text-[#1A1A2E]">Grafik Pendapatan Harian</h3>
          </div>
          <span class="text-xs font-mono text-[#1E3A8A] font-bold">IDR (Rupiah)</span>
        </div>
        <div class="h-64 sm:h-72">
          <canvas id="revenueBarChart"></canvas>
        </div>
      </div>
      <div class="text-[11px] text-slate-500 text-center pt-3 border-t border-[#F0ECE4] font-sans">
        Perkembangan pendapatan studio harian pada rentang waktu terpilih.
      </div>
    </div>

    <!-- DONUT CHART: Pendapatan per Booth Foto -->
    <div class="lg:col-span-5 bg-white rounded-3xl border border-[#E7E2D8] p-6 flex flex-col justify-between shadow-soft">
      <div>
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
          <div>
            <span class="text-[10px] font-heading font-black uppercase text-[#1E3A8A]">KONTRIBUSI OMZET</span>
            <h3 class="text-base sm:text-lg font-heading font-black text-[#1A1A2E]">Pendapatan per Booth Foto</h3>
          </div>
          <span class="text-xs font-mono text-emerald-700 font-bold">Total IDR</span>
        </div>
        <div class="h-56 sm:h-64 flex items-center justify-center">
          <canvas id="packageDonutChart"></canvas>
        </div>
      </div>
      
      <!-- Mini Legends with IDR & Color Dots -->
      <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
        @foreach($packageStats as $pStat)
        <div class="flex items-center justify-between text-[11px]">
          <div class="flex items-center gap-2 truncate max-w-[200px]">
            <span class="w-2.5 h-2.5 rounded-full shrink-0 shadow-2xs" style="background-color: {{ $pStat['color'] }}"></span>
            <span class="text-slate-700 font-bold truncate">{{ $pStat['name'] }}</span>
          </div>
          <span class="font-mono font-black text-[#1E3A8A]">
            Rp {{ number_format($pStat['revenue'], 0, ',', '.') }}
            <span class="text-slate-400 font-normal text-[10px]">({{ $pStat['percentage'] }}%)</span>
          </span>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  <!-- SECTION: RINCIAN PENDAPATAN PER BOOTH FOTO (TABLE/CARD BREAKDOWN) -->
  <div class="bg-white rounded-3xl border border-[#E7E2D8] shadow-soft overflow-hidden">
    <div class="bg-[#1E3A8A] text-white px-6 py-4 font-heading font-bold uppercase text-xs tracking-wider flex items-center justify-between">
      <span>Rincian Pendapatan per Booth Foto Studio</span>
      <span class="font-mono text-white/80 text-xs">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 uppercase font-heading font-bold text-slate-700 text-[11px]">
            <th class="p-4 pl-6">Bilik Booth Studio</th>
            <th class="p-4">Kategori &amp; Badge</th>
            <th class="p-4">Tarif Satuan</th>
            <th class="p-4 text-center">Total Antrean</th>
            <th class="p-4">Total Pendapatan (IDR)</th>
            <th class="p-4 pr-6">Kontribusi Omzet</th>
          </tr>
        </thead>
        <tbody id="adminPackageTableBody" class="divide-y divide-slate-100">
          @forelse($packageStats as $pStat)
          <tr class="hover:bg-slate-50/80 transition-colors">
            <td class="p-4 pl-6">
              <div class="font-heading font-bold text-[#1A1A2E] text-sm">{{ $pStat['name'] }}</div>
            </td>
            <td class="p-4">
              <span class="bg-[#E8EEF9] text-[#1E3A8A] px-2.5 py-0.5 rounded-full text-[10px] font-heading font-bold uppercase">
                {{ $pStat['category'] ?? 'PHOTODUO' }}
              </span>
              @if(!empty($pStat['badge']))
                <span class="bg-amber-100 text-amber-900 px-2.5 py-0.5 rounded-full text-[10px] font-heading font-bold uppercase ml-1">
                  {{ $pStat['badge'] }}
                </span>
              @endif
            </td>
            <td class="p-4 font-mono font-bold text-slate-700">
              Rp {{ number_format($pStat['price'], 0, ',', '.') }}
            </td>
            <td class="p-4 text-center font-mono font-bold text-slate-900">
              <span class="bg-slate-100 px-2.5 py-1 rounded-lg text-xs">
                {{ $pStat['count'] }} Sesi
              </span>
            </td>
            <td class="p-4 font-mono font-black text-[#1E3A8A] text-sm">
              Rp {{ number_format($pStat['revenue'], 0, ',', '.') }}
            </td>
            <td class="p-4 pr-6">
              <div class="flex items-center gap-2">
                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                  <div class="h-2.5 rounded-full" style="background-color: {{ $pStat['color'] }}; width: {{ min(100, $pStat['percentage']) }}%"></div>
                </div>
                <span class="font-mono font-bold text-[11px] text-slate-700 shrink-0 w-12 text-right">
                  {{ $pStat['percentage'] }}%
                </span>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="p-8 text-center text-slate-500 font-medium">
              Belum ada data paket foto.
            </td>
          </tr>
          @endforelse
        </tbody>
        <tfoot class="bg-slate-50/80 border-t-2 border-slate-200 text-xs font-bold font-heading">
          <tr>
            <td class="p-4 pl-6 uppercase text-[#1A1A2E]" colspan="3">Total Akumulasi Pendapatan Booth:</td>
            <td class="p-4 text-center font-mono font-black text-slate-900">{{ $totalBookings }} Sesi</td>
            <td class="p-4 font-mono font-black text-[#1E3A8A] text-sm">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            <td class="p-4 pr-6 font-mono font-black text-slate-700">100.0%</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <!-- RECENT BOOKINGS & REPORT TABLE -->
  <div class="bg-white rounded-3xl border border-slate-200/80 shadow-soft overflow-hidden">
    <div class="bg-[#1E3A8A] text-white px-6 py-4 font-heading font-bold uppercase text-xs tracking-wider flex items-center justify-between">
      <span>Daftar Transaksi Antrean Terpilih</span>
      <span class="font-mono text-white/80 text-xs">{{ $recentBookings->count() }} Data Terbaru</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 uppercase font-heading font-bold text-slate-700 text-[11px]">
            <th class="p-4 pl-6">Antrean</th>
            <th class="p-4">Waktu Reservasi</th>
            <th class="p-4">Pelanggan</th>
            <th class="p-4">Paket &amp; Booth</th>
            <th class="p-4">Total Biaya</th>
            <th class="p-4 pr-6 text-right">Status Sesi</th>
          </tr>
        </thead>
        <tbody id="recentBookingsTableBody" class="divide-y divide-slate-100">
          @forelse($recentBookings as $b)
          <tr class="hover:bg-slate-50/80 transition-colors">
            <td class="p-4 pl-6 font-mono font-black text-[#1E3A8A]">
              {{ $b->queue_number }}
            </td>
            <td class="p-4 font-mono text-slate-600">
              {{ \Carbon\Carbon::parse($b->booking_date)->format('d/m/Y H:i') }}
            </td>
            <td class="p-4">
              <div class="font-bold text-[#1A1A2E]">{{ $b->user?->name ?? 'Guest' }}</div>
              <div class="text-[10px] font-mono text-slate-400">{{ $b->user?->phone_whatsapp ?? '-' }}</div>
            </td>
            <td class="p-4">
              <div class="font-bold text-slate-800">{{ $b->package?->name ?? 'Paket Foto' }}</div>
              <div class="text-[10px] text-slate-500 font-mono">{{ $b->studio_backdrop ?? 'Flower Garden Booth' }}</div>
            </td>
            <td class="p-4 font-mono font-black text-[#1E3A8A]">
              Rp {{ number_format($b->total_price, 0, ',', '.') }}
            </td>
            <td class="p-4 pr-6 text-right">
              @if($b->status === 'completed')
                <span class="px-3 py-1 rounded-full text-[10px] font-heading font-black uppercase inline-block bg-emerald-100 text-emerald-800">
                  Selesai
                </span>
              @elseif($b->status === 'in_studio')
                <span class="px-3 py-1 rounded-full text-[10px] font-heading font-black uppercase inline-block bg-emerald-600 text-white">
                  Sedang Foto
                </span>
              @elseif(in_array($b->status, ['cancelled', 'no_show']))
                <span class="px-3 py-1 rounded-full text-[10px] font-heading font-black uppercase inline-block bg-rose-100 text-rose-800">
                  Tidak Hadir
                </span>
              @else
                <span class="px-3 py-1 rounded-full text-[10px] font-heading font-black uppercase inline-block bg-amber-100 text-amber-900">
                  Dalam Antrean
                </span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="p-8 text-center text-slate-500 font-heading font-medium">
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
  // 1. Daily Revenue Bar Chart (Warna Dinamis & Harmonis)
  const dailyLabels = {!! json_encode(array_column($dailyChartData, 'label')) !!};
  const dailyRevenues = {!! json_encode(array_column($dailyChartData, 'revenue')) !!};
  const isTodayList = {!! json_encode(array_column($dailyChartData, 'is_today')) !!};

  // Bar hari ini disorot Royal Navy (#1E3A8A), hari-hari sebelumnya bernuansa Sapphire Blue (#2563EB)
  const barColors = isTodayList.map(isToday => isToday ? '#1E3A8A' : '#3B82F6');
  const hoverColors = isTodayList.map(isToday => isToday ? '#2D2DB0' : '#60A5FA');

  const ctxBar = document.getElementById('revenueBarChart').getContext('2d');
  new Chart(ctxBar, {
    type: 'bar',
    data: {
      labels: dailyLabels,
      datasets: [
        {
          label: 'Pendapatan (Rp)',
          data: dailyRevenues,
          backgroundColor: barColors,
          hoverBackgroundColor: hoverColors,
          borderRadius: 8,
          borderWidth: 0,
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
              return 'Pendapatan: Rp ' + Number(context.raw).toLocaleString('id-ID');
            }
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { font: { family: 'DM Sans', weight: 'bold' } }
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(30, 58, 138, 0.06)' },
          ticks: {
            callback: function(value) {
              return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'k';
            }
          }
        }
      }
    }
  });

  // 2. Package Revenue Donut Chart (Warna Harmonis & Bervariasi per Booth)
  const packageLabels = {!! json_encode(array_column($packageStats, 'name')) !!};
  const packageRevenues = {!! json_encode(array_column($packageStats, 'revenue')) !!};
  const packageColors = {!! json_encode(array_column($packageStats, 'color')) !!};

  const ctxDonut = document.getElementById('packageDonutChart').getContext('2d');
  new Chart(ctxDonut, {
    type: 'doughnut',
    data: {
      labels: packageLabels,
      datasets: [{
        data: packageRevenues,
        backgroundColor: packageColors,
        borderWidth: 3,
        borderColor: '#ffffff',
        hoverOffset: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const val = Number(context.raw);
              return ' ' + context.label + ': Rp ' + val.toLocaleString('id-ID');
            }
          }
        }
      },
      cutout: '68%'
    }
  });

  // 3. REAL-TIME AUTO-UPDATE DASBOR ADMIN (Setiap 3.5 detik, tanpa reload halaman)
  let isAdminRefreshing = false;

  function fetchLiveAdminDashboard() {
    if (isAdminRefreshing) return;
    isAdminRefreshing = true;

    const url = window.location.href + (window.location.href.includes('?') ? '&' : '?') + '_t=' + Date.now();

    fetch(url, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Cache-Control': 'no-cache, no-store, must-revalidate',
        'Pragma': 'no-cache'
      },
      cache: 'no-store'
    })
    .then(res => res.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');

      // Update 4 Metric Cards
      const newMetrics = doc.getElementById('adminMetricCards');
      const curMetrics = document.getElementById('adminMetricCards');
      if (newMetrics && curMetrics) {
        curMetrics.innerHTML = newMetrics.innerHTML;
      }

      // Update Package / Booth Revenue Breakdown Table
      const newPkgTable = doc.getElementById('adminPackageTableBody');
      const curPkgTable = document.getElementById('adminPackageTableBody');
      if (newPkgTable && curPkgTable) {
        curPkgTable.innerHTML = newPkgTable.innerHTML;
      }

      // Update Recent Bookings Table
      const newRecent = doc.getElementById('recentBookingsTableBody');
      const curRecent = document.getElementById('recentBookingsTableBody');
      if (newRecent && curRecent) {
        curRecent.innerHTML = newRecent.innerHTML;
      }
    })
    .catch(err => {
      // Silent on brief connection blip
    })
    .finally(() => {
      isAdminRefreshing = false;
    });
  }

  // Cross-tab broadcast listener untuk respon seketika (0 ms saat pelanggan pesan / kasir update antrean)
  try {
    if ('BroadcastChannel' in window) {
      const liveQueueChannel = new BroadcastChannel('kothak_live_queue');
      liveQueueChannel.onmessage = fetchLiveAdminDashboard;
    }
  } catch(e) {}
  window.addEventListener('storage', function(e) {
    if (e.key === 'kothak_queue_bump') fetchLiveAdminDashboard();
  });

  // Polling data dasbor admin setiap 1 detik (Real-Time Ultra Responsif)
  setInterval(fetchLiveAdminDashboard, 1000);
</script>
@endpush
@endsection
