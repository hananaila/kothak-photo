@extends('layouts.app')

@section('title', 'Tiket Antrean ' . $booking->queue_number . ' — KOTHaK Photo Studio')

@section('content')
<div class="w-full max-w-6xl mx-auto space-y-6 relative">

  <!-- TOP ACTION BAR: CLEAR PAGE TITLE, BREADCRUMB & ACTIONS -->
  <div class="bg-white rounded-2xl sm:rounded-3xl border border-[#E7E2D8] p-4 sm:p-5 shadow-soft flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 no-print">
    <div class="flex items-center gap-3 min-w-0">
      <div class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-[#E8EEF9] border border-[#1E3A8A]/20 text-[#1E3A8A] shadow-xs shrink-0">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
      </div>
      <div class="min-w-0">
        <div>
          <h1 class="text-base sm:text-lg font-heading font-black text-[#1A1A2E] leading-tight truncate">
            Tiket Antrean Digital
          </h1>
        </div>
        <p class="text-xs text-slate-500 font-sans mt-0.5">
          Pantau giliran bilik fotomu secara real-time dari perangkat ini.
        </p>
      </div>
    </div>

    <!-- Tombol Aksi: Riwayat Tiket & Share -->
    <div class="flex items-center flex-wrap gap-2 shrink-0">
      <a href="{{ route('booking.history') }}" title="Riwayat Tiket Perangkat Ini" class="inline-flex items-center gap-1.5 text-xs font-heading font-bold text-[#1E3A8A] bg-[#E8EEF9]/70 hover:bg-[#E8EEF9] border border-[#1E3A8A]/20 px-3.5 py-2 rounded-xl transition-all">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Riwayat</span>
      </a>
      <button onclick="shareTicket()" title="Bagikan Tiket" class="inline-flex items-center gap-1.5 text-xs font-heading font-bold text-white bg-[#1E3A8A] hover:bg-[#2D2DB0] px-3.5 py-2 rounded-xl shadow-xs transition-all cursor-pointer">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
        <span>Bagikan</span>
      </button>
    </div>
  </div>

  <!-- RESPONSIVE TWO-COLUMN GRID: DESKTOP LEGA & FULL, MOBILE & TABLET ADAPTIF -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

    <!-- KOLOM KIRI (7 KARTU TIKET UTAMA): PAS TIKET ANTREAN ELEGAN -->
    <div class="lg:col-span-7 xl:col-span-8">
      <div class="bg-white rounded-3xl border border-[#E7E2D8] shadow-soft relative overflow-hidden">
        
        <!-- Top Navy Ribbon Accent -->
        <div class="h-2 bg-gradient-to-r from-[#1E3A8A] via-[#2D2DB0] to-[#1E3A8A]"></div>

        <!-- Studio Branding Header -->
        <div class="p-5 sm:p-6 pb-4 flex items-center justify-between border-b border-[#F0ECE4]">
          <div class="flex items-center gap-3">
            <div class="bg-white p-1 rounded-xl border border-[#1E3A8A]/15 shadow-xs">
              <img src="{{ asset('images/logo.jpeg') }}" alt="KOTHaK Photo" class="w-8 h-8 sm:w-9 sm:h-9 object-contain rounded-lg" />
            </div>
            <div>
              <span class="font-heading font-black text-sm sm:text-base text-[#1E3A8A] block leading-none">KOTHaK Photo Studio</span>
              <span class="text-[10px] text-slate-500 font-sans mt-0.5 block">Purbalingga &bull; Smart Queue Pass</span>
            </div>
          </div>

          <div class="text-right hidden sm:block">
            <span class="text-[9px] font-mono font-bold uppercase text-slate-400 block">BILIK FOTO</span>
            <span class="text-xs font-heading font-bold text-[#1A1A2E]">{{ $booking->studio_backdrop }}</span>
          </div>
        </div>

        <!-- TOP SECTION: STATUS ANTREAN / GILIRAN / ANTREAN DI DEPAN -->
        <div id="queueStatusSection" class="py-8 sm:py-10 px-6 text-center transition-all duration-300">
          @if(in_array($booking->status, ['cancelled', 'no_show']))
            <div class="space-y-3 py-2">
              <div class="w-16 h-16 mx-auto rounded-full bg-amber-100 border border-amber-300 flex items-center justify-center text-3xl font-black text-amber-700">
                !
              </div>
              <div class="text-2xl sm:text-3xl font-heading font-black tracking-tight text-[#1A1A2E]">
                Nomor Antrean Terlewat
              </div>
              <p class="text-xs sm:text-sm text-slate-600 font-sans max-w-md mx-auto leading-relaxed">
                Nomor antreanmu sudah terlewat atau dibatalkan. Jangan khawatir, kamu bisa mengambil antrean baru kapan saja.
              </p>
              <div class="pt-2">
                <a href="{{ route('packages.index') }}" class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 text-xs sm:text-sm font-heading font-black tracking-wider uppercase rounded-xl shadow-md transition-all">
                  Ambil Antrean Baru &rarr;
                </a>
              </div>
            </div>
          @elseif($booking->status === 'completed')
            <div class="space-y-3 py-2">
              <div class="w-16 h-16 mx-auto rounded-full bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-700">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
              </div>
              <div class="text-2xl sm:text-3xl font-heading font-black tracking-tight text-emerald-700">
                Sesi Foto Selesai
              </div>
              <p class="text-xs sm:text-sm text-slate-600 font-sans max-w-md mx-auto leading-relaxed">
                Terima kasih telah berfoto di KOTHaK Photo Studio Purbalingga. Hasil cetak fisik dapat diambil di meja kasir.
              </p>
              <div class="pt-2">
                <a href="{{ route('packages.index') }}" class="inline-block kothak-btn-primary px-6 py-3 text-xs sm:text-sm font-heading font-black tracking-wider uppercase rounded-xl shadow-md transition-all">
                  Ambil Antrean Baru &rarr;
                </a>
              </div>
            </div>
          @elseif($booking->status === 'in_studio' || $booking->status === 'calling')
            <div class="text-[11px] sm:text-xs font-heading font-black uppercase tracking-[0.2em] {{ $booking->status === 'calling' ? 'text-amber-700' : 'text-emerald-700' }} mb-1">
              STATUS BILIK SAAT INI
            </div>
            <div class="text-4xl sm:text-5xl lg:text-6xl font-black font-heading tracking-tight {{ $booking->status === 'calling' ? 'text-amber-600' : 'text-emerald-600' }} my-3 animate-pulse">
              GILIRANMU!
            </div>
            <p class="text-xs sm:text-sm text-slate-600 font-sans mt-1">
              @if($booking->status === 'calling')
                Silakan segera menuju ke bilik <strong>{{ $booking->studio_backdrop }}</strong> dan konfirmasi ke kasir sekarang.
              @else
                Silakan segera masuk ke dalam bilik <strong>{{ $booking->studio_backdrop }}</strong> sekarang.
              @endif
            </p>
          @elseif($aheadCount > 0)
            <div class="text-[11px] sm:text-xs font-heading font-black uppercase tracking-[0.2em] text-slate-500 mb-1">
              ANTREAN DI DEPANMU (AHEAD OF YOU)
            </div>
            <div id="aheadCountNumber" class="text-5xl sm:text-6xl lg:text-7xl font-black font-heading tracking-tight text-[#1E3A8A] my-3">
              {{ $aheadCount }} <span class="text-2xl sm:text-3xl text-slate-500 font-bold lowercase">antrean</span>
            </div>
            <p id="aheadCountDesc" class="text-xs sm:text-sm text-slate-600 font-sans mt-1">Ada {{ $aheadCount }} pemesan lain sebelum giliran sesi fotomu.</p>
          @else
            <div class="text-[11px] sm:text-xs font-heading font-black uppercase tracking-[0.2em] text-slate-500 mb-1">
              AHEAD OF YOU (ANTREAN DI DEPANMU)
            </div>
            <div id="aheadCountNumber" class="text-4xl sm:text-5xl lg:text-6xl font-black font-heading tracking-tight text-emerald-600 my-3">
              Giliran Berikutnya!
            </div>
            <p id="aheadCountDesc" class="text-xs sm:text-sm text-slate-600 font-sans mt-1">Kamu adalah antrean terdepan di <strong>{{ $booking->studio_backdrop }}</strong>. Bersiaplah!</p>
          @endif
        </div>

        <!-- TICKET COUPON CUTOUT NOTCH & DASHED LINE -->
        <div class="relative py-2 overflow-hidden">
          <!-- Left circular punch hole -->
          <div class="absolute -left-4 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-[#F8F7F4] border border-[#E7E2D8] shadow-inner"></div>
          <!-- Dashed divider line -->
          <div class="border-t-2 border-dashed border-[#1E3A8A]/20 w-full px-6"></div>
          <!-- Right circular punch hole -->
          <div class="absolute -right-4 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-[#F8F7F4] border border-[#E7E2D8] shadow-inner"></div>
        </div>

        <!-- BOTTOM SECTION: YOUR TICKET NUMBER -->
        <div class="py-8 sm:py-10 px-6 text-center space-y-4">
          <div>
            <div class="text-[11px] sm:text-xs font-heading font-black uppercase tracking-[0.2em] text-slate-500">
              YOUR TICKET NUMBER
            </div>
            <div class="text-6xl sm:text-7xl lg:text-8xl font-mono font-black tracking-tight text-[#1E3A8A] my-2 select-all">
              {{ $booking->queue_number }}
            </div>
            <div class="text-xs text-slate-500 font-mono">
              Waktu Booking: <span class="font-bold text-slate-700">{{ $booking->created_at->format('d M Y • H.i') }} WIB</span>
            </div>
          </div>

          <!-- STATUS BADGE & MICRO-DETAILS -->
          <div class="inline-flex items-center gap-2 bg-[#FAF9F6] border border-[#EBE7E0] px-4 py-2 rounded-2xl shadow-2xs">
            <span id="ticketStatusDot" class="w-2.5 h-2.5 rounded-full {{ in_array($booking->status, ['in_studio']) ? 'bg-emerald-500 animate-ping' : 'bg-[#1E3A8A]' }}"></span>
            <span id="ticketStatusText" class="text-xs font-heading font-bold text-slate-800">
              {{ $booking->status_label }}
            </span>
            <span class="text-slate-300">&bull;</span>
            <span class="text-xs font-mono font-bold text-[#1E3A8A]">
              {{ $booking->studio_backdrop }}
            </span>
          </div>

          <!-- SIMULATED PHOTO STRIP FRAME PREVIEW -->
          <div class="pt-4 max-w-xs mx-auto">
            <div class="bg-[#FAF9F6] border border-[#E7E2D8] p-3 rounded-2xl flex items-center justify-between text-xs font-sans text-slate-600">
              <span class="font-medium">Kode Unik Tiket:</span>
              <div class="flex items-center gap-1.5 font-mono font-bold text-[#1E3A8A]">
                <span>{{ $booking->queue_number }}-{{ $booking->id }}</span>
                <button type="button" onclick="copyTicketCode('{{ $booking->queue_number }}-{{ $booking->id }}')" class="hover:text-[#2D2DB0] text-slate-400 p-0.5" title="Salin kode tiket">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
              </div>
            </div>
          </div>

        </div>

        <!-- FOOTER TICKET NOTATION -->
        <div class="bg-[#F8F7F4] p-4 text-center border-t border-[#E7E2D8] text-[11px] text-slate-500 font-sans flex items-center justify-center gap-2">
          <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          <span>Tiket digital resmi KOTHaK Photo Studio. Simpan halaman ini saat mengantre.</span>
        </div>

      </div>
    </div>

    <!-- KOLOM KANAN (5 SPAN DI DESKTOP): NOTIFIKASI BROWSER, DETAIL RESERVASI & PANDUAN -->
    <div class="lg:col-span-5 xl:col-span-4 space-y-6">

      <!-- BROWSER NOTIFICATION STATUS CARD (Fitur Inti Pengingat Antrean) -->
      <div id="notifStatusCard" class="bg-white rounded-3xl p-5 border border-[#1E3A8A]/15 shadow-soft space-y-3">
        <div class="flex items-center justify-between gap-3 sm:gap-4">
          <div class="flex items-center gap-3 sm:gap-4 min-w-0">
            <div class="w-11 h-11 rounded-2xl bg-[#E8EEF9] text-[#1E3A8A] border border-[#1E3A8A]/20 flex items-center justify-center shrink-0 shadow-xs" id="notifIconBadge">
              <svg class="w-5 h-5 text-[#1E3A8A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div class="space-y-0.5 min-w-0">
              <h4 class="font-heading font-black text-sm text-[#1A1A2E]" id="notifStatusHeading">
                Pengingat Antrean
              </h4>
              <p class="text-xs text-slate-500 font-sans" id="notifStatusSubtitle">
                Aktifkan notifikasi browser agar Anda dapat bersantai tanpa perlu terus melihat layar.
              </p>
            </div>
          </div>
          <div id="notifActionSlot" class="shrink-0">
            <button type="button" onclick="requestBrowserNotification()" id="notifStatusBtn" class="shrink-0 text-xs font-heading font-black px-4 py-2.5 rounded-xl bg-[#1E3A8A] text-white hover:bg-[#2D2DB0] transition-all shadow-xs cursor-pointer">
              Aktifkan
            </button>
          </div>
        </div>

        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
          <div class="inline-flex items-center gap-1.5 font-heading font-bold text-[11px]" id="notifPermissionLabel">
            <span class="w-2 h-2 rounded-full bg-amber-400" id="notifPermDot"></span>
            <span class="text-slate-600">Status Izin: <span id="notifPermText" class="font-bold text-[#1E3A8A]">Belum Aktif</span></span>
          </div>
          <button type="button" onclick="openNotifyModal()" id="notifResetLink" class="hidden text-[11px] font-heading font-bold text-[#1E3A8A] hover:underline cursor-pointer">
            Atur Ulang &rarr;
          </button>
        </div>
      </div>

      <!-- CARD 1: DETAIL SESI & BOOTH -->
      <div class="bg-white rounded-3xl border border-[#E7E2D8] p-5 sm:p-6 shadow-soft space-y-4">
        <div class="flex items-center justify-between border-b border-[#F0ECE4] pb-3">
          <h3 class="text-sm font-heading font-black uppercase text-[#1A1A2E] tracking-wider">
            Detail Reservasi
          </h3>
          <span class="text-xs font-mono font-bold text-[#1E3A8A] bg-[#E8EEF9] px-2.5 py-0.5 rounded-lg border border-[#1E3A8A]/20">
            {{ $booking->studio_backdrop }}
          </span>
        </div>

        <div class="space-y-3 text-xs font-sans">
          <div class="flex items-center justify-between py-1 border-b border-slate-100">
            <span class="text-slate-500">Nama Pemesan:</span>
            <span class="font-bold text-[#1A1A2E] text-right">{{ $booking->user->name }}</span>
          </div>

          <div class="flex items-center justify-between py-1 border-b border-slate-100">
            <span class="text-slate-500">Nomor WhatsApp:</span>
            <span class="font-mono font-bold text-slate-800 text-right">{{ $booking->user->phone_whatsapp ?: '-' }}</span>
          </div>

          <div class="flex items-center justify-between py-1 border-b border-slate-100">
            <span class="text-slate-500">Catatan Khusus:</span>
            <span class="font-medium text-slate-700 text-right italic">{{ $booking->notes ?: 'Tidak ada' }}</span>
          </div>

          <div class="flex items-center justify-between py-1 border-b border-slate-100">
            <span class="text-slate-500">Paket &amp; Durasi:</span>
            <span class="font-bold text-[#1E3A8A] text-right">Rp {{ number_format($booking->package_price, 0, ',', '.') }} (15 Menit)</span>
          </div>

          <div class="flex items-center justify-between py-1 border-b border-slate-100">
            <span class="text-slate-500">Output Cetak:</span>
            <span class="font-bold text-[#1E3A8A] text-right">{{ $booking->package?->prints_included ?: '2 Strip + Soft File' }}</span>
          </div>

          @if($booking->bookingAddOns && $booking->bookingAddOns->count() > 0)
            <div class="py-1 border-b border-slate-100 space-y-1">
              <span class="text-slate-500 block">Layanan Tambahan (Add-on):</span>
              @foreach($booking->bookingAddOns as $bAddOn)
                <div class="flex items-center justify-between pl-2 text-[11px]">
                  <span class="text-slate-600">&bull; {{ $bAddOn->addOn->name }} (x{{ $bAddOn->quantity }})</span>
                  <span class="font-mono font-bold text-slate-800">Rp {{ number_format($bAddOn->subtotal, 0, ',', '.') }}</span>
                </div>
              @endforeach
            </div>
          @endif

          <div class="pt-2 flex items-baseline justify-between">
            <span class="font-heading font-black text-xs uppercase text-slate-700">Total Pembayaran:</span>
            <span class="text-xl font-mono font-black text-[#1E3A8A]">
              Rp {{ number_format($booking->total_price, 0, ',', '.') }}
            </span>
          </div>
        </div>
      </div>

    </div>

  </div>

</div>

<!-- POPUP MODAL MINTA IZIN NOTIFIKASI ANTREAN (COLOR PALETTE KOTHAK & COMPACT RESPONSIF) -->
<div id="stulokalNotifyModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#0F172A]/50 backdrop-blur-xs hidden transition-opacity duration-200">
  <div class="bg-white text-[#1A1A2E] rounded-3xl max-w-sm sm:max-w-md w-full p-5 sm:p-6 border border-[#E7E2D8] shadow-2xl relative space-y-3.5 sm:space-y-4 animate-in fade-in zoom-in-95 duration-200">
    <!-- Close button -->
    <button onclick="dismissNotifyModal()" class="absolute top-4 right-4 w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-all cursor-pointer font-bold text-base leading-none" title="Tutup">&times;</button>

    <!-- Header / Title -->
    <div class="pr-6">
      <h3 class="text-base sm:text-lg font-heading font-black text-[#1A1A2E] leading-snug">
        Tidak perlu terus memantau layar
      </h3>
      <p class="text-xs text-slate-500 font-sans leading-relaxed mt-1">
        Kami akan memberi tahu saat giliran fotomu tiba. Tetap dapat notifikasi bahkan saat layar ponsel terkunci atau tab diminimalkan.
      </p>
    </div>

    <!-- PREVIEW KARTU NOTIFIKASI SMARTPHONE (PALET KOTHAK, TANPA EMOTIKON) -->
    <div class="p-3.5 rounded-2xl bg-[#FAF9F6] border border-[#E7E2D8] text-slate-800 space-y-1 shadow-2xs">
      <div class="flex items-center justify-between text-[10px] text-slate-400">
        <div class="flex items-center gap-1.5 font-heading font-black tracking-wider text-[#1E3A8A] uppercase">
          <svg class="w-3.5 h-3.5 text-[#1E3A8A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
          <span>KOTHAK PHOTO</span>
        </div>
        <span class="text-[10px] text-slate-400 font-mono">baru saja</span>
      </div>
      <div class="text-xs sm:text-sm font-heading font-black text-[#1A1A2E]">
        Sebentar lagi giliranmu!
      </div>
      <p class="text-[11px] sm:text-xs text-slate-600 font-sans leading-relaxed">
        @if($aheadCount > 0)
          Tinggal {{ $aheadCount }} nomor lagi di {{ $booking->studio_backdrop }}. Siap-siap ya!
        @else
          Kamu adalah antrean terdepan di {{ $booking->studio_backdrop }}. Siap-siap ya!
        @endif
      </p>
    </div>

    <!-- Instruction & Action Buttons -->
    <div class="space-y-2.5 pt-1">
      <p class="text-[11px] text-slate-500 font-sans text-center" id="notifModalInstruction">
        Tekan tombol di bawah, lalu pilih <strong class="text-slate-800">"Izinkan" (Allow)</strong> ketika browser meminta izin.
      </p>

      <button type="button" onclick="requestBrowserNotification()" class="w-full py-3 px-4 rounded-xl bg-[#1E3A8A] hover:bg-[#2D2DB0] text-white text-xs font-heading font-black tracking-wider uppercase text-center shadow-sm hover:shadow transition-all active:scale-[0.98] cursor-pointer flex items-center justify-center gap-2">
        <span id="notifModalBtnText">Aktifkan Notifikasi</span>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </button>

      <button type="button" onclick="dismissNotifyModal()" class="w-full text-center text-xs font-heading font-bold text-slate-400 hover:text-slate-700 py-1 transition-all cursor-pointer">
        Nanti Saja
      </button>
    </div>
  </div>
</div>

<!-- TOAST NOTIFICATION -->
<div id="copyToast" class="fixed bottom-6 right-6 z-50 bg-[#1A1A2E] text-white px-4 py-2.5 rounded-xl shadow-lg text-xs font-heading font-bold opacity-0 transition-opacity duration-300 pointer-events-none flex items-center gap-2">
  <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
  <span id="copyToastMsg">Berhasil disalin!</span>
</div>
@endsection

@push('scripts')
<script>
  let hasNotifiedTurn = false;
  const bookingId = @json($booking->id);

  function openNotifyModal() {
    const isGranted = ("Notification" in window) && (Notification.permission === 'granted');
    if (isGranted) {
      updateNotificationUI('granted');
      return;
    }
    const modal = document.getElementById('stulokalNotifyModal');
    if (modal) {
      modal.classList.remove('hidden');
      const instr = document.getElementById('notifModalInstruction');
      const btnText = document.getElementById('notifModalBtnText');
      if (("Notification" in window) && Notification.permission === 'denied') {
        if (instr) instr.innerHTML = '<div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 text-amber-950 text-[11px] leading-relaxed text-left space-y-1"><div class="font-heading font-black text-xs text-amber-900">Izin Notifikasi Diblokir di HP / Browser:</div><p class="text-slate-700">1. Di HP, tap ikon <strong>Setelan / Gembok (🔒)</strong> di kiri atas kolom alamat web.</p><p class="text-slate-700">2. Pilih <strong>Izin Situs (Permissions) &rarr; Notifikasi</strong>.</p><p class="text-slate-700">3. Ubah menjadi <strong>Izinkan (Allow)</strong> lalu muat ulang halaman ini.</p><p class="text-[10px] text-slate-500 italic pt-0.5">Atau buka menu titik tiga (⋮) di pojok kanan atas browser &gt; Setelan &gt; Notifikasi.</p></div>';
        if (btnText) btnText.textContent = 'Coba Minta Izin Lagi';
      } else {
        if (instr) instr.innerHTML = 'Tekan tombol di bawah, lalu pilih <strong class="text-slate-800">"Izinkan" (Allow)</strong> ketika browser atau ponsel meminta izin.';
        if (btnText) btnText.textContent = 'Aktifkan Notifikasi';
      }
    }
  }

  function closeNotifyModal() {
    const modal = document.getElementById('stulokalNotifyModal');
    if (modal) modal.classList.add('hidden');
  }

  function dismissNotifyModal() {
    closeNotifyModal();
    sessionStorage.setItem('kothak_notif_seen_' + bookingId, '1');
    if (!("Notification" in window) || Notification.permission !== 'granted') {
      updateNotificationUI('dismissed');
    }
  }

  function showToast(msg) {
    const toast = document.getElementById('copyToast');
    const toastMsg = document.getElementById('copyToastMsg');
    if (!toast || !toastMsg) return;
    toastMsg.textContent = msg;
    toast.classList.remove('opacity-0', 'pointer-events-none');
    setTimeout(() => {
      toast.classList.add('opacity-0', 'pointer-events-none');
    }, 2500);
  }

  // Web Audio Synthesizer: Menggunakan shared AudioContext agar lancar di browser modern
  let sharedAudioCtx = null;
  function getAudioContext() {
    if (!sharedAudioCtx) {
      sharedAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
    }
    if (sharedAudioCtx.state === 'suspended') {
      sharedAudioCtx.resume();
    }
    return sharedAudioCtx;
  }

  // Aktifkan AudioContext pada interaksi pertama pengguna di dokumen
  document.addEventListener('click', () => {
    try { getAudioContext(); } catch(e) {}
  }, { once: true });

  function playChime() {
    try {
      const ctx = getAudioContext();
      const now = ctx.currentTime;
      const osc1 = ctx.createOscillator();
      const gain1 = ctx.createGain();

      osc1.type = 'sine';
      osc1.frequency.setValueAtTime(587.33, now); // D5
      osc1.frequency.exponentialRampToValueAtTime(880, now + 0.15); // A5

      gain1.gain.setValueAtTime(0.35, now);
      gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.6);

      osc1.connect(gain1);
      gain1.connect(ctx.destination);

      osc1.start(now);
      osc1.stop(now + 0.6);
    } catch(e) {
      console.log('Audio chime not supported', e);
    }
  }

  // Helper: Kirim notifikasi status antrean aktif (meniru persis notifikasi ponsel seperti Stulokal)
  function sendCurrentQueueNotification() {
    let title = "Sebentar lagi giliranmu ⌛";
    let body = "";

    if (currentStatus === 'in_studio' || currentStatus === 'calling') {
      title = "Giliranmu Tiba! 🎉";
      body = "Nomor antrean " + queueCode + " dipanggil masuk ke bilik foto " + boothName + " sekarang!";
    } else if (currentAhead === 0) {
      title = "Sebentar lagi giliranmu ⌛";
      body = "Kamu adalah antrean terdepan di " + boothName + ". Siap-siap ya!";
    } else {
      title = "Sebentar lagi giliranmu ⌛";
      body = "Tinggal " + currentAhead + " nomor lagi di " + boothName + ". Siap-siap ya!";
    }

    // Kirim notifikasi web browser resmi ke OS & smartphone notification tray
    dispatchPushNotification(title, body, 'kothak-active-queue');
  }

  // Dispatch System & Push Notification (Support Desktop Notification API & Mobile Android Service Worker)
  async function dispatchPushNotification(title, body, tag = 'kothak-active-queue') {
    // 1. Mainkan suara lonceng panggilan
    playChime();

    // 2. Getarkan perangkat jika didukung
    if (navigator.vibrate) {
      try { navigator.vibrate([250, 100, 250]); } catch(e) {}
    }

    // 3. Browser Push Notification
    if (!("Notification" in window) || Notification.permission !== "granted") {
      return;
    }

    const notifOptions = {
      body: body,
      icon: "{{ asset('images/logo.jpeg') }}",
      badge: "{{ asset('images/logo.jpeg') }}",
      tag: tag + '-' + Date.now(), // Tag dinamis agar OS selalu memicu banner / pop-up aktif
      renotify: true,
      requireInteraction: true,
      vibrate: [250, 100, 250],
      data: { url: window.location.href }
    };

    let shown = false;

    // Jalur A: Desktop Window Notification (Chrome / Edge / Safari Desktop)
    try {
      if (typeof window.Notification === 'function') {
        const notif = new Notification(title, notifOptions);
        notif.onclick = function() {
          window.focus();
          this.close();
        };
        shown = true;
      }
    } catch(e) {
      // Android Chromium throws: Illegal constructor. Use ServiceWorkerRegistration.showNotification() instead.
      console.log('Window notification fallback to ServiceWorker:', e);
    }

    // Jalur B: Service Worker Registration showNotification (Khusus Mobile Chrome/Samsung Browser Android)
    if (!shown && ('serviceWorker' in navigator)) {
      try {
        const reg = await Promise.race([
          navigator.serviceWorker.ready,
          new Promise(r => setTimeout(() => r(null), 800))
        ]);
        if (reg && reg.showNotification) {
          await reg.showNotification(title, notifOptions);
          shown = true;
        }
      } catch(err) {
        console.log('SW showNotification fallback:', err);
      }
    }

    // Jalur C: Fallback via PostMessage ke ServiceWorker
    if (!shown && navigator.serviceWorker && navigator.serviceWorker.controller) {
      try {
        navigator.serviceWorker.controller.postMessage({
          type: 'SHOW_NOTIFICATION',
          title: title,
          options: notifOptions
        });
      } catch(e) {}
    }
  }

  // Update All Notification UI Elements based on user choice & permission
  function updateNotificationUI(state) {
    const bannerTitle = document.getElementById('notifStatusHeading');
    const bannerSubtitle = document.getElementById('notifStatusSubtitle');
    const actionSlot = document.getElementById('notifActionSlot');
    const bannerDot = document.getElementById('notifPermDot');
    const permText = document.getElementById('notifPermText');
    const resetLink = document.getElementById('notifResetLink');

    if (state === 'granted') {
      if (bannerTitle) bannerTitle.textContent = "Notifikasi Aktif";
      if (bannerSubtitle) bannerSubtitle.textContent = "Sistem akan memberi sinyal bunyi dan pesan otomatis saat giliran fotomu tiba.";
      if (actionSlot) {
        actionSlot.innerHTML = `
          <div class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs text-xs font-heading font-black">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Aktif</span>
          </div>
        `;
      }
      if (bannerDot) {
        bannerDot.className = "w-2 h-2 rounded-full bg-emerald-500";
      }
      if (permText) {
        permText.textContent = "Aktif";
        permText.className = "font-bold text-emerald-600";
      }
      if (resetLink) {
        resetLink.classList.add('hidden');
      }
    } else if (state === 'dismissed') {
      if (bannerTitle) bannerTitle.textContent = "Pengingat Antrean (Belum Aktif)";
      if (bannerSubtitle) bannerSubtitle.textContent = "Notifikasi ditunda. Aktifkan agar Anda dapat bersantai tanpa perlu terus melihat layar.";
      if (actionSlot) {
        actionSlot.innerHTML = `
          <button type="button" onclick="requestBrowserNotification()" class="shrink-0 text-xs font-heading font-black px-4 py-2.5 rounded-xl bg-[#1E3A8A] text-white hover:bg-[#2D2DB0] transition-all shadow-xs cursor-pointer">
            Aktifkan
          </button>
        `;
      }
      if (bannerDot) {
        bannerDot.className = "w-2 h-2 rounded-full bg-amber-400";
      }
      if (permText) {
        permText.textContent = "Belum Aktif";
        permText.className = "font-bold text-amber-600";
      }
      if (resetLink) {
        resetLink.classList.add('hidden');
      }
    } else {
      if (bannerTitle) bannerTitle.textContent = "Pengingat Antrean";
      if (bannerSubtitle) bannerSubtitle.textContent = "Aktifkan notifikasi browser agar Anda dapat bersantai tanpa perlu terus melihat layar.";
      if (actionSlot) {
        actionSlot.innerHTML = `
          <button type="button" onclick="requestBrowserNotification()" class="shrink-0 text-xs font-heading font-black px-4 py-2.5 rounded-xl bg-[#1E3A8A] text-white hover:bg-[#2D2DB0] transition-all shadow-xs cursor-pointer">
            Aktifkan
          </button>
        `;
      }
      if (bannerDot) {
        bannerDot.className = "w-2 h-2 rounded-full bg-amber-400";
      }
      if (permText) {
        permText.textContent = "Belum Aktif";
        permText.className = "font-bold text-[#1E3A8A]";
      }
      if (resetLink) {
        resetLink.classList.add('hidden');
      }
    }
  }

  // Request Web Browser Notification Permission & Trigger Test Notification
  async function requestBrowserNotification() {
    sessionStorage.setItem('kothak_notif_seen_' + bookingId, '1');

    if (!("Notification" in window)) {
      showToast("Browser ini belum mendukung Web Notification.");
      closeNotifyModal();
      return;
    }

    if (Notification.permission === "granted") {
      updateNotificationUI('granted');
      closeNotifyModal();
      showToast("Notifikasi browser aktif!");
      sendCurrentQueueNotification();
      return;
    }

    if (Notification.permission === "denied") {
      openNotifyModal();
      showToast("Izin diblokir browser. Buka ikon gembok di kolom URL.");
      return;
    }

    try {
      let perm;
      if (typeof Notification.requestPermission === 'function') {
        const result = Notification.requestPermission();
        if (result && typeof result.then === 'function') {
          perm = await result;
        } else {
          perm = await new Promise(r => Notification.requestPermission(r));
        }
      }

      if (perm === "granted") {
        updateNotificationUI('granted');
        closeNotifyModal();
        showToast("Notifikasi antrean berhasil diaktifkan!");
        sendCurrentQueueNotification();
      } else if (perm === "denied") {
        updateNotificationUI('dismissed');
        openNotifyModal();
      } else {
        dismissNotifyModal();
      }
    } catch(err) {
      console.log('Notification request error:', err);
      closeNotifyModal();
    }
  }

  function copyTicketCode(code) {
    navigator.clipboard.writeText(code).then(() => {
      const btnText = document.getElementById('copyCodeText');
      if (btnText) btnText.textContent = 'Copied!';
      showToast('Kode antrean ' + code + ' disalin!');
      setTimeout(() => {
        if (btnText) btnText.textContent = 'Copy';
      }, 2000);
    }).catch(() => {
      showToast('Kode antrean: ' + code);
    });
  }

  function shareTicket() {
    const ticketUrl = window.location.href;
    const shareData = {
      title: 'Tiket Antrean KOTHaK Photo Studio — ' + @json($booking->queue_number),
      text: 'Ini tiket antrean KOTHaK Photo Studio saya untuk booth ' + @json($booking->studio_backdrop) + ' (Nomor: ' + @json($booking->queue_number) + ').',
      url: ticketUrl
    };

    if (navigator.share) {
      navigator.share(shareData).catch((err) => {
        console.log('Share dismissed:', err);
      });
    } else {
      navigator.clipboard.writeText(ticketUrl).then(() => {
        showToast('Link tiket berhasil disalin ke clipboard!');
      }).catch(() => {
        showToast(ticketUrl);
      });
    }
  }

  // DOM Live Render Helpers (Update tampilan tiket secara instan tanpa flicker / reload)
  function updateQueueDisplay(ahead, booth, isNext) {
    const section = document.getElementById('queueStatusSection');
    if (!section) return;

    if (isNext || ahead === 0) {
      section.innerHTML = `
        <div class="text-[11px] sm:text-xs font-heading font-black uppercase tracking-[0.2em] text-emerald-700 mb-1">
          AHEAD OF YOU (ANTREAN DI DEPANMU)
        </div>
        <div id="aheadCountNumber" class="text-4xl sm:text-5xl lg:text-6xl font-black font-heading tracking-tight text-emerald-600 my-3 animate-pulse">
          Giliran Berikutnya!
        </div>
        <p id="aheadCountDesc" class="text-xs sm:text-sm text-slate-600 font-sans mt-1">
          Kamu adalah antrean terdepan di <strong>${booth}</strong>. Bersiaplah!
        </p>
      `;
    } else {
      section.innerHTML = `
        <div class="text-[11px] sm:text-xs font-heading font-black uppercase tracking-[0.2em] text-slate-500 mb-1">
          ANTREAN DI DEPANMU (AHEAD OF YOU)
        </div>
        <div id="aheadCountNumber" class="text-5xl sm:text-6xl lg:text-7xl font-black font-heading tracking-tight text-[#1E3A8A] my-3">
          ${ahead} <span class="text-2xl sm:text-3xl text-slate-500 font-bold lowercase">antrean</span>
        </div>
        <p id="aheadCountDesc" class="text-xs sm:text-sm text-slate-600 font-sans mt-1">
          Ada ${ahead} pemesan lain sebelum giliran sesi fotomu.
        </p>
      `;
    }
  }

  function renderInStudioView(booth) {
    const section = document.getElementById('queueStatusSection');
    if (!section) return;
    section.innerHTML = `
      <div class="text-[11px] sm:text-xs font-heading font-black uppercase tracking-[0.2em] text-emerald-700 mb-1">
        STATUS BILIK SAAT INI
      </div>
      <div class="text-4xl sm:text-5xl lg:text-6xl font-black font-heading tracking-tight text-emerald-600 my-3 animate-pulse">
        GILIRANMU!
      </div>
      <p class="text-xs sm:text-sm text-slate-600 font-sans mt-1">Silakan segera masuk ke dalam bilik <strong>${booth}</strong> sekarang.</p>
    `;
  }

  function renderCompletedView() {
    const section = document.getElementById('queueStatusSection');
    if (!section) return;
    section.innerHTML = `
      <div class="space-y-3 py-2">
        <div class="w-16 h-16 mx-auto rounded-full bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-700">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div class="text-2xl sm:text-3xl font-heading font-black tracking-tight text-emerald-700">
          Sesi Foto Selesai
        </div>
        <p class="text-xs sm:text-sm text-slate-600 font-sans max-w-md mx-auto leading-relaxed">
          Terima kasih telah berfoto di KOTHaK Photo Studio Purbalingga. Hasil cetak fisik dapat diambil di meja kasir.
        </p>
        <div class="pt-2">
          <a href="{{ route('packages.index') }}" class="inline-block kothak-btn-primary px-6 py-3 text-xs sm:text-sm font-heading font-black tracking-wider uppercase rounded-xl shadow-md transition-all">
            Ambil Antrean Baru &rarr;
          </a>
        </div>
      </div>
    `;
  }

  function renderMissedView() {
    const section = document.getElementById('queueStatusSection');
    if (!section) return;
    section.innerHTML = `
      <div class="space-y-3 py-2">
        <div class="w-16 h-16 mx-auto rounded-full bg-amber-100 border border-amber-300 flex items-center justify-center text-3xl font-black text-amber-700">!</div>
        <div class="text-2xl sm:text-3xl font-heading font-black tracking-tight text-[#1A1A2E]">Nomor Antrean Terlewat</div>
        <p class="text-xs sm:text-sm text-slate-600 font-sans max-w-md mx-auto leading-relaxed">
          Nomor antreanmu sudah terlewat atau dibatalkan. Jangan khawatir, kamu bisa mengambil antrean baru kapan saja.
        </p>
        <div class="pt-2">
          <a href="{{ route('packages.index') }}" class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 text-xs sm:text-sm font-heading font-black tracking-wider uppercase rounded-xl shadow-md transition-all">
            Ambil Antrean Baru &rarr;
          </a>
        </div>
      </div>
    `;
  }

  function updateStatusBadge(label, dotClass) {
    const textEl = document.getElementById('ticketStatusText');
    const dotEl = document.getElementById('ticketStatusDot');
    if (textEl) textEl.textContent = label;
    if (dotEl) dotEl.className = 'w-2.5 h-2.5 rounded-full ' + dotClass;
  }

  let currentAhead = @json($aheadCount);
  let currentStatus = @json($booking->status);
  let boothName = @json($booking->studio_backdrop);
  let queueCode = @json($booking->queue_number);

  // Check existing choice on page load, register ServiceWorker & persist last ticket ID
  document.addEventListener('DOMContentLoaded', () => {
    // Register Service Worker untuk Push Notification di Android Chrome & Background
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/sw.js', { scope: '/' })
        .then(() => console.log('KOTHaK Service Worker aktif'))
        .catch(err => console.log('Service Worker reg notice:', err));
    }

    try {
      localStorage.setItem('kothak_last_ticket_id', @json($booking->id));
      localStorage.setItem('kothak_last_ticket_code', @json($booking->queue_number));
      localStorage.setItem('kothak_last_ticket_booth', @json($booking->studio_backdrop));
    } catch(e) {}

    // 1. Update status kartu notifikasi di sebelah kanan & sinkronkan notifikasi ponsel
    if (("Notification" in window) && Notification.permission === 'granted') {
      updateNotificationUI('granted');
      setTimeout(sendCurrentQueueNotification, 150);
    } else {
      updateNotificationUI('initial');
    }

    // 2. Tampilkan POP-UP MODAL izin notifikasi otomatis langsung saat masuk ke halaman tiket jika belum diizinkan
    setTimeout(() => {
      if (!("Notification" in window) || Notification.permission !== 'granted') {
        openNotifyModal();
      }
    }, 200);
  });

  // REAL-TIME QUEUE POLLING & INSTANT EVENT HANDLER
  let isPollingTicket = false;

  async function pollTicketStatus() {
    if (isPollingTicket) return;
    isPollingTicket = true;

    try {
      const res = await fetch("{{ route('api.booking.status', $booking->id) }}?_t=" + Date.now(), {
        headers: {
          'Cache-Control': 'no-cache, no-store, must-revalidate',
          'Pragma': 'no-cache'
        },
        cache: 'no-store'
      });
      if (!res.ok) return;
      const data = await res.json();

      // CASE 1: JUMLAH ANTREAN DI DEPAN BERUBAH (MAJU)
      if (typeof data.ahead_count === 'number' && data.ahead_count !== currentAhead) {
        currentAhead = data.ahead_count;
        let isNext = (currentAhead === 0);

        // Perbarui notifikasi live di status bar HP & Banner visual
        sendCurrentQueueNotification();

        // Perbarui tampilan antrean di layar secara dinamis
        updateQueueDisplay(currentAhead, boothName, isNext);
      }

      // CASE 2: STATUS TIKET BERUBAH (Misal kasir klik Masuk / in_studio, selesai / completed, atau terlewat)
      if (data.status !== currentStatus) {
        currentStatus = data.status;

        if (data.status === 'in_studio' || data.status === 'calling') {
          sendCurrentQueueNotification();
          renderInStudioView(boothName);
          updateStatusBadge(data.status === 'calling' ? 'Dipanggil (Konfirmasi)' : 'Sedang Sesi Foto', data.status === 'calling' ? 'bg-amber-400 animate-pulse' : 'bg-emerald-500 animate-ping');
        } else if (data.status === 'completed') {
          const compTitle = "Sesi Foto Selesai";
          const compBody = "Terima kasih telah berfoto di KOTHaK Photo Studio. Silakan ambil cetakan foto di kasir.";
          dispatchPushNotification(compTitle, compBody, 'kothak-active-queue');
          renderCompletedView();
          updateStatusBadge('Selesai', 'bg-slate-400');
        } else if (data.status === 'cancelled' || data.status === 'no_show') {
          const cancelTitle = "Nomor Antrean Terlewat";
          const cancelBody = "Nomor antreanmu sudah terlewat atau dibatalkan.";
          dispatchPushNotification(cancelTitle, cancelBody, 'kothak-active-queue');
          renderMissedView();
          updateStatusBadge('Terlewat / Dibatalkan', 'bg-amber-500');
        }
      }
    } catch(e) {
      console.log('Poll ticket error:', e);
    } finally {
      isPollingTicket = false;
    }
  }

  // Handle Event Broadcast Instan Langsung dari Aksi Kasir (0 ms Latensi)
  function handleQueueBroadcast(eventData) {
    if (!eventData) return;

    // Jika antrean ini yang langsung dimasukkan / diubah kasir:
    if (eventData.booking_id && eventData.booking_id == bookingId) {
      if (eventData.status && eventData.status !== currentStatus) {
        currentStatus = eventData.status;

        if (currentStatus === 'in_studio' || currentStatus === 'calling') {
          dispatchPushNotification(
            "Giliranmu Tiba! 🎉",
            "Nomor antrean " + queueCode + " dipanggil masuk ke bilik foto " + boothName + " sekarang!",
            'kothak-call'
          );
          renderInStudioView(boothName);
          updateStatusBadge(currentStatus === 'calling' ? 'Dipanggil (Konfirmasi)' : 'Sedang Sesi Foto', currentStatus === 'calling' ? 'bg-amber-400 animate-pulse' : 'bg-emerald-500 animate-ping');
        } else if (currentStatus === 'completed') {
          dispatchPushNotification(
            "Sesi Foto Selesai",
            "Terima kasih telah berfoto di KOTHaK Photo Studio. Silakan ambil cetakan foto di kasir.",
            'kothak-complete'
          );
          renderCompletedView();
          updateStatusBadge('Selesai', 'bg-slate-400');
        } else if (currentStatus === 'cancelled' || currentStatus === 'no_show') {
          dispatchPushNotification(
            "Nomor Antrean Terlewat",
            "Nomor antreanmu sudah terlewat atau dibatalkan.",
            'kothak-cancel'
          );
          renderMissedView();
          updateStatusBadge('Terlewat / Dibatalkan', 'bg-amber-500');
        }
      }
    } else if (eventData.next_id && eventData.next_id == bookingId) {
      // Jika antrean ini otomatis dipanggil saat kasir menyelesaikan sesi sebelumnya
      const nextStat = eventData.next_status || 'calling';
      if (nextStat !== currentStatus) {
        currentStatus = nextStat;
        dispatchPushNotification(
          "Giliranmu Tiba! 🎉",
          "Nomor antrean " + queueCode + " dipanggil masuk ke bilik foto " + boothName + " sekarang!",
          'kothak-call'
        );
        renderInStudioView(boothName);
        updateStatusBadge('Dipanggil (Konfirmasi)', 'bg-amber-400 animate-pulse');
      }
    }

    // Segera perbarui antrean di depan (aheadCount) langsung dari server
    pollTicketStatus();
  }

  // 1. Sinkronisasi Antar-Tab Instan via BroadcastChannel (0 milidetik respon ketika kasir melakukan aksi)
  try {
    if ('BroadcastChannel' in window) {
      const liveQueueChannel = new BroadcastChannel('kothak_live_queue');
      liveQueueChannel.onmessage = function(ev) {
        handleQueueBroadcast(ev.data);
      };
    }
  } catch(e) {}

  // 2. Sinkronisasi Antar-Tab via Storage Event (Cross-Window / Cross-Tab Instant)
  window.addEventListener('storage', function(e) {
    if (e.key === 'kothak_queue_bump') {
      try {
        const parsed = JSON.parse(e.newValue);
        handleQueueBroadcast(parsed);
      } catch(err) {
        pollTicketStatus();
      }
    }
  });

  // 3. Ultra Fast Fallback Polling (Setiap 400ms / 0.4 detik untuk respon instan)
  setInterval(pollTicketStatus, 400);
</script>
@endpush
