@extends('layouts.app')

@section('title', 'Tiket Saya — KOTHaK Photo Studio')

@section('content')
<div class="w-full max-w-6xl mx-auto space-y-6">

  <!-- HEADER -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#1E3A8A]/15">
    <div>
      <div class="inline-flex items-center gap-2">
        <a href="{{ route('packages.index') }}" title="Kembali ke Beranda" class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-white border border-[#1E3A8A]/20 text-[#1E3A8A] hover:bg-[#E8EEF9] shadow-sm transition-all group shrink-0">
          <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <span class="text-xs font-heading font-black uppercase tracking-wider text-[#1E3A8A]">HISTORI TIKET BOOTH</span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-heading font-black text-[#1A1A2E] mt-1.5">
        Tiket Saya <span class="font-serif italic font-normal text-[#1E3A8A] lowercase">studio photobox</span>
      </h1>
      <p class="text-xs sm:text-sm text-slate-500 font-sans mt-0.5">
        Daftar seluruh riwayat booth yang pernah kamu pesan beserta status antreannya.
      </p>
    </div>
  </div>

  <!-- DUA FILTER CHIP: ACTIVE & HISTORY (DILEBARKAN KE TENGAH SECARA RESPONSIVE, AMBIL ANTREAN BARU DIHAPUS) -->
  <div id="customerHistoryContainer" class="space-y-6">
    <div class="flex items-center justify-center w-full py-1">
      <div class="bg-white p-1.5 rounded-full border border-[#E7E2D8] shadow-xs flex items-center w-full max-w-md sm:max-w-xl">
        <a href="{{ route('booking.history', ['tab' => 'active']) }}" class="flex-1 text-center py-2.5 px-6 rounded-full text-xs sm:text-sm font-heading font-black tracking-wider uppercase transition-all {{ $tab === 'active' ? 'bg-[#1E3A8A] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-[#FAF9F6]' }}">
          Active ({{ $activeCount }})
        </a>
        <a href="{{ route('booking.history', ['tab' => 'history']) }}" class="flex-1 text-center py-2.5 px-6 rounded-full text-xs sm:text-sm font-heading font-black tracking-wider uppercase transition-all {{ $tab === 'history' ? 'bg-[#1E3A8A] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-[#FAF9F6]' }}">
          History ({{ $historyCount }})
        </a>
      </div>
    </div>

  <!-- LIST OF TICKETS (RESPONSIF PENUH DI DESKTOP, ADAPTIF DI MOBILE & TABLET) -->
  @if($bookings->isEmpty())
    <div class="bg-white rounded-3xl border border-[#E7E2D8] p-8 sm:p-12 text-center space-y-4 shadow-soft max-w-md mx-auto">
      <!-- Mini Ticket Illustration -->
      <div class="w-16 h-20 mx-auto rounded-xl bg-[#FAF9F6] border-2 border-dashed border-[#1E3A8A]/30 flex flex-col items-center justify-center p-2 shadow-sm rotate-2">
        <span class="text-[9px] font-mono font-bold text-slate-500 uppercase">TIKET</span>
        <span class="text-base font-mono font-black text-[#1E3A8A] mt-0.5">---</span>
      </div>

      @if($tab === 'active')
        <div class="space-y-1">
          <h3 class="font-heading font-black text-lg sm:text-xl text-[#1A1A2E]">Belum Ada Tiket Aktif</h3>
          <p class="text-xs text-slate-500 max-w-sm mx-auto">
            No active tickets. Belum ada nomor antrean yang sedang aktif di perangkat ini.
          </p>
        </div>

        <div class="pt-2 space-y-3">
          <a href="{{ route('packages.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 rounded-full text-xs font-heading font-black uppercase tracking-wider bg-[#1E3A8A] text-white hover:bg-[#2D2DB0] shadow-sm transition-all">
            Ambil Nomor Antrean &rarr;
          </a>

          @if($historyCount > 0)
            <div>
              <a href="{{ route('booking.history', ['tab' => 'history']) }}" class="text-xs font-heading font-bold text-[#1E3A8A] hover:underline inline-flex items-center gap-1">
                <span>Lihat Riwayat Selesai ({{ $historyCount }})</span>
                <span>&rarr;</span>
              </a>
            </div>
          @endif
        </div>
      @else
        <div class="space-y-1">
          <h3 class="font-heading font-black text-lg sm:text-xl text-[#1A1A2E]">Belum Ada Riwayat Selesai</h3>
          <p class="text-xs text-slate-500 max-w-sm mx-auto">
            Belum ada sesi foto yang selesai atau riwayat antrean sebelumnya di perangkat ini.
          </p>
        </div>

        <div class="pt-2 space-y-3">
          @if($activeCount > 0)
            <a href="{{ route('booking.history', ['tab' => 'active']) }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 rounded-full text-xs font-heading font-black uppercase tracking-wider bg-[#1E3A8A] text-white hover:bg-[#2D2DB0] shadow-sm transition-all">
              Lihat Antrean Aktif ({{ $activeCount }}) &rarr;
            </a>
          @else
            <a href="{{ route('packages.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 rounded-full text-xs font-heading font-black uppercase tracking-wider bg-[#1E3A8A] text-white hover:bg-[#2D2DB0] shadow-sm transition-all">
              Ambil Nomor Antrean &rarr;
            </a>
          @endif
        </div>
      @endif
    </div>
  @elseif($bookings->count() === 1)
    @php
      $b = $bookings->first();
      $isActive = in_array($b->status, ['waiting_verification', 'confirmed', 'checked_in', 'in_studio']);
      $isCompleted = ($b->status === 'completed');
      $isMissed = in_array($b->status, ['cancelled', 'no_show']);
    @endphp

    <!-- SINGLE TICKET HERO: FULL-WIDTH RESPONSIVE CARD DI DESKTOP -->
    <div class="bg-white rounded-3xl border border-[#E7E2D8] shadow-soft p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6 hover:border-[#1E3A8A]/30 transition-all">
      
      <!-- SISI KIRI: INFORMASI BOOTH, STATUS & DETAIL -->
      <div class="flex-1 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-[#F0ECE4]">
          <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-2xl bg-[#E8EEF9] text-[#1E3A8A] border border-[#1E3A8A]/20 flex items-center justify-center font-mono font-black text-base shrink-0">
              {{ substr($b->studio_backdrop ?? 'F', 0, 1) }}
            </span>
            <div>
              <span class="text-[10px] font-heading font-bold uppercase text-slate-400 block tracking-wider">STUDIO BOOTH</span>
              <h2 class="font-heading font-black text-lg sm:text-xl text-[#1A1A2E] leading-tight">
                {{ $b->studio_backdrop }}
              </h2>
            </div>
          </div>

          <span class="font-mono font-black text-base px-3 py-1.5 rounded-xl bg-[#E8EEF9] text-[#1E3A8A] border border-[#1E3A8A]/20">
            {{ $b->queue_number }}
          </span>
        </div>

        <!-- STATUS BOX -->
        <div>
          @if($b->status === 'in_studio')
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-1">
              <div class="text-xs font-heading font-black uppercase tracking-wider flex items-center gap-1.5 text-emerald-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span>Sedang Sesi Foto</span>
              </div>
              <p class="text-xs text-emerald-800 font-sans">Nomormu sedang dipanggil masuk ke bilik.</p>
            </div>
          @elseif($isActive)
            @if(($b->ahead_count ?? 0) === 0)
              <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-1">
                <div class="text-xs font-heading font-black uppercase tracking-wider flex items-center gap-1.5 text-emerald-700">
                  <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                  <span>Giliran Berikutnya</span>
                </div>
                <p class="text-xs text-emerald-800 font-sans">Antrean terdepan di bilik ini, bersiaplah.</p>
              </div>
            @else
              <div class="p-3.5 rounded-2xl bg-[#E8EEF9] border border-[#1E3A8A]/20 text-[#1E3A8A] space-y-1">
                <div class="text-xs font-heading font-black uppercase tracking-wider flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-[#1E3A8A] animate-pulse"></span>
                  <span>Dalam Antrian</span>
                </div>
                <p class="text-xs text-slate-700 font-sans">
                  Tersisa {{ $b->ahead_count }} orang lagi di depanmu.
                </p>
              </div>
            @endif
          @elseif($isCompleted)
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 space-y-1">
              <div class="text-xs font-heading font-black uppercase text-slate-600 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                <span>Selesai</span>
              </div>
              <p class="text-xs text-slate-500 font-sans">Sesi foto telah selesai dilaksanakan.</p>
            </div>
          @elseif($isMissed)
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 space-y-1">
              <div class="text-xs font-heading font-black uppercase text-rose-700 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span>Tidak Hadir</span>
              </div>
              <p class="text-xs text-rose-700 font-sans">Nomor antrean telah dilewati.</p>
            </div>
          @endif
        </div>

        <!-- TICKET SPEC DETAILS: GRID TIGA KOLOM LEGA -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 text-xs font-sans">
          <div class="bg-[#FAF9F6] p-3 rounded-2xl border border-[#EBE7E0]">
            <span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal Reservasi</span>
            <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ \Carbon\Carbon::parse($b->schedule->date)->translatedFormat('d M Y') }}</span>
          </div>
          <div class="bg-[#FAF9F6] p-3 rounded-2xl border border-[#EBE7E0]">
            <span class="text-slate-400 block text-[10px] uppercase font-bold">Waktu Booking</span>
            <span class="font-mono font-bold text-slate-800 text-sm mt-0.5 block">{{ $b->created_at->format('H.i') }} WIB</span>
          </div>
          <div class="bg-[#FAF9F6] p-3 rounded-2xl border border-[#EBE7E0]">
            <span class="text-slate-400 block text-[10px] uppercase font-bold">Total Biaya</span>
            <strong class="font-mono font-black text-[#1E3A8A] text-sm mt-0.5 block">Rp {{ number_format($b->total_price, 0, ',', '.') }}</strong>
          </div>
        </div>
      </div>

      <!-- SISI KANAN: NOMOR ANTREAN HERO & TOMBOL BUKA TIKET -->
      <div class="w-full lg:w-80 bg-[#FAF9F6] p-6 rounded-3xl border border-[#EBE7E0] flex flex-col items-center justify-center text-center space-y-4 shrink-0">
        <div>
          <span class="text-[10px] font-heading font-black uppercase tracking-[0.18em] text-slate-400 block">NOMOR ANTREAN</span>
          <div class="text-5xl sm:text-6xl font-mono font-black tracking-tight text-[#1E3A8A] my-1">
            {{ $b->queue_number }}
          </div>
          <span class="text-xs font-heading font-bold text-slate-500">KOTHaK Photo Studio</span>
        </div>

        <a href="{{ route('booking.ticket', ['booking' => $b->id, 'from_history' => 1]) }}" class="w-full kothak-btn-primary py-3.5 px-6 text-xs sm:text-sm font-heading font-black uppercase tracking-wider text-center rounded-2xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer">
          <span>Buka Tiket Digital</span>
          <span>&rarr;</span>
        </a>
      </div>

    </div>
  @else
    <!-- MULTIPLE TICKETS: GRID RESPONSIVE (2 TIKET DIBAGI RATA 50-50 DI DESKTOP AGAR PENUH DAN RAPI) -->
    <div class="grid grid-cols-1 {{ $bookings->count() === 2 ? 'md:grid-cols-2' : 'md:grid-cols-2 lg:grid-cols-3' }} gap-5 sm:gap-6">
      @foreach($bookings as $b)
        @php
          $isActive = in_array($b->status, ['waiting_verification', 'confirmed', 'checked_in', 'in_studio']);
          $isCompleted = ($b->status === 'completed');
          $isMissed = in_array($b->status, ['cancelled', 'no_show']);
        @endphp

        <div class="bg-white rounded-3xl border border-[#E7E2D8] shadow-soft p-5 sm:p-6 flex flex-col justify-between space-y-4 hover:border-[#1E3A8A]/40 transition-all">
          
          <!-- TOP ROW: BOOTH & TICKET CODE -->
          <div>
            <div class="flex items-center justify-between pb-3 border-b border-[#F0ECE4] gap-2">
              <span class="font-heading font-black text-sm sm:text-base text-[#1A1A2E] truncate">
                {{ $b->studio_backdrop }}
              </span>
              <span class="font-mono font-black text-sm sm:text-base px-2.5 py-1 rounded-xl bg-[#E8EEF9] text-[#1E3A8A] border border-[#1E3A8A]/15 shrink-0">
                {{ $b->queue_number }}
              </span>
            </div>

            <!-- STATUS BOX: DYNAMIC (DALAM ANTRIAN, SELESAI, TIDAK HADIR) -->
            <div class="mt-3">
              @if($b->status === 'in_studio')
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-1">
                  <div class="text-[11px] font-heading font-black uppercase tracking-wider flex items-center gap-1.5 text-emerald-700">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>Sedang Sesi Foto</span>
                  </div>
                  <p class="text-xs text-emerald-800 font-sans">Nomormu sedang dipanggil masuk ke bilik.</p>
                </div>
              @elseif($isActive)
                @if(($b->ahead_count ?? 0) === 0)
                  <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-1">
                    <div class="text-[11px] font-heading font-black uppercase tracking-wider flex items-center gap-1.5 text-emerald-700">
                      <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                      <span>Giliran Berikutnya</span>
                    </div>
                    <p class="text-xs text-emerald-800 font-sans">Antrean terdepan di bilik ini, bersiaplah.</p>
                  </div>
                @else
                  <div class="p-3 rounded-xl bg-[#E8EEF9] border border-[#1E3A8A]/20 text-[#1E3A8A] space-y-1">
                    <div class="text-[11px] font-heading font-black uppercase tracking-wider flex items-center gap-1.5">
                      <span class="w-2 h-2 rounded-full bg-[#1E3A8A] animate-pulse"></span>
                      <span>Dalam Antrian</span>
                    </div>
                    <p class="text-xs text-slate-700 font-sans">
                      Tersisa {{ $b->ahead_count }} orang lagi di depanmu.
                    </p>
                  </div>
                @endif
              @elseif($isCompleted)
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 space-y-1">
                  <div class="text-[11px] font-heading font-black uppercase text-slate-600 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    <span>Selesai</span>
                  </div>
                  <p class="text-xs text-slate-500 font-sans">Sesi foto telah selesai dilaksanakan.</p>
                </div>
              @elseif($isMissed)
                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 space-y-1">
                  <div class="text-[11px] font-heading font-black uppercase text-rose-700 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>Tidak Hadir</span>
                  </div>
                  <p class="text-xs text-rose-700 font-sans">Nomor antrean telah dilewati.</p>
                </div>
              @endif
            </div>

            <!-- TICKET SPEC DETAILS -->
            <div class="mt-3.5 space-y-1.5 text-xs text-slate-600 font-sans border-t border-[#F0ECE4] pt-3">
              <div class="flex justify-between items-center">
                <span class="text-slate-400">Tanggal:</span>
                <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($b->schedule->date)->translatedFormat('d M Y') }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-slate-400">Waktu Booking:</span>
                <span class="font-mono text-slate-700">{{ $b->created_at->format('H.i') }} WIB</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-slate-400">Total Biaya:</span>
                <strong class="font-mono font-black text-[#1E3A8A]">Rp {{ number_format($b->total_price, 0, ',', '.') }}</strong>
              </div>
            </div>
          </div>

          <!-- FOOTER ACTION BUTTON -->
          <div class="pt-2">
            <a href="{{ route('booking.ticket', ['booking' => $b->id, 'from_history' => 1]) }}" class="w-full kothak-btn-primary py-3 px-4 text-xs font-heading font-black uppercase tracking-wider text-center rounded-2xl flex items-center justify-center shadow-sm hover:shadow transition-all">
              Buka Tiket &rarr;
            </a>
          </div>

        </div>
      @endforeach
    </div>
  @endif

  </div> <!-- /customerHistoryContainer -->

</div>

@push('scripts')
<script>
  let isHistoryRefreshing = false;

  function fetchLiveHistory() {
    if (isHistoryRefreshing) return;
    isHistoryRefreshing = true;

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

      const newContainer = doc.getElementById('customerHistoryContainer');
      const curContainer = document.getElementById('customerHistoryContainer');
      if (newContainer && curContainer) {
        curContainer.innerHTML = newContainer.innerHTML;
      }
    })
    .catch(() => {})
    .finally(() => {
      isHistoryRefreshing = false;
    });
  }

  // Cross-tab broadcast listener untuk respon seketika (0 ms)
  try {
    if ('BroadcastChannel' in window) {
      const liveQueueChannel = new BroadcastChannel('kothak_live_queue');
      liveQueueChannel.onmessage = fetchLiveHistory;
    }
  } catch(e) {}
  window.addEventListener('storage', function(e) {
    if (e.key === 'kothak_queue_bump') fetchLiveHistory();
  });

  // Polling data otomatis setiap 2 detik
  setInterval(fetchLiveHistory, 2000);
</script>
@endpush
@endsection
