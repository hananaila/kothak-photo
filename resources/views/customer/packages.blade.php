@extends('layouts.app')

@section('title', 'Ambil Antrean Photobox — KOTHaK Photo Studio')

@section('content')
<div class="w-full space-y-6 sm:space-y-8">

  <!-- HERO BANNER: Responsive Mobile, Tablet & Laptop -->
  <section class="bg-gradient-to-br from-[#1E3A8A] via-[#1E3A8A] to-[#2D2DB0] text-white rounded-2xl sm:rounded-3xl border border-white/10 shadow-soft relative overflow-hidden p-6 sm:p-8 lg:p-10">
    <div class="absolute -top-20 -right-20 w-64 h-64 bg-[#2D2DB0] rounded-full blur-3xl opacity-40 pointer-events-none"></div>
    <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-blue-400 rounded-full blur-3xl opacity-20 pointer-events-none"></div>

    <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
      <div class="space-y-3 max-w-xl">
        <div class="inline-flex items-center gap-1.5 bg-white/10 backdrop-blur-md text-white border border-white/20 px-3 py-1 rounded-full text-[11px] font-heading font-bold uppercase tracking-wider">
          <span>Studio Photobox &bull; Purbalingga</span>
        </div>

        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-heading font-black tracking-tight leading-tight uppercase">
          Self Photobox. <br />
          <span class="font-serif italic font-normal text-[#F5F1E8] lowercase">capture your memories.</span>
        </h1>

        <p class="text-xs sm:text-sm text-white/90 leading-relaxed font-sans">
          Ambil antrean langsung dari ponselmu tanpa perlu registrasi akun. Cukup pilih booth, isi nama, dan langsung dapatkan nomor antreanmu.
        </p>
      </div>

      <!-- Quick info card on banner -->
      <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 sm:p-5 text-xs text-white space-y-2 shrink-0 md:w-64">
        <div class="flex items-center justify-between text-[11px] font-heading font-bold uppercase text-white/80">
          <span>STATUS STUDIO</span>
          <span class="text-emerald-300 font-bold">Buka</span>
        </div>
        <div class="font-mono text-sm font-bold">09.00 — 23.00 WIB</div>
        <div class="pt-2 border-t border-white/15 flex items-center justify-between text-[11px]">
          <span class="text-white/80">Antrean Aktif:</span>
          <span class="font-mono font-black text-amber-300" id="heroWaitingCount">{{ array_sum($boothQueues) }} Antrean</span>
        </div>
      </div>
    </div>
  </section>

  <!-- FORM AMBIL ANTREAN: FULL-WIDTH RESPONSIVE (MENGIKUTI LEBAR HERO BANNER) -->
  <form action="{{ route('booking.store') }}" method="POST" id="mainBookingForm" onsubmit="return validateBookingForm()" class="w-full space-y-6 sm:space-y-8">
    @csrf
    <input type="hidden" name="package_id" id="hiddenPackageId" value="{{ $defaultPackage?->id }}" />
    <input type="hidden" name="schedule_id" value="{{ $todaySchedule?->id }}" />

    <!-- LANGKAH 01: PILIH BOOTH (TERHUBUNG OTOMATIS DENGAN KELOLA PAKET ADMIN) -->
    <section class="w-full bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-7 lg:p-8 border border-[#E7E2D8] shadow-[0_4px_20px_-4px_rgba(26,26,46,0.05),0_1px_3px_rgba(26,26,46,0.02)] space-y-5">
      <div class="border-b border-[#F0ECE4] pb-4">
        <span class="text-[11px] sm:text-xs font-heading font-black uppercase tracking-wider text-[#1E3A8A]">LANGKAH 01 &bull; PILIH STUDIO BOOTH</span>
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-heading font-black text-[#1A1A2E] mt-1">
          Pilih Booth <span class="font-serif italic font-normal text-[#1E3A8A] lowercase">fotomu</span>
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 font-sans mt-1">
          Sentuh atau klik salah satu booth untuk memilih. Klik lagi jika ingin membatalkan pilihan.
        </p>
      </div>

      <!-- Booth Cards: Dinamis dari Database Paket Studio Admin -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5" id="boothListContainer">
        @forelse($packages as $pkg)
        <div
          class="booth-card relative flex flex-col justify-between p-5 sm:p-6 rounded-2xl border-2 cursor-pointer transition-all duration-200 select-none border-[#E5E1DA] bg-white hover:border-[#1E3A8A]/40 shadow-xs group"
          data-booth="{{ $pkg->name }}"
          data-package-id="{{ $pkg->id }}"
          onclick="selectBoothCard(this)"
        >
          <input
            type="radio"
            name="studio_backdrop"
            id="radio_pkg_{{ $pkg->id }}"
            value="{{ $pkg->name }}"
            class="sr-only booth-radio"
          />

          <!-- Top row: Waiting Counter Box & Circular Checkmark -->
          <div class="flex items-center justify-between gap-3 mb-4">
            <div class="booth-waiting-box px-3 py-1.5 rounded-xl bg-[#E8EEF9] text-[#1E3A8A] border border-[#1E3A8A]/25 flex items-center gap-2 shrink-0 shadow-xs transition-all">
              <span class="font-mono font-black text-base sm:text-lg leading-none text-[#1E3A8A] booth-waiting-num" id="boothCount_{{ $pkg->id }}">
                {{ $boothQueues[$pkg->name] ?? 0 }}
              </span>
              <span class="text-[8px] sm:text-[9px] font-heading font-black uppercase tracking-wider text-[#1E3A8A]">WAITING</span>
            </div>

            <div class="booth-check-circle w-7 h-7 sm:w-8 sm:h-8 rounded-full border-2 border-slate-300 text-transparent flex items-center justify-center shrink-0 transition-colors">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            </div>
          </div>

          <!-- Middle: Booth Title & Operational Hours -->
          <div class="space-y-1 mb-4">
            <div>
              <h3 class="font-heading font-black text-base sm:text-lg text-[#1A1A2E] leading-snug group-hover:text-[#1E3A8A] transition-colors">
                {{ $pkg->name }}
              </h3>
            </div>
            <p class="text-xs text-slate-500 font-sans">
              {{ $pkg->operational_hours ?: 'Buka 09.00 — 23.00 WIB' }}
            </p>
          </div>

          <!-- Bottom: Pricing & Prints Included -->
          <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
            <span class="font-mono font-black text-sm sm:text-base text-[#1E3A8A]">
              Rp {{ number_format($pkg->price, 0, ',', '.') }}
            </span>
            <span class="text-[11px] text-slate-600 font-medium">
              {{ $pkg->prints_included ?: '2 Strip + Soft File' }}
            </span>
          </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-slate-500 font-medium text-xs bg-slate-50 rounded-2xl border border-dashed border-slate-300">
          Belum ada bilik booth aktif saat ini.
        </div>
        @endforelse
      </div>
    </section>

    <!-- LANGKAH 02: DATA DIRI PEMESAN (FULL-WIDTH 3-COLUMN INPUTS ON DESKTOP) -->
    <section class="w-full bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-7 lg:p-8 border border-[#E7E2D8] shadow-[0_4px_20px_-4px_rgba(26,26,46,0.05),0_1px_3px_rgba(26,26,46,0.02)] space-y-5">
      <div class="border-b border-[#F0ECE4] pb-4">
        <span class="text-[11px] sm:text-xs font-heading font-black uppercase tracking-wider text-[#1E3A8A]">LANGKAH 02 &bull; DATA DIRI PEMESAN</span>
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-heading font-black text-[#1A1A2E] mt-1">
          Informasi <span class="font-serif italic font-normal text-[#1E3A8A] lowercase">pemesan &amp; tiket</span>
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 font-sans mt-1">
          Cukup isi nama &amp; kontak. Nomor antrean dan notifikasi giliran akan langsung aktif di layarmu.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
        <div>
          <label for="customerName" class="block text-xs font-heading font-bold uppercase tracking-wider mb-1.5 text-[#1A1A2E]">
            Nama Pemesan *
          </label>
          <input
            type="text"
            id="customerName"
            name="name"
            value="{{ (Auth::check() && Auth::user()->role === 'customer') ? Auth::user()->name : old('name') }}"
            required
            placeholder="Contoh: Hana Naila"
            class="w-full rounded-xl border border-slate-300 bg-white p-3 text-sm font-bold text-[#1A1A2E] focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20 focus:border-[#1E3A8A] shadow-sm"
          />
        </div>

        <div>
          <label for="customerPhone" class="block text-xs font-heading font-bold uppercase tracking-wider mb-1.5 text-[#1A1A2E]">
            Nomor Handphone / WA *
          </label>
          <input
            type="tel"
            id="customerPhone"
            name="phone_whatsapp"
            value="{{ (Auth::check() && Auth::user()->role === 'customer') ? Auth::user()->phone_whatsapp : old('phone_whatsapp') }}"
            required
            placeholder="Contoh: 081234567890"
            class="w-full rounded-xl border border-slate-300 bg-white p-3 text-sm font-mono font-bold text-[#1A1A2E] focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20 focus:border-[#1E3A8A] shadow-sm"
          />
        </div>

        <div>
          <label for="bookingNotes" class="block text-xs font-heading font-bold uppercase tracking-wider mb-1.5 text-[#1A1A2E]">
            Catatan Tambahan (Opsional)
          </label>
          <input
            type="text"
            id="bookingNotes"
            name="notes"
            placeholder="Contoh: Butuh kursi"
            class="w-full rounded-xl border border-slate-300 bg-white p-3 text-sm font-medium text-[#1A1A2E] focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]/20 focus:border-[#1E3A8A] shadow-sm"
          />
        </div>
      </div>

      <!-- TOMBOL AMBIL ANTREAN: FULL-WIDTH CTA -->
      <div class="pt-2">
        <button
          type="submit"
          id="submitBookingBtn"
          disabled
          class="submitBookingBtn w-full bg-slate-200 text-slate-400 py-4 px-6 text-xs sm:text-sm font-heading font-black uppercase tracking-wider text-center rounded-2xl cursor-not-allowed shrink-0 transition-all shadow-sm"
        >
          Pilih Booth Terlebih Dahulu
        </button>
      </div>
    </section>

  </form>

</div>

@push('scripts')
<script>
  let currentSelectedBooth = null;

  function selectBoothCard(cardEl) {
    if (!cardEl) return;
    const boothName = cardEl.getAttribute('data-booth');
    const packageId = cardEl.getAttribute('data-package-id');

    if (currentSelectedBooth === boothName) {
      // Sentuh lagi untuk membatalkan pilihan (toggle off)
      currentSelectedBooth = null;
      const pkgInput = document.getElementById('hiddenPackageId');
      if (pkgInput) pkgInput.value = '';
    } else {
      currentSelectedBooth = boothName;
      const pkgInput = document.getElementById('hiddenPackageId');
      if (pkgInput && packageId) pkgInput.value = packageId;
    }
    renderBoothUI();
  }

  // Alias untuk kompatibilitas
  function handleBoothClick(boothName, packageId) {
    if (typeof boothName === 'object' && boothName !== null && boothName.getAttribute) {
      return selectBoothCard(boothName);
    }
    currentSelectedBooth = boothName;
    const pkgInput = document.getElementById('hiddenPackageId');
    if (pkgInput && packageId) pkgInput.value = packageId;
    renderBoothUI();
  }

  function renderBoothUI() {
    const cards = document.querySelectorAll('.booth-card');
    const submitBtns = document.querySelectorAll('.submitBookingBtn');

    cards.forEach(card => {
      const booth = card.getAttribute('data-booth');
      const radio = card.querySelector('.booth-radio');
      const circle = card.querySelector('.booth-check-circle');
      const title = card.querySelector('h3');

      if (booth === currentSelectedBooth) {
        if (radio) radio.checked = true;
        card.classList.remove('border-[#E5E1DA]', 'border-slate-200', 'shadow-xs', 'shadow-sm');
        card.classList.add('border-[#1E3A8A]', 'ring-2', 'ring-[#1E3A8A]/20', 'shadow-md');

        if (title) title.classList.add('text-[#1E3A8A]');

        if (circle) {
          circle.classList.remove('border-slate-300', 'text-transparent');
          circle.classList.add('bg-[#1E3A8A]', 'text-white', 'border-[#1E3A8A]');
        }
      } else {
        if (radio) radio.checked = false;
        card.classList.remove('border-[#1E3A8A]', 'ring-2', 'ring-[#1E3A8A]/20', 'shadow-md');
        card.classList.add('border-[#E5E1DA]', 'shadow-xs', 'bg-white');

        if (title) title.classList.remove('text-[#1E3A8A]');

        if (circle) {
          circle.classList.remove('bg-[#1E3A8A]', 'text-white', 'border-[#1E3A8A]');
          circle.classList.add('border-slate-300', 'text-transparent');
        }
      }
    });

    if (currentSelectedBooth) {
      submitBtns.forEach(btn => {
        btn.disabled = false;
        btn.className = 'submitBookingBtn w-full kothak-btn-primary py-4 px-6 text-xs sm:text-sm font-heading font-black uppercase tracking-wider text-center rounded-2xl shadow-md hover:shadow-lg transition-all shrink-0 cursor-pointer';
        btn.innerHTML = 'Ambil Nomor Antrean &rarr;';
      });
    } else {
      submitBtns.forEach(btn => {
        btn.disabled = true;
        btn.className = 'submitBookingBtn w-full bg-slate-200 text-slate-400 py-4 px-6 text-xs sm:text-sm font-heading font-black uppercase tracking-wider text-center rounded-2xl cursor-not-allowed shrink-0 transition-all shadow-sm';
        btn.innerHTML = 'Pilih Booth Terlebih Dahulu';
      });
    }
  }

  function validateBookingForm() {
    if (!currentSelectedBooth) {
      showKothakAlert({
        title: 'Pilih Booth Terlebih Dahulu',
        message: 'Silakan sentuh atau klik salah satu bilik booth foto sebelum mengambil nomor antrean.',
        type: 'warning',
        buttonText: 'Mengerti'
      });
      return false;
    }
    return true;
  }

  // Real-time Queue Polling & Instant Broadcast Sync (Pembaruan Dinamis Tanpa Refresh)
  let isCustomerRefreshing = false;

  async function pollQueueData() {
    if (isCustomerRefreshing) return;
    isCustomerRefreshing = true;

    try {
      const url = window.location.pathname + '?_t=' + Date.now();
      const res = await fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Cache-Control': 'no-cache, no-store, must-revalidate',
          'Pragma': 'no-cache'
        },
        cache: 'no-store'
      });
      if (!res.ok) return;
      const html = await res.text();
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');

      // 1. Update hero waiting counter
      const newHero = doc.getElementById('heroWaitingCount');
      const curHero = document.getElementById('heroWaitingCount');
      if (newHero && curHero) {
        curHero.innerHTML = newHero.innerHTML;
      }

      // 2. Update booth list cards (sinkron langsung saat admin tambah/ubah booth atau antrean bertambah)
      const newBoothContainer = doc.getElementById('boothListContainer');
      const curBoothContainer = document.getElementById('boothListContainer');
      if (newBoothContainer && curBoothContainer) {
        curBoothContainer.innerHTML = newBoothContainer.innerHTML;
        renderBoothUI(); // mempertahankan pilihan booth pelanggan saat ini
      }
    } catch(e) {
      console.log('Customer live poll error:', e);
    } finally {
      isCustomerRefreshing = false;
    }
  }

  // Cross-tab broadcast listener for instant sync (0 milidetik antar-tab)
  try {
    if ('BroadcastChannel' in window) {
      const liveQueueChannel = new BroadcastChannel('kothak_live_queue');
      liveQueueChannel.onmessage = pollQueueData;
    }
  } catch(e) {}
  window.addEventListener('storage', function(e) {
    if (e.key === 'kothak_queue_bump') pollQueueData();
  });

  // Polling data otomatis setiap 1.5 detik (Real-Time Ultra Responsif)
  setInterval(pollQueueData, 1500);
</script>
@endpush
@endsection
