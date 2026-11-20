@extends('layouts.app')

@section('title', 'Katalog Paket Foto & Antrean Pintar — KOTHaK Photo Studio')

@section('content')
<div class="space-y-10 sm:space-y-14">

  <!-- HERO SECTION -->
  <section class="bg-blue-700 text-white border-2 border-slate-900 shadow-[6px_6px_0px_#0F172A] relative overflow-hidden p-6 sm:p-10 lg:p-12">
    <div class="checker-white absolute inset-0 pointer-events-none"></div>
    <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

      <!-- Left Column: Story & CTA -->
      <div class="lg:col-span-7 space-y-4 sm:space-y-6">
        <div class="inline-flex items-center gap-2 bg-slate-900 text-white border-2 border-white px-3 py-1 text-xs font-black uppercase tracking-widest">
          <span class="w-2.5 h-2.5 bg-emerald-400 rounded-full animate-ping"></span>
          <span>Studio Photobox #1 di Purbalingga</span>
        </div>
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.08]">
          Coffee. Photo. <span class="font-editorial italic font-normal text-blue-200 underline decoration-2 underline-offset-8">Memory.</span>
        </h1>
        <p class="text-sm sm:text-base text-blue-100 max-w-xl leading-relaxed font-medium">
          Studio photobox estetik di Purbalingga dengan sistem antrean pintar & reservasi slot online. Pilih paket fotomu, kunci jadwal tanpa takut bentrok, dan bayar praktis via QRIS Statis.
        </p>
        <div class="flex flex-wrap items-center gap-3 pt-2">
          <a href="#packagesSection" class="neo-btn bg-white text-blue-700 hover:bg-blue-50 px-6 py-3.5 text-xs sm:text-sm font-black uppercase tracking-widest text-center">
            PILIH PAKET FOTO ➔
          </a>
          <a href="{{ route('queue.display') }}" target="_blank" class="neo-btn bg-slate-900 text-white hover:bg-slate-800 px-5 py-3.5 text-xs sm:text-sm font-bold uppercase tracking-wider text-center flex items-center gap-1.5">
            <span>📺</span> Buka Layar Antrean TV
          </a>
        </div>
        <!-- Info Badges -->
        <div class="pt-4 border-t border-blue-500/50 grid grid-cols-3 gap-2 sm:gap-4 text-[11px] sm:text-xs">
          <div>
            <div class="text-blue-200 font-bold uppercase">LOKASI</div>
            <div class="font-bold text-white mt-0.5">Purbalingga Kota</div>
          </div>
          <div>
            <div class="text-blue-200 font-bold uppercase">OPERASIONAL</div>
            <div class="font-bold text-white mt-0.5">09:00 - 23:00 WIB</div>
          </div>
          <div>
            <div class="text-blue-200 font-bold uppercase">PEMBAYARAN</div>
            <div class="font-bold text-white mt-0.5">QRIS Statis Semua E-Wallet</div>
          </div>
        </div>
      </div>

      <!-- Right Column: Live Smart Queue Monitor Widget (FR-04) -->
      <div class="lg:col-span-5">
        <div class="neo-box bg-[#F5F4EE] text-slate-900 p-5 sm:p-6" id="liveQueueWidget">
          <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-4">
            <div>
              <span class="text-[10px] font-black uppercase tracking-widest text-blue-700 block">SMART QUEUE LIVE</span>
              <h3 class="text-lg font-black text-slate-900">Status Antrean Hari Ini</h3>
            </div>
            <span class="bg-emerald-500 text-slate-900 border-2 border-slate-900 px-2.5 py-1 text-[11px] font-black uppercase tracking-wider flex items-center gap-1">
              <span class="w-2 h-2 rounded-full bg-white animate-ping"></span> LIVE
            </span>
          </div>

          <div class="grid grid-cols-2 gap-3 mb-4">
            <!-- In Studio -->
            <div class="bg-blue-700 text-white border-2 border-slate-900 p-3.5 shadow-[3px_3px_0px_#0F172A]">
              <div class="text-[9px] font-black uppercase tracking-wider text-blue-200">SEDANG DI STUDIO</div>
              <div id="widgetInStudioNumber" class="text-2xl sm:text-3xl font-black font-mono tracking-tight mt-1 text-amber-300">
                {{ $inStudio?->queue_number ?? 'SIAP' }}
              </div>
              <div id="widgetInStudioName" class="text-[11px] font-bold text-blue-100 truncate mt-0.5">
                {{ $inStudio ? $inStudio->user->name : 'Studio Kosong / Siap' }}
              </div>
            </div>

            <!-- Next Queue -->
            <div class="bg-white text-slate-900 border-2 border-slate-900 p-3.5 shadow-[3px_3px_0px_#0F172A]">
              <div class="text-[9px] font-black uppercase tracking-wider text-slate-500">BERIKUTNYA</div>
              <div id="widgetNextNumber" class="text-2xl sm:text-3xl font-black font-mono tracking-tight mt-1 text-blue-700">
                {{ $callingNext?->queue_number ?? 'TIDAK ADA' }}
              </div>
              <div id="widgetNextName" class="text-[11px] font-bold text-slate-700 truncate mt-0.5">
                {{ $callingNext ? $callingNext->user->name : 'Semua terlayani' }}
              </div>
            </div>
          </div>

          <!-- Booth status bars -->
          <div class="bg-white border-2 border-slate-900 p-3 mb-3 text-xs space-y-2">
            <div class="flex items-center justify-between">
              <span class="font-bold text-slate-700">Booth A (Electric Blue)</span>
              <span class="font-mono text-[10px] font-black px-2 py-0.5 border {{ $inStudio ? 'bg-amber-100 text-amber-900 border-amber-400' : 'bg-emerald-100 text-emerald-800 border-emerald-400' }}">
                {{ $inStudio ? 'SESI BERLANGSUNG' : 'READY / SIAP' }}
              </span>
            </div>
            <div class="flex items-center justify-between">
              <span class="font-bold text-slate-700">Booth B (Warm Cream)</span>
              <span class="font-mono text-[10px] font-black px-2 py-0.5 border bg-emerald-100 text-emerald-800 border-emerald-400">
                READY / SIAP
              </span>
            </div>
          </div>

          <div class="flex items-center justify-between text-xs pt-1">
            <span class="text-slate-600 font-bold">Antrean Menunggu: <strong id="widgetWaitingCount" class="text-blue-700 font-mono">{{ $waitingCount }}</strong></span>
            <a href="{{ route('booking.history') }}" class="font-black text-blue-700 hover:underline uppercase text-[11px]">
              Cek Tiket Saya ➔
            </a>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- STEPPER PROCESS BAR -->
  <section class="border-2 border-slate-900 bg-white p-4 sm:p-6 shadow-[4px_4px_0px_#1D4ED8]">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <div class="border-2 border-slate-900 bg-[#F5F4EE] p-3 flex items-start gap-2.5">
        <span class="bg-blue-700 text-white font-mono font-black text-xs px-2 py-1 border border-slate-900">01</span>
        <div>
          <div class="font-black text-xs uppercase text-slate-900">Pilih Paket</div>
          <div class="text-[11px] text-slate-600 mt-0.5">Tentukan paket photobox & add-ons.</div>
        </div>
      </div>
      <div class="border-2 border-slate-900 bg-[#F5F4EE] p-3 flex items-start gap-2.5">
        <span class="bg-blue-700 text-white font-mono font-black text-xs px-2 py-1 border border-slate-900">02</span>
        <div>
          <div class="font-black text-xs uppercase text-slate-900">Kunci Jadwal Slot</div>
          <div class="text-[11px] text-slate-600 mt-0.5">Pilih tanggal & jam anti double-booking.</div>
        </div>
      </div>
      <div class="border-2 border-slate-900 bg-[#F5F4EE] p-3 flex items-start gap-2.5">
        <span class="bg-blue-700 text-white font-mono font-black text-xs px-2 py-1 border border-slate-900">03</span>
        <div>
          <div class="font-black text-xs uppercase text-slate-900">Bayar QRIS Statis</div>
          <div class="text-[11px] text-slate-600 mt-0.5">Scan barcode & upload bukti transfer.</div>
        </div>
      </div>
      <div class="border-2 border-slate-900 bg-[#F5F4EE] p-3 flex items-start gap-2.5">
        <span class="bg-blue-700 text-white font-mono font-black text-xs px-2 py-1 border border-slate-900">04</span>
        <div>
          <div class="font-black text-xs uppercase text-slate-900">Tiket & Antrean</div>
          <div class="text-[11px] text-slate-600 mt-0.5">Dapat no. antrean & pantau giliran.</div>
        </div>
      </div>
    </div>
  </section>

  <!-- SECTION 1: KATALOG PAKET FOTO (FR-07) -->
  <section id="packagesSection" class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b-2 border-slate-900 pb-4">
      <div>
        <span class="text-xs font-black uppercase tracking-[0.2em] text-blue-700">STUDIO PHOTOBOX CATALOG</span>
        <h2 class="text-2xl sm:text-4xl font-black text-slate-900 mt-1">
          Koleksi Paket Foto <span class="font-editorial italic font-normal text-blue-700">Kothak</span>
        </h2>
        <p class="text-xs sm:text-sm text-slate-600 mt-1">
          Semua sesi mencakup pencahayaan studio profesional, properti gratis, cetak foto instan, dan soft file HD.
        </p>
      </div>

      <!-- Category Filter Tabs -->
      <div class="flex flex-wrap gap-2 text-xs font-black uppercase">
        <button type="button" onclick="filterPackages('ALL')" class="filter-tab px-3 py-1.5 border-2 border-slate-900 bg-blue-700 text-white shadow-[2px_2px_0px_#0F172A]" data-cat="ALL">Semua ({{ $packages->count() }})</button>
        <button type="button" onclick="filterPackages('SOLO_DUO')" class="filter-tab px-3 py-1.5 border-2 border-slate-900 bg-white text-slate-900 hover:bg-slate-100" data-cat="SOLO_DUO">Solo / Duo</button>
        <button type="button" onclick="filterPackages('GROUP')" class="filter-tab px-3 py-1.5 border-2 border-slate-900 bg-white text-slate-900 hover:bg-slate-100" data-cat="GROUP">Grup & Bestie</button>
        <button type="button" onclick="filterPackages('BUNDLING')" class="filter-tab px-3 py-1.5 border-2 border-slate-900 bg-white text-slate-900 hover:bg-slate-100" data-cat="BUNDLING">Bundle Kopi</button>
      </div>
    </div>

    <!-- PACKAGES GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach($packages as $pkg)
      <div class="package-card neo-box bg-white flex flex-col justify-between" data-category="{{ $pkg->category }}">
        <div>
          <!-- Card Header -->
          <div class="p-5 border-b-2 border-slate-900 bg-[#F5F4EE] flex items-center justify-between">
            <span class="bg-blue-700 text-white text-[10px] font-black uppercase px-2.5 py-1 tracking-wider border border-slate-900">
              {{ $pkg->badge ?? 'PHOTOBOX' }}
            </span>
            <div class="text-right">
              <span class="text-[10px] font-bold text-slate-500 uppercase">Durasi:</span>
              <span class="font-mono font-black text-xs text-slate-900 ml-1">{{ $pkg->duration }} Menit</span>
            </div>
          </div>

          <!-- Card Body -->
          <div class="p-5 space-y-3">
            <h3 class="text-xl font-black text-slate-900">{{ $pkg->name }}</h3>
            <p class="text-xs text-slate-600 leading-relaxed min-h-[48px]">
              {{ $pkg->description }}
            </p>

            <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs text-slate-700">
              <span class="flex items-center gap-1 font-bold">
                <span>👥</span> Kapasitas: <strong>1 - {{ $pkg->max_participants }} Orang</strong>
              </span>
              <span class="font-mono text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 border border-emerald-200">
                Studio Ready
              </span>
            </div>
          </div>
        </div>

        <!-- Card Footer -->
        <div class="p-5 border-t-2 border-slate-900 bg-slate-50 flex items-center justify-between gap-3">
          <div>
            <div class="text-[10px] font-bold uppercase text-slate-500">Harga Paket:</div>
            <div class="text-2xl font-black font-mono text-blue-700">
              Rp {{ number_format($pkg->price, 0, ',', '.') }}
            </div>
          </div>
          <a href="{{ route('schedule.select', $pkg->id) }}" class="neo-btn bg-blue-700 hover:bg-blue-800 text-white px-4 py-2.5 text-xs font-black uppercase tracking-wider text-center shrink-0">
            PILIH JADWAL ➔
          </a>
        </div>
      </div>
      @endforeach
    </div>
  </section>

  <!-- SECTION 2: LAYANAN TAMBAHAN (ADD-ONS) -->
  <section class="border-2 border-slate-900 bg-white p-6 sm:p-8 shadow-[4px_4px_0px_#0F172A]">
    <div class="border-b-2 border-slate-900 pb-3 mb-6">
      <span class="text-xs font-black uppercase tracking-[0.2em] text-blue-700">LAYANAN TAMBAHAN (OPTIONAL ADD-ONS)</span>
      <h3 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">Upgrade Pengalaman Fotomu</h3>
      <p class="text-xs text-slate-600 mt-1">Layanan ekstra ini dapat ditambahkan langsung saat kamu memilih jadwal sesi foto.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach($addOns as $ao)
      <div class="border-2 border-slate-900 bg-[#F5F4EE] p-4 flex flex-col justify-between">
        <div>
          <div class="font-black text-sm text-slate-900">{{ $ao->name }}</div>
          <p class="text-[11px] text-slate-600 mt-1">{{ $ao->description }}</p>
        </div>
        <div class="mt-3 pt-2 border-t border-slate-300 font-mono font-black text-blue-700 text-sm">
          + Rp {{ number_format($ao->price, 0, ',', '.') }}
        </div>
      </div>
      @endforeach
    </div>
  </section>

  <!-- SECTION 3: TABEL ANTREAN PINTAR LIVE (FR-04) -->
  <section class="border-2 border-slate-900 bg-white shadow-[6px_6px_0px_#1D4ED8] overflow-hidden">
    <div class="bg-blue-700 text-white px-5 py-4 border-b-2 border-slate-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h3 class="text-lg font-black uppercase tracking-wider">Antrean Photobox Hari Ini</h3>
        <p class="text-xs text-blue-100">Pantau pergerakan antrean studio secara real-time dari ponselmu.</p>
      </div>
      <a href="{{ route('queue.display') }}" target="_blank" class="neo-btn bg-white text-blue-900 hover:bg-blue-50 px-3 py-1.5 text-xs font-black uppercase tracking-wider shrink-0 text-center">
        Buka Mode Layar Penuh (TV Display) ↗
      </a>
    </div>

    <!-- Desktop Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-[#F5F4EE] border-b-2 border-slate-900 uppercase font-black text-slate-700">
            <th class="p-3.5">No. Antrean</th>
            <th class="p-3.5">Pelanggan</th>
            <th class="p-3.5">Paket Foto</th>
            <th class="p-3.5">Slot Waktu</th>
            <th class="p-3.5">Status</th>
          </tr>
        </thead>
        <tbody id="liveQueueTableBody" class="divide-y-2 divide-slate-900">
          @forelse($activeQueues as $q)
          <tr class="hover:bg-slate-50 transition-colors {{ $q->status === 'in_studio' ? 'bg-amber-50 font-bold' : '' }}">
            <td class="p-3.5 font-mono font-black text-blue-700 text-sm">{{ $q->queue_number }}</td>
            <td class="p-3.5 font-bold text-slate-900">{{ $q->user->name }}</td>
            <td class="p-3.5">{{ $q->package->name }}</td>
            <td class="p-3.5 font-mono text-slate-600">{{ $q->schedule?->formatted_time ?? '-' }}</td>
            <td class="p-3.5">
              <span class="border px-2 py-0.5 text-[10px] font-black uppercase {{ $q->status_badge_class }}">
                {{ $q->status_label }}
              </span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="p-8 text-center text-slate-500 font-bold">
              Belum ada antrean untuk hari ini. Jadilah yang pertama reservasi!
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

</div>

@push('scripts')
<script>
  function filterPackages(category) {
    const cards = document.querySelectorAll('.package-card');
    const tabs = document.querySelectorAll('.filter-tab');

    tabs.forEach(t => {
      if (t.dataset.cat === category) {
        t.className = 'filter-tab px-3 py-1.5 border-2 border-slate-900 bg-blue-700 text-white shadow-[2px_2px_0px_#0F172A]';
      } else {
        t.className = 'filter-tab px-3 py-1.5 border-2 border-slate-900 bg-white text-slate-900 hover:bg-slate-100';
      }
    });

    cards.forEach(card => {
      if (category === 'ALL' || card.dataset.category === category) {
        card.style.display = 'flex';
      } else {
        card.style.display = 'none';
      }
    });
  }

  // Real-time Queue Polling every 8 seconds
  setInterval(async function() {
    try {
      const res = await fetch("{{ route('api.queue.status') }}");
      if (!res.ok) return;
      const data = await res.json();

      const inStudioNum = document.getElementById('widgetInStudioNumber');
      const inStudioName = document.getElementById('widgetInStudioName');
      const nextNum = document.getElementById('widgetNextNumber');
      const nextName = document.getElementById('widgetNextName');
      const waitingCount = document.getElementById('widgetWaitingCount');

      if (inStudioNum) inStudioNum.innerText = data.in_studio ? data.in_studio.queue_number : 'SIAP';
      if (inStudioName) inStudioName.innerText = data.in_studio ? data.in_studio.customer_name : 'Studio Kosong / Siap';
      if (nextNum) nextNum.innerText = data.next_queue ? data.next_queue.queue_number : 'TIDAK ADA';
      if (nextName) nextName.innerText = data.next_queue ? data.next_queue.customer_name : 'Semua terlayani';
      if (waitingCount) waitingCount.innerText = data.waiting_count;
    } catch(e) {
      console.log('Poll queue error:', e);
    }
  }, 8000);
</script>
@endpush
@endsection
