@extends('layouts.app')

@section('title', 'Katalog Paket Foto & Antrean Pintar — KOTHaK Photo')

@section('content')
<div id="viewCustomer" class="space-y-0">

  <!-- HERO SECTION -->
  <section id="heroSection" class="bg-blue-700 text-white border-b-2 border-slate-900 relative overflow-hidden -mx-3 sm:-mx-4 md:-mx-6">
    <div class="checker-white absolute inset-0 pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-16 relative z-10">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-center">

        <!-- Left Column: Brand Story & CTA -->
        <div class="lg:col-span-7 space-y-4 sm:space-y-6">
          <div class="inline-flex items-center gap-2 bg-slate-900 text-white border-2 border-white px-2.5 py-1 text-[10px] sm:text-xs font-extrabold uppercase tracking-widest">
            <span class="w-2 h-2 bg-emerald-400 animate-ping shrink-0"></span>
            <span>#1 Photobox Cafe in Purbalingga</span>
          </div>
          <h1 class="text-3xl sm:text-4xl lg:text-6xl font-extrabold tracking-tight leading-[1.06]">
            Coffee. Photo. <span class="font-editorial italic font-normal text-blue-200 underline decoration-2 underline-offset-4 sm:underline-offset-8">Memory.</span>
          </h1>
          <p class="text-sm sm:text-base lg:text-lg text-blue-100 max-w-2xl leading-relaxed font-medium">
            Cafe photobox di Purbalingga — <span class="italic">where every moment finds its frame</span>. Pilih paket studio estetik, kunci slot jadwal, scan QRIS statis, dan pantau antrean secara real-time.
          </p>
          <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-2">
            <a href="#packagesSection" class="bg-white text-blue-700 hover:bg-blue-50 border-2 border-slate-900 px-5 py-3 text-xs sm:text-sm font-extrabold uppercase tracking-widest shadow-[4px_4px_0px_#0F172A] transition-transform hover:-translate-y-0.5 text-center">PILIH PAKET FOTO ➔</a>
            <a href="#queueSection" class="bg-transparent text-white hover:bg-blue-800 border-2 border-white px-5 py-3 text-xs sm:text-sm font-extrabold uppercase tracking-widest transition-colors text-center">LIHAT SMART QUEUE</a>
          </div>

          <!-- Info row: hidden on smallest screens to save space -->
          <div class="pt-4 border-t border-blue-500/60 hidden sm:grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
              <div class="text-blue-200 uppercase font-bold tracking-wider">LOKASI</div>
              <div class="font-semibold text-white mt-0.5">Jl. AW Sumarmo No.39, Purbalingga</div>
            </div>
            <div>
              <div class="text-blue-200 uppercase font-bold tracking-wider">JAM OPERASIONAL</div>
              <div class="font-semibold text-white mt-0.5">09:00 AM – 11:00 PM</div>
            </div>
            <div>
              <div class="text-blue-200 uppercase font-bold tracking-wider">FASILITAS</div>
              <div class="font-semibold text-white mt-0.5">2 Background + Cetak & Soft File</div>
            </div>
          </div>
        </div>

        <!-- Right Column: Live Smart Queue Monitor Card -->
        <div class="lg:col-span-5">
          <div class="bg-[#F5F4EE] text-slate-900 border-2 border-slate-900 shadow-[6px_6px_0px_#0F172A] sm:shadow-[8px_8px_0px_#0F172A] p-4 sm:p-6">
            <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-4 gap-2">
              <div class="min-w-0">
                <span class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-widest text-blue-700 block">REAL-TIME MONITOR</span>
                <h2 class="text-base sm:text-lg font-extrabold text-slate-900 truncate">Status Antrean</h2>
              </div>
              <span class="bg-emerald-500 text-slate-900 border-2 border-slate-900 px-2 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider shrink-0">AKTIF</span>
            </div>
            <div class="grid grid-cols-2 gap-2 sm:gap-3 mb-4">
              <div class="bg-blue-700 text-white border-2 border-slate-900 p-3 sm:p-4">
                <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-blue-200">SEDANG DI STUDIO</div>
                <div id="heroActiveQueue" class="text-2xl sm:text-3xl font-black font-mono tracking-tight mt-1">KTH-012</div>
                <div id="heroActivePackage" class="text-[10px] sm:text-[11px] text-blue-100 mt-1 truncate">Kothak Signature Box</div>
              </div>
              <div class="bg-white text-slate-900 border-2 border-slate-900 p-3 sm:p-4">
                <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-slate-500">BERIKUTNYA</div>
                <div id="heroNextQueue" class="text-2xl sm:text-3xl font-black font-mono tracking-tight text-blue-700 mt-1">KTH-013</div>
                <div class="text-[10px] sm:text-[11px] text-slate-600 mt-1">~<span id="heroWaitMinutes">10</span> menit lagi</div>
              </div>
            </div>
            <div class="bg-white border-2 border-slate-900 p-2.5 sm:p-3 mb-3 text-[11px] space-y-2">
              <div class="flex items-center justify-between gap-2">
                <span class="font-bold text-slate-700 truncate">Booth A (Electric Blue)</span>
                <span class="font-mono font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 border border-emerald-700 text-[10px] shrink-0">READY</span>
              </div>
              <div class="flex items-center justify-between gap-2">
                <span class="font-bold text-slate-700 truncate">Booth B (Warm Cream)</span>
                <span class="font-mono font-bold text-blue-700 bg-blue-100 px-2 py-0.5 border border-blue-700 text-[10px] shrink-0">IN SESSION</span>
              </div>
            </div>
            <div class="flex items-center justify-between text-xs pt-1">
              <span class="text-slate-600 font-medium">Menunggu: <strong id="heroWaitingCount" class="text-slate-900">3</strong></span>
              <a href="#bookingSection" class="font-extrabold text-blue-700 hover:underline uppercase tracking-wider text-[11px]">Ambil Antrean ➔</a>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- STEPPER BAR -->
  <section class="bg-white border-b-2 border-slate-900 py-4 sm:py-6 -mx-3 sm:-mx-4 md:-mx-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-4">
        <div class="border-2 border-slate-900 bg-[#F5F4EE] p-3 sm:p-4 flex items-start gap-2 sm:gap-3">
          <span class="bg-blue-700 text-white font-mono font-extrabold text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 border border-slate-900 shrink-0">01</span>
          <div class="min-w-0">
            <div class="font-extrabold text-[10px] sm:text-xs uppercase tracking-wider text-slate-900">Pilih Paket</div>
            <p class="text-[10px] sm:text-xs text-slate-600 mt-0.5 hidden sm:block">Tentukan paket photobox dan tema background.</p>
          </div>
        </div>
        <div class="border-2 border-slate-900 bg-[#F5F4EE] p-3 sm:p-4 flex items-start gap-2 sm:gap-3">
          <span class="bg-blue-700 text-white font-mono font-extrabold text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 border border-slate-900 shrink-0">02</span>
          <div class="min-w-0">
            <div class="font-extrabold text-[10px] sm:text-xs uppercase tracking-wider text-slate-900">Pilih Jadwal</div>
            <p class="text-[10px] sm:text-xs text-slate-600 mt-0.5 hidden sm:block">Kunci slot waktu (anti double-booking).</p>
          </div>
        </div>
        <div class="border-2 border-slate-900 bg-[#F5F4EE] p-3 sm:p-4 flex items-start gap-2 sm:gap-3">
          <span class="bg-blue-700 text-white font-mono font-extrabold text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 border border-slate-900 shrink-0">03</span>
          <div class="min-w-0">
            <div class="font-extrabold text-[10px] sm:text-xs uppercase tracking-wider text-slate-900">QRIS & Upload</div>
            <p class="text-[10px] sm:text-xs text-slate-600 mt-0.5 hidden sm:block">Scan QRIS lalu unggah bukti transfer.</p>
          </div>
        </div>
        <div class="border-2 border-slate-900 bg-[#F5F4EE] p-3 sm:p-4 flex items-start gap-2 sm:gap-3">
          <span class="bg-blue-700 text-white font-mono font-extrabold text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 border border-slate-900 shrink-0">04</span>
          <div class="min-w-0">
            <div class="font-extrabold text-[10px] sm:text-xs uppercase tracking-wider text-slate-900">Tiket & Queue</div>
            <p class="text-[10px] sm:text-xs text-slate-600 mt-0.5 hidden sm:block">Kasir verifikasi, tiket WA, pantau antrean.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- SECTION 1: PEMILIHAN PAKET FOTO -->
  <section id="packagesSection" class="py-8 sm:py-14 border-b-2 border-slate-900">
    <div class="max-w-7xl mx-auto">
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-6 sm:mb-8 gap-4">
        <div>
          <div class="text-[10px] sm:text-xs font-extrabold uppercase tracking-[0.2em] text-blue-700 mb-1">WHAT WE OFFER • PHOTOBOX STUDIO</div>
          <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">Pilih Paket Foto <span class="font-editorial italic font-normal text-blue-700">Kothak</span></h2>
          <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-2xl">Seluruh paket termasuk pencahayaan profesional, properti gratis, cetak strip instan, dan soft file digital HD.</p>
        </div>
        <div class="w-full md:w-auto">
          <input type="text" id="packageSearchInput" oninput="renderPackages()" placeholder="Cari paket..." class="bg-white border-2 border-slate-900 px-3.5 py-2 text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-700 w-full md:w-56" />
        </div>
      </div>

      <!-- Category Filters -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 mb-6 sm:mb-8 pb-4 border-b-2 border-slate-900/15">
        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2" id="categoryFilterBar">
          <button type="button" onclick="setCategoryFilter('ALL')" data-cat="ALL" class="cat-filter-btn bg-blue-700 text-white border-2 border-slate-900 px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-extrabold uppercase tracking-wider">ALL (6)</button>
          <button type="button" onclick="setCategoryFilter('SOLO_DUO')" data-cat="SOLO_DUO" class="cat-filter-btn bg-white text-slate-900 hover:bg-blue-50 border-2 border-slate-900 px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-extrabold uppercase tracking-wider">SOLO</button>
          <button type="button" onclick="setCategoryFilter('GROUP')" data-cat="GROUP" class="cat-filter-btn bg-white text-slate-900 hover:bg-blue-50 border-2 border-slate-900 px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-extrabold uppercase tracking-wider">GROUP</button>
          <button type="button" onclick="setCategoryFilter('BUNDLING')" data-cat="BUNDLING" class="cat-filter-btn bg-white text-slate-900 hover:bg-blue-50 border-2 border-slate-900 px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-extrabold uppercase tracking-wider">BUNDLE</button>
        </div>
        <div class="flex items-center gap-2 text-xs">
          <span class="font-bold uppercase tracking-wider text-slate-700 hidden sm:inline">Background:</span>
          <select id="backdropFilterSelect" onchange="renderPackages()" class="bg-white border-2 border-slate-900 px-2 sm:px-3 py-1.5 sm:py-2 font-bold text-slate-900 focus:outline-none text-[11px] sm:text-xs w-full sm:w-auto">
            <option value="ALL">Semua Background</option>
            <option value="Electric Blue Backdrop">Electric Blue</option>
            <option value="Warm Cream Minimalist">Warm Cream</option>
          </select>
        </div>
      </div>

      <div id="packagesGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Dynamically rendered by JS -->
      </div>
    </div>
  </section>

  <!-- SECTION 2: BOOKING & CHECKOUT -->
  <section id="bookingSection" class="py-8 sm:py-14 bg-[#FAF8F5] border-b-2 border-slate-900 -mx-3 sm:-mx-4 md:-mx-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-6 sm:mb-8">
        <div class="text-[10px] sm:text-xs font-extrabold uppercase tracking-[0.2em] text-blue-700 mb-1">RESERVASI & PEMBAYARAN QRIS</div>
        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">Atur Jadwal & Checkout <span class="font-editorial italic font-normal text-blue-700">Instan</span></h2>
        <p class="text-xs sm:text-sm text-slate-600 mt-1">Pilih tanggal dan jam sesi foto. Sistem mengunci kuota untuk mencegah double-booking.</p>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

        <!-- Left Column: Booking Steps -->
        <div class="lg:col-span-7 space-y-4 sm:space-y-6">
          <!-- Step 1: Selected Package -->
          <div class="neo-box bg-white p-4 sm:p-5">
            <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-4 gap-2">
              <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-widest text-blue-700">LANGKAH 1 • PAKET</span>
              <a href="#packagesSection" class="text-[10px] sm:text-xs font-bold text-slate-600 underline hover:text-blue-700">Ubah</a>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
              <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                  <span id="selectedPkgBadge" class="bg-blue-700 text-white text-[9px] sm:text-[10px] font-extrabold uppercase px-2 py-0.5">BEST SELLER</span>
                  <span id="selectedPkgDuration" class="font-mono text-[10px] sm:text-xs font-bold text-slate-600">15 Menit</span>
                </div>
                <h3 id="selectedPkgName" class="text-lg sm:text-xl font-extrabold text-slate-900 mt-1 truncate">Kothak Signature Box (2–4 Orang)</h3>
                <p id="selectedPkgDesc" class="text-[10px] sm:text-xs text-slate-600 mt-1 line-clamp-2">Sesi photobox 15 menit, 2 strip cetak 4R, soft file HD + GIF via QR.</p>
              </div>
              <div class="sm:text-right shrink-0 bg-[#F5F4EE] border-2 border-slate-900 px-3 sm:px-4 py-2 sm:py-3">
                <div class="text-[9px] sm:text-[10px] font-bold uppercase text-slate-500">HARGA</div>
                <div id="selectedPkgPrice" class="text-xl sm:text-2xl font-black text-blue-700 font-mono">Rp 35.000</div>
              </div>
            </div>
          </div>

          <!-- Step 2: Schedule -->
          <div class="neo-box bg-white p-4 sm:p-5">
            <div class="flex flex-wrap items-center justify-between border-b-2 border-slate-900 pb-3 mb-4 gap-2">
              <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-widest text-blue-700">LANGKAH 2 • JADWAL</span>
              <span class="text-[10px] sm:text-[11px] font-mono text-slate-600">09:00–23:00 WIB</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-4 sm:mb-5">
              <div>
                <label for="bookingDateInput" class="block text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">Tanggal</label>
                <input type="date" id="bookingDateInput" value="2026-11-13" min="2026-11-13" onchange="renderScheduleSlots()" class="w-full bg-[#F5F4EE] border-2 border-slate-900 px-3 py-2 sm:py-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:border-blue-700" />
              </div>
              <div>
                <label for="studioBackdropSelect" class="block text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">Background</label>
                <select id="studioBackdropSelect" onchange="updateCheckoutSummary()" class="w-full bg-[#F5F4EE] border-2 border-slate-900 px-3 py-2 sm:py-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:border-blue-700">
                  <option value="Background 1: Electric Blue (Signature)">Electric Blue (Signature)</option>
                  <option value="Background 2: Warm Cream Minimalist">Warm Cream Minimalist</option>
                </select>
              </div>
            </div>
            <div class="mb-2 flex flex-wrap items-center justify-between text-[10px] sm:text-xs gap-2">
              <span class="font-extrabold uppercase tracking-wider text-slate-700">Slot (<span id="slotDateLabel">13 Nov 2026</span>):</span>
              <div class="flex items-center gap-2 sm:gap-3 text-[10px] sm:text-[11px]">
                <span class="inline-flex items-center gap-1"><span class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-white border border-slate-900 inline-block"></span> Tersedia</span>
                <span class="inline-flex items-center gap-1"><span class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-blue-700 border border-slate-900 inline-block"></span> Dipilih</span>
                <span class="inline-flex items-center gap-1"><span class="w-2 h-2 sm:w-2.5 sm:h-2.5 bg-slate-200 border border-slate-400 inline-block"></span> Penuh</span>
              </div>
            </div>
            <div id="scheduleSlotsGrid" class="grid grid-cols-3 sm:grid-cols-4 gap-2 sm:gap-2.5 mt-3">
              <!-- Dynamically rendered by JS -->
            </div>
          </div>
        </div>

        <!-- Right Column: Checkout QRIS -->
        <div class="lg:col-span-5">
          <div class="neo-box bg-white p-4 sm:p-6 lg:sticky lg:top-24">
            <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-4 gap-2">
              <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-widest text-blue-700">LANGKAH 3 • CHECKOUT</span>
              <span class="bg-blue-100 text-blue-800 border border-blue-700 px-2 py-0.5 text-[9px] sm:text-[10px] font-extrabold uppercase shrink-0">QRIS</span>
            </div>
            <!-- Order Summary -->
            <div class="bg-[#F5F4EE] border-2 border-slate-900 p-3 text-[11px] sm:text-xs space-y-2 mb-4">
              <div class="flex justify-between gap-2"><span class="text-slate-600 shrink-0">Pelanggan:</span><span id="summaryCustomerName" class="font-bold text-slate-900 text-right truncate">Hana Naila Rahmadina</span></div>
              <div class="flex justify-between gap-2"><span class="text-slate-600 shrink-0">Paket:</span><span id="summaryPkgName" class="font-bold text-slate-900 text-right truncate">Kothak Signature Box</span></div>
              <div class="flex justify-between gap-2"><span class="text-slate-600 shrink-0">Jadwal:</span><span id="summarySlotTime" class="font-mono font-bold text-blue-700 text-right">13 Nov • 15:00-15:30</span></div>
              <div class="flex justify-between gap-2"><span class="text-slate-600 shrink-0">Background:</span><span id="summaryBackdrop" class="font-semibold text-slate-900 text-right truncate">Electric Blue</span></div>
              <div class="border-t border-slate-300 pt-2 flex justify-between items-baseline"><span class="font-extrabold uppercase text-slate-900">Total:</span><span id="summaryTotalPrice" class="text-lg sm:text-xl font-black font-mono text-blue-700">Rp 35.000</span></div>
            </div>
            <!-- QRIS Card -->
            <div class="border-2 border-slate-900 bg-white p-3 sm:p-4 text-center mb-4">
              <div class="text-[10px] sm:text-xs font-black uppercase tracking-wider text-slate-900">KOTHaK CAFE &amp; PHOTO</div>
              <div class="text-[10px] sm:text-[11px] font-mono text-blue-700 font-bold mt-0.5">NMID: ID2026KOTHAKPBG01</div>
              <div class="text-[10px] text-slate-500 mb-2">Scan via GoPay, OVO, DANA, ShopeePay, dll</div>
              <div class="mx-auto w-32 h-32 sm:w-44 sm:h-44 border-2 border-slate-900 p-2 bg-white flex items-center justify-center">
                <svg viewBox="0 0 120 120" class="w-full h-full" role="img" aria-label="QRIS Statis Kothak Photo"><rect width="120" height="120" fill="#FFFFFF"/><rect x="8" y="8" width="28" height="28" fill="none" stroke="#0F172A" stroke-width="4"/><rect x="14" y="14" width="16" height="16" fill="#1D4ED8"/><rect x="84" y="8" width="28" height="28" fill="none" stroke="#0F172A" stroke-width="4"/><rect x="90" y="14" width="16" height="16" fill="#1D4ED8"/><rect x="8" y="84" width="28" height="28" fill="none" stroke="#0F172A" stroke-width="4"/><rect x="14" y="90" width="16" height="16" fill="#1D4ED8"/><rect x="46" y="46" width="28" height="28" fill="#1D4ED8" stroke="#0F172A" stroke-width="2"/><text x="60" y="59" text-anchor="middle" fill="#FFFFFF" font-size="7" font-weight="800" font-family="sans-serif">KOTHaK</text><text x="60" y="68" text-anchor="middle" fill="#E0E7FF" font-size="6" font-weight="700" font-family="monospace">QRIS</text></svg>
              </div>
            </div>
            <!-- Upload & Submit -->
            <div class="space-y-3">
              <label class="block text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-slate-900">Upload Bukti Transfer *</label>
              <div class="flex gap-2">
                <input type="file" id="proofFileInput" accept="image/*" onchange="handleProofFileChange(event)" class="block w-full text-[11px] sm:text-xs text-slate-700 file:mr-2 file:py-1.5 file:px-2 sm:file:py-2 sm:file:px-3 file:border-2 file:border-slate-900 file:text-[10px] sm:file:text-xs file:font-extrabold file:uppercase file:bg-[#F5F4EE] file:text-slate-900 hover:file:bg-blue-50 border-2 border-slate-900 bg-white p-1" />
                <button type="button" onclick="simulateSampleProof()" class="neo-btn bg-blue-100 hover:bg-blue-200 text-blue-900 px-2 sm:px-3 py-1.5 text-[10px] sm:text-[11px] font-extrabold uppercase shrink-0" title="Contoh bukti">+Demo</button>
              </div>
              <div id="proofPreviewBox" class="hidden border-2 border-emerald-700 bg-emerald-50 p-2 text-[11px] flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 truncate"><span class="bg-emerald-600 text-white font-mono text-[10px] font-bold px-1.5 py-0.5 shrink-0">READY</span><span id="proofFileName" class="font-mono text-slate-800 truncate">bukti.jpg</span></div>
                <button type="button" onclick="clearProofFile()" class="text-rose-700 font-bold hover:underline shrink-0">Hapus</button>
              </div>
              <div>
                <label for="customerWaInput" class="block text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-slate-900 mb-1">No. WhatsApp</label>
                <input type="tel" id="customerWaInput" value="0812-3456-7890" placeholder="08xx-xxxx-xxxx" class="w-full bg-[#F5F4EE] border-2 border-slate-900 px-3 py-2 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-blue-700" />
              </div>
              <button type="button" onclick="submitBookingCheckout()" class="neo-btn w-full bg-blue-700 hover:bg-blue-800 text-white py-3 sm:py-3.5 px-4 text-[11px] sm:text-xs font-extrabold uppercase tracking-widest mt-2">KIRIM BUKTI & AMBIL ANTREAN ➔</button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- SECTION 3: SMART QUEUE TABLE -->
  <section id="queueSection" class="py-8 sm:py-14 bg-[#F5F4EE]">
    <div class="max-w-7xl mx-auto">
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-6 sm:mb-8 gap-4">
        <div>
          <div class="text-[10px] sm:text-xs font-extrabold uppercase tracking-[0.2em] text-blue-700 mb-1">REAL-TIME SMART QUEUE</div>
          <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">Dasbor Antrean <span class="font-editorial italic font-normal text-blue-700">Pintar</span></h2>
          <p class="text-xs sm:text-sm text-slate-600 mt-1">Pantau status antrean & simulasi aksi kasir.</p>
        </div>
        <div>
          <button type="button" onclick="advanceNextQueue()" class="neo-btn bg-emerald-500 hover:bg-emerald-400 text-slate-900 px-3 sm:px-4 py-2 sm:py-2.5 text-[10px] sm:text-xs font-extrabold uppercase tracking-wider">🔊 Panggil Berikutnya</button>
        </div>
      </div>
      <div class="neo-box bg-white overflow-x-auto -mx-3 sm:mx-0">
        <table class="w-full text-left border-collapse min-w-[700px]">
          <thead>
            <tr class="bg-blue-700 text-white border-b-2 border-slate-900 text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider">
              <th class="py-3 px-3 sm:px-4">Antrean</th>
              <th class="py-3 px-3 sm:px-4">Pelanggan</th>
              <th class="py-3 px-3 sm:px-4">Paket</th>
              <th class="py-3 px-3 sm:px-4">Jadwal</th>
              <th class="py-3 px-3 sm:px-4">Bukti QRIS</th>
              <th class="py-3 px-3 sm:px-4">Status</th>
              <th class="py-3 px-3 sm:px-4 text-right">Aksi Kasir</th>
            </tr>
          </thead>
          <tbody id="smartQueueTableBody" class="divide-y-2 divide-slate-900 text-xs">
            <!-- Dynamically rendered by JS -->
          </tbody>
        </table>
      </div>
    </div>
  </section>

</div>
@endsection
