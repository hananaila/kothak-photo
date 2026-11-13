<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>KOTHaK Cafe &amp; Photo — Smart Queue &amp; Booking System</title>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet" />
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            kothak: {
              cream: '#F5F4EE',
              paper: '#FAF8F5',
              blue: '#1D4ED8',
              cobalt: '#1E40AF',
              royal: '#2563EB',
              ice: '#E0E7FF',
              ink: '#0F172A',
              mint: '#10B981'
            }
          },
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
            serif: ['"Instrument Serif"', 'Georgia', 'serif'],
            mono: ['"JetBrains Mono"', 'monospace']
          }
        }
      }
    };
  </script>
  <style>
    body {
      background-color: #F5F4EE;
      color: #0F172A;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .font-editorial {
      font-family: 'Instrument Serif', Georgia, serif;
    }
    .neo-box {
      border: 2px solid #0F172A;
      box-shadow: 4px 4px 0px #1D4ED8;
    }
    .neo-box-dark {
      border: 2px solid #1D4ED8;
      box-shadow: 4px 4px 0px #0F172A;
    }
    .neo-box-white {
      border: 2px solid #FFFFFF;
      box-shadow: 4px 4px 0px #93C5FD;
    }
    .neo-btn {
      border: 2px solid #0F172A;
      box-shadow: 3px 3px 0px #0F172A;
      transition: transform 0.12s ease, box-shadow 0.12s ease;
    }
    .neo-btn:hover:not(:disabled) {
      transform: translate(-1px, -1px);
      box-shadow: 5px 5px 0px #0F172A;
    }
    .neo-btn:active:not(:disabled) {
      transform: translate(2px, 2px);
      box-shadow: 1px 1px 0px #0F172A;
    }
    .checker-blue {
      background-image:
        linear-gradient(45deg, #1D4ED8 25%, transparent 25%),
        linear-gradient(-45deg, #1D4ED8 25%, transparent 25%),
        linear-gradient(45deg, transparent 75%, #1D4ED8 75%),
        linear-gradient(-45deg, transparent 75%, #1D4ED8 75%);
      background-size: 20px 20px;
      background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
    }
    .checker-white {
      background-image:
        linear-gradient(45deg, rgba(255,255,255,0.16) 25%, transparent 25%),
        linear-gradient(-45deg, rgba(255,255,255,0.16) 25%, transparent 25%),
        linear-gradient(45deg, transparent 75%, rgba(255,255,255,0.16) 75%),
        linear-gradient(-45deg, transparent 75%, rgba(255,255,255,0.16) 75%);
      background-size: 24px 24px;
      background-position: 0 0, 0 12px, 12px -12px, -12px 0px;
    }
  </style>
</head>
<body class="min-h-screen flex flex-col selection:bg-blue-700 selection:text-white">

  <!-- TOP DEVELOPER & ROLE SWITCHER BAR -->
  <div class="bg-slate-900 text-white border-b-2 border-blue-600 px-4 py-2 text-xs">
    <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
      <div class="flex items-center gap-2">
        <span class="bg-blue-600 text-white font-bold px-2 py-0.5 uppercase tracking-wider">Full-Stack Blueprint v1.0</span>
        <span class="text-slate-300 hidden sm:inline">Selaras UI/UX dengan</span>
        <a href="https://kothak-cafe.frostyice.id/" target="_blank" rel="noopener noreferrer" class="text-blue-300 underline hover:text-white font-mono">kothak-cafe.frostyice.id</a>
      </div>
      <div class="flex flex-wrap items-center gap-1.5">
        <span class="text-slate-400 mr-1">Mode Tampilan:</span>
        <button type="button" id="tabBtnCustomer" onclick="switchWorkspaceTab('customer')" class="px-2.5 py-1 font-bold uppercase tracking-wider bg-blue-600 text-white border border-blue-400">
          1. Beranda &amp; Pilih Paket (Customer)
        </button>
        <button type="button" id="tabBtnFolder" onclick="switchWorkspaceTab('folder')" class="px-2.5 py-1 font-bold uppercase tracking-wider bg-slate-800 text-slate-300 hover:text-white border border-slate-700">
          2. Struktur Folder (XAMPP/Laragon)
        </button>
        <button type="button" id="tabBtnSql" onclick="switchWorkspaceTab('sql')" class="px-2.5 py-1 font-bold uppercase tracking-wider bg-slate-800 text-slate-300 hover:text-white border border-slate-700">
          3. Skema Database SQL
        </button>
      </div>
    </div>
  </div>

  <!-- STICKY HEADER (IDENTICAL TO KOTHAK-CAFE.FROSTYICE.ID) -->
  <header class="sticky top-0 z-40 bg-[#F5F4EE]/95 backdrop-blur-sm border-b-2 border-blue-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
      <!-- Brand Logo Grid -->
      <a href="#heroSection" onclick="switchWorkspaceTab('customer')" class="flex items-center gap-3 group focus:outline-none">
        <div class="w-12 h-12 bg-blue-700 text-white border-2 border-slate-900 flex flex-col justify-center items-center leading-none font-extrabold tracking-tighter select-none">
          <span class="text-sm">KO</span>
          <span class="text-xs bg-white text-blue-700 px-1 font-black">THaK</span>
        </div>
        <div>
          <div class="font-extrabold text-lg sm:text-xl tracking-tight text-slate-900 leading-none">
            KOTHaK <span class="text-blue-700">Cafe &amp; Photo</span>
          </div>
          <div class="text-[11px] font-semibold uppercase tracking-widest text-slate-600 mt-1">
            Photobox Studio &bull; Smart Queue Purbalingga
          </div>
        </div>
      </a>

      <!-- Desktop Nav Links -->
      <nav class="hidden md:flex items-center gap-6 text-xs font-extrabold tracking-widest uppercase">
        <a href="#heroSection" onclick="switchWorkspaceTab('customer')" class="text-slate-900 hover:text-blue-700 py-1 border-b-2 border-blue-700">HOME</a>
        <a href="#packagesSection" onclick="switchWorkspaceTab('customer')" class="text-slate-700 hover:text-blue-700 py-1 border-b-2 border-transparent hover:border-blue-700">PHOTO PACKAGES</a>
        <a href="#bookingSection" onclick="switchWorkspaceTab('customer')" class="text-slate-700 hover:text-blue-700 py-1 border-b-2 border-transparent hover:border-blue-700">BOOKING &amp; QRIS</a>
        <a href="#queueSection" onclick="switchWorkspaceTab('customer')" class="text-slate-700 hover:text-blue-700 py-1 border-b-2 border-transparent hover:border-blue-700 flex items-center gap-1.5">
          <span class="w-2 h-2 bg-emerald-500 inline-block animate-pulse"></span>
          SMART QUEUE
        </a>
        <a href="https://kothak-cafe.frostyice.id/" target="_blank" rel="noopener noreferrer" class="text-slate-700 hover:text-blue-700 py-1 border-b-2 border-transparent hover:border-blue-700">CAFE MENU ↗</a>
      </nav>

      <!-- User Auth & Active Ticket Pill -->
      <div class="flex items-center gap-2.5">
        <button type="button" onclick="openAuthModal()" id="authStatusBtn" class="neo-btn bg-white hover:bg-blue-50 text-slate-900 px-3.5 py-2 text-xs font-extrabold uppercase tracking-wider flex items-center gap-2">
          <span class="w-2 h-2 bg-blue-700"></span>
          <span id="currentUserLabel">Hana Naila (Customer)</span>
        </button>
        <button type="button" onclick="toggleMobileMenu()" class="md:hidden border-2 border-slate-900 bg-white p-2 text-slate-900" aria-label="Toggle Menu">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="square" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div id="mobileMenuPanel" class="hidden md:hidden bg-[#F5F4EE] border-t-2 border-slate-900 px-4 py-4 space-y-2 text-xs font-extrabold uppercase tracking-widest">
      <a href="#heroSection" onclick="switchWorkspaceTab('customer'); toggleMobileMenu();" class="block py-2 text-blue-700">HOME</a>
      <a href="#packagesSection" onclick="switchWorkspaceTab('customer'); toggleMobileMenu();" class="block py-2 text-slate-900">PHOTO PACKAGES</a>
      <a href="#bookingSection" onclick="switchWorkspaceTab('customer'); toggleMobileMenu();" class="block py-2 text-slate-900">BOOKING &amp; QRIS</a>
      <a href="#queueSection" onclick="switchWorkspaceTab('customer'); toggleMobileMenu();" class="block py-2 text-slate-900">SMART QUEUE LIVE</a>
      <button type="button" onclick="switchWorkspaceTab('folder'); toggleMobileMenu();" class="block w-full text-left py-2 text-blue-700">STRUKTUR FOLDER PROYEK</button>
      <button type="button" onclick="switchWorkspaceTab('sql'); toggleMobileMenu();" class="block w-full text-left py-2 text-blue-700">SKEMA DATABASE MYSQL</button>
    </div>
  </header>

  <!-- ===================================================================== -->
  <!-- VIEW 1: CUSTOMER FRONT-END (BERANDA, PEMILIHAN PAKET, BOOKING & QUEUE) -->
  <!-- ===================================================================== -->
  <main id="viewCustomer" class="flex-1">

    <!-- HERO SECTION (DEEP ROYAL BLUE #1D4ED8 + CHECKERBOARD) -->
    <section id="heroSection" class="bg-blue-700 text-white border-b-2 border-slate-900 relative overflow-hidden">
      <div class="checker-white absolute inset-0 pointer-events-none"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
          <!-- Left Column: Brand Story & CTA -->
          <div class="lg:col-span-7 space-y-6">
            <div class="inline-flex items-center gap-2 bg-slate-900 text-white border-2 border-white px-3 py-1 text-xs font-extrabold uppercase tracking-widest">
              <span class="w-2 h-2 bg-emerald-400 animate-ping"></span>
              <span>#1 Photobox Cafe in Purbalingga &bull; Smart Queue System</span>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.06]">
              Coffee. Photo. <span class="font-editorial italic font-normal text-blue-200 underline decoration-2 underline-offset-8">Memory.</span>
            </h1>

            <p class="text-base sm:text-lg text-blue-100 max-w-2xl leading-relaxed font-medium">
              Cafe photobox di Purbalingga — <span class="italic">where every moment finds its frame</span>. Pilih paket foto studio estetik, kunci slot jadwal harian tanpa antre berdiri, scan QRIS statis, dan pantau nomor antrean Anda secara real-time sambil menikmati kopi Kothak.
            </p>

            <!-- CTA Buttons matching Kothak Cafe reference -->
            <div class="flex flex-wrap items-center gap-4 pt-2">
              <a href="#packagesSection" class="bg-white text-blue-700 hover:bg-blue-50 border-2 border-slate-900 px-6 py-3.5 text-xs sm:text-sm font-extrabold uppercase tracking-widest shadow-[4px_4px_0px_#0F172A] transition-transform hover:-translate-y-0.5">
                PILIH PAKET FOTO ➔
              </a>
              <a href="#queueSection" class="bg-transparent text-white hover:bg-blue-800 border-2 border-white px-6 py-3.5 text-xs sm:text-sm font-extrabold uppercase tracking-widest transition-colors">
                LIHAT SMART QUEUE LIVE
              </a>
            </div>

            <!-- Verified Location & Hours Strip -->
            <div class="pt-4 border-t border-blue-500/60 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
              <div>
                <div class="text-blue-200 uppercase font-bold tracking-wider">LOKASI STUDIO</div>
                <div class="font-semibold text-white mt-0.5">Jl. AW Sumarmo No.39, Kembaran Kulon, Purbalingga</div>
              </div>
              <div>
                <div class="text-blue-200 uppercase font-bold tracking-wider">JAM OPERASIONAL</div>
                <div class="font-semibold text-white mt-0.5">Everyday 09:00 AM – 11:00 PM</div>
              </div>
              <div>
                <div class="text-blue-200 uppercase font-bold tracking-wider">FASILITAS PHOTOBOX</div>
                <div class="font-semibold text-white mt-0.5">2 Background Estetik + Cetak &amp; Soft File</div>
              </div>
            </div>
          </div>

          <!-- Right Column: Live Smart Queue Monitor Card in Hero -->
          <div class="lg:col-span-5">
            <div class="bg-[#F5F4EE] text-slate-900 border-2 border-slate-900 shadow-[8px_8px_0px_#0F172A] p-6">
              <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-4">
                <div>
                  <span class="text-[11px] font-extrabold uppercase tracking-widest text-blue-700 block">REAL-TIME STUDIO MONITOR</span>
                  <h2 class="text-lg font-extrabold text-slate-900">Status Antrean Photobox</h2>
                </div>
                <span class="bg-emerald-500 text-slate-900 border-2 border-slate-900 px-2.5 py-1 text-[11px] font-extrabold uppercase tracking-wider">
                  STUDIO AKTIF
                </span>
              </div>

              <!-- Active Queue Number Box -->
              <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-blue-700 text-white border-2 border-slate-900 p-4">
                  <div class="text-[10px] font-bold uppercase tracking-widest text-blue-200">SEDANG DI STUDIO</div>
                  <div id="heroActiveQueue" class="text-3xl font-black font-mono tracking-tight mt-1">KTH-012</div>
                  <div id="heroActivePackage" class="text-[11px] text-blue-100 mt-1 truncate">Kothak Signature Box</div>
                </div>
                <div class="bg-white text-slate-900 border-2 border-slate-900 p-4">
                  <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500">PANGGILAN BERIKUTNYA</div>
                  <div id="heroNextQueue" class="text-3xl font-black font-mono tracking-tight text-blue-700 mt-1">KTH-013</div>
                  <div class="text-[11px] text-slate-600 mt-1">Estimasi ~<span id="heroWaitMinutes">10</span> menit lagi</div>
                </div>
              </div>

              <!-- Studio Background Status -->
              <div class="bg-white border-2 border-slate-900 p-3 mb-4 text-xs space-y-2">
                <div class="flex items-center justify-between">
                  <span class="font-bold text-slate-700">Booth A (Electric Blue Backdrop)</span>
                  <span class="font-mono font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 border border-emerald-700">READY &bull; Sisa 14 Slot</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="font-bold text-slate-700">Booth B (Warm Cream Backdrop)</span>
                  <span class="font-mono font-bold text-blue-700 bg-blue-100 px-2 py-0.5 border border-blue-700">IN SESSION &bull; Sisa 11 Slot</span>
                </div>
              </div>

              <div class="flex items-center justify-between text-xs pt-1">
                <span class="text-slate-600 font-medium">Antrean menunggu: <strong id="heroWaitingCount" class="text-slate-900">3 pelanggan</strong></span>
                <a href="#bookingSection" class="font-extrabold text-blue-700 hover:underline uppercase tracking-wider">Ambil Antrean ➔</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- BUSINESS LOGIC STEPPER BAR (ALUR PROSES 4 TAHAP) -->
    <section class="bg-white border-b-2 border-slate-900 py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="border-2 border-slate-900 bg-[#F5F4EE] p-4 flex items-start gap-3">
            <span class="bg-blue-700 text-white font-mono font-extrabold text-xs px-2 py-1 border border-slate-900">01</span>
            <div>
              <div class="font-extrabold text-xs uppercase tracking-wider text-slate-900">Pilih Paket Foto</div>
              <p class="text-xs text-slate-600 mt-0.5">Tentukan paket photobox, durasi sesi, dan tema background studio.</p>
            </div>
          </div>
          <div class="border-2 border-slate-900 bg-[#F5F4EE] p-4 flex items-start gap-3">
            <span class="bg-blue-700 text-white font-mono font-extrabold text-xs px-2 py-1 border border-slate-900">02</span>
            <div>
              <div class="font-extrabold text-xs uppercase tracking-wider text-slate-900">Pilih Jadwal Slot</div>
              <p class="text-xs text-slate-600 mt-0.5">Kunci slot waktu harian yang masih tersedia (anti double-booking).</p>
            </div>
          </div>
          <div class="border-2 border-slate-900 bg-[#F5F4EE] p-4 flex items-start gap-3">
            <span class="bg-blue-700 text-white font-mono font-extrabold text-xs px-2 py-1 border border-slate-900">03</span>
            <div>
              <div class="font-extrabold text-xs uppercase tracking-wider text-slate-900">QRIS Statis &amp; Upload</div>
              <p class="text-xs text-slate-600 mt-0.5">Scan QRIS Statis Kothak Photo lalu unggah bukti transfer pembayaran.</p>
            </div>
          </div>
          <div class="border-2 border-slate-900 bg-[#F5F4EE] p-4 flex items-start gap-3">
            <span class="bg-blue-700 text-white font-mono font-extrabold text-xs px-2 py-1 border border-slate-900">04</span>
            <div>
              <div class="font-extrabold text-xs uppercase tracking-wider text-slate-900">Tiket WA &amp; Smart Queue</div>
              <p class="text-xs text-slate-600 mt-0.5">Kasir memverifikasi, tiket dikirim ke WhatsApp, &amp; pantau antrean live.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================================================================== -->
    <!-- SECTION 1: PEMILIHAN PAKET FOTO (CUSTOMER PACKAGE CATALOG)             -->
    <!-- ===================================================================== -->
    <section id="packagesSection" class="py-14 border-b-2 border-slate-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header matching "WHAT WE OFFER" style -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
          <div>
            <div class="text-xs font-extrabold uppercase tracking-[0.2em] text-blue-700 mb-1">WHAT WE OFFER &bull; PHOTOBOX STUDIO</div>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
              Pilih Paket Foto <span class="font-editorial italic font-normal text-blue-700">Kothak</span> Anda
            </h2>
            <p class="text-sm text-slate-600 mt-1 max-w-2xl">
              Seluruh paket sudah termasuk pencahayaan studio profesional, properti foto gratis, cetak strip instan, dan unduhan soft file digital resolusi tinggi.
            </p>
          </div>

          <!-- Search & Filter Controls -->
          <div class="flex flex-wrap items-center gap-2">
            <input
              type="text"
              id="packageSearchInput"
              oninput="renderPackages()"
              placeholder="Cari paket (mis. Bestie, Kopi)..."
              class="bg-white border-2 border-slate-900 px-3.5 py-2 text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-700 w-56"
            />
          </div>
        </div>

        <!-- Category Filter Buttons (Neo-Brutalist Square Style) -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8 pb-4 border-b-2 border-slate-900/15">
          <div class="flex flex-wrap items-center gap-2" id="categoryFilterBar">
            <button type="button" onclick="setCategoryFilter('ALL')" data-cat="ALL" class="cat-filter-btn bg-blue-700 text-white border-2 border-slate-900 px-4 py-2 text-xs font-extrabold uppercase tracking-wider">
              ALL PACKAGES (6)
            </button>
            <button type="button" onclick="setCategoryFilter('SOLO_DUO')" data-cat="SOLO_DUO" class="cat-filter-btn bg-white text-slate-900 hover:bg-blue-50 border-2 border-slate-900 px-4 py-2 text-xs font-extrabold uppercase tracking-wider">
              SOLO &amp; COUPLE
            </button>
            <button type="button" onclick="setCategoryFilter('GROUP')" data-cat="GROUP" class="cat-filter-btn bg-white text-slate-900 hover:bg-blue-50 border-2 border-slate-900 px-4 py-2 text-xs font-extrabold uppercase tracking-wider">
              GROUP &amp; BESTIE
            </button>
            <button type="button" onclick="setCategoryFilter('BUNDLING')" data-cat="BUNDLING" class="cat-filter-btn bg-white text-slate-900 hover:bg-blue-50 border-2 border-slate-900 px-4 py-2 text-xs font-extrabold uppercase tracking-wider">
              COFFEE + PHOTO BUNDLE
            </button>
          </div>

          <!-- Studio Backdrop Preference Selector -->
          <div class="flex items-center gap-2 text-xs">
            <span class="font-bold uppercase tracking-wider text-slate-700">Pilihan Background:</span>
            <select id="backdropFilterSelect" onchange="renderPackages()" class="bg-white border-2 border-slate-900 px-3 py-2 font-bold text-slate-900 focus:outline-none">
              <option value="ALL">Semua Background (2 Studio)</option>
              <option value="Electric Blue Backdrop">Bg 1: Electric Blue Backdrop</option>
              <option value="Warm Cream Minimalist">Bg 2: Warm Cream Minimalist</option>
            </select>
          </div>
        </div>

        <!-- Package Cards Grid (Populated from `packages` entity) -->
        <div id="packagesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Dynamically rendered by JS -->
        </div>
      </div>
    </section>

    <!-- ===================================================================== -->
    <!-- SECTION 2: INTERACTIVE BOOKING, SLOT SCHEDULE & STATIC QRIS CHECKOUT  -->
    <!-- ===================================================================== -->
    <section id="bookingSection" class="py-14 bg-[#FAF8F5] border-b-2 border-slate-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
          <div class="text-xs font-extrabold uppercase tracking-[0.2em] text-blue-700 mb-1">RESERVASI &amp; PEMBAYARAN QRIS STATIS</div>
          <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">
            Atur Jadwal Slot &amp; Checkout <span class="font-editorial italic font-normal text-blue-700">Instan</span>
          </h2>
          <p class="text-sm text-slate-600 mt-1">
            Pilih tanggal dan jam sesi foto Anda. Sistem mengunci kuota dengan presisi milidetik untuk mencegah bentrok jadwal (<span class="font-mono text-xs">double-booking</span>).
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          <!-- Left 7 Cols: Step 1 (Selected Package) & Step 2 (Schedule Slot Picker) -->
          <div class="lg:col-span-7 space-y-6">
            <!-- Selected Package Banner -->
            <div class="neo-box bg-white p-5">
              <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-4">
                <span class="text-xs font-extrabold uppercase tracking-widest text-blue-700">LANGKAH 1 &bull; PAKET FOTO TERPILIH</span>
                <a href="#packagesSection" class="text-xs font-bold text-slate-600 underline hover:text-blue-700">Ubah Paket</a>
              </div>
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                  <div class="flex items-center gap-2">
                    <span id="selectedPkgBadge" class="bg-blue-700 text-white text-[10px] font-extrabold uppercase px-2 py-0.5">BEST SELLER</span>
                    <span id="selectedPkgDuration" class="font-mono text-xs font-bold text-slate-600">15 Menit / Sesi</span>
                  </div>
                  <h3 id="selectedPkgName" class="text-xl font-extrabold text-slate-900 mt-1">Kothak Signature Box (2–4 Orang)</h3>
                  <p id="selectedPkgDesc" class="text-xs text-slate-600 mt-1">Sesi photobox 15 menit, 2 strip cetak foto ukuran 4R, semua soft file HD + GIF via Google Drive/QR.</p>
                </div>
                <div class="sm:text-right shrink-0 bg-[#F5F4EE] border-2 border-slate-900 px-4 py-3">
                  <div class="text-[10px] font-bold uppercase text-slate-500">HARGA PAKET</div>
                  <div id="selectedPkgPrice" class="text-2xl font-black text-blue-700 font-mono">Rp 35.000</div>
                </div>
              </div>
            </div>

            <!-- Date & Time Slot Picker (`schedules` table) -->
            <div class="neo-box bg-white p-5">
              <div class="flex flex-wrap items-center justify-between border-b-2 border-slate-900 pb-3 mb-4 gap-2">
                <span class="text-xs font-extrabold uppercase tracking-widest text-blue-700">LANGKAH 2 &bull; PILIH TANGGAL &amp; SLOT JADWAL</span>
                <span class="text-[11px] font-mono text-slate-600">Jam Operasional: 09:00 - 23:00 WIB</span>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                  <label for="bookingDateInput" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">Tanggal Reservasi</label>
                  <input
                    type="date"
                    id="bookingDateInput"
                    value="2026-11-13"
                    min="2026-11-13"
                    onchange="renderScheduleSlots()"
                    class="w-full bg-[#F5F4EE] border-2 border-slate-900 px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:border-blue-700"
                  />
                </div>
                <div>
                  <label for="studioBackdropSelect" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">Pilih Background Studio</label>
                  <select
                    id="studioBackdropSelect"
                    onchange="updateCheckoutSummary()"
                    class="w-full bg-[#F5F4EE] border-2 border-slate-900 px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:border-blue-700"
                  >
                    <option value="Background 1: Electric Blue (Signature)">Background 1: Electric Blue (Signature)</option>
                    <option value="Background 2: Warm Cream Minimalist">Background 2: Warm Cream Minimalist</option>
                  </select>
                </div>
              </div>

              <!-- Slot Grid -->
              <div class="mb-2 flex items-center justify-between text-xs">
                <span class="font-extrabold uppercase tracking-wider text-slate-700">Slot Waktu Tersedia (<span id="slotDateLabel">13 Nov 2026</span>):</span>
                <div class="flex items-center gap-3 text-[11px]">
                  <span class="inline-flex items-center gap-1"><span class="w-2.5 h-2.5 bg-white border border-slate-900 inline-block"></span> Tersedia</span>
                  <span class="inline-flex items-center gap-1"><span class="w-2.5 h-2.5 bg-blue-700 border border-slate-900 inline-block"></span> Dipilih</span>
                  <span class="inline-flex items-center gap-1"><span class="w-2.5 h-2.5 bg-slate-200 border border-slate-400 inline-block"></span> Penuh</span>
                </div>
              </div>

              <div id="scheduleSlotsGrid" class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 mt-3">
                <!-- Dynamically rendered by JS -->
              </div>
            </div>
          </div>

          <!-- Right 5 Cols: Step 3 (Static QRIS & Proof Upload) -->
          <div class="lg:col-span-5">
            <div class="neo-box bg-white p-6 sticky top-24">
              <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-4">
                <span class="text-xs font-extrabold uppercase tracking-widest text-blue-700">LANGKAH 3 &bull; CHECKOUT QRIS STATIS</span>
                <span class="bg-blue-100 text-blue-800 border border-blue-700 px-2 py-0.5 text-[10px] font-extrabold uppercase">MANUAL VERIFICATION</span>
              </div>

              <!-- Summary Table -->
              <div class="bg-[#F5F4EE] border-2 border-slate-900 p-3.5 text-xs space-y-2 mb-4">
                <div class="flex justify-between">
                  <span class="text-slate-600">Pelanggan:</span>
                  <span id="summaryCustomerName" class="font-bold text-slate-900">Hana Naila Rahmadina</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-slate-600">Paket Foto:</span>
                  <span id="summaryPkgName" class="font-bold text-slate-900">Kothak Signature Box</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-slate-600">Jadwal Slot:</span>
                  <span id="summarySlotTime" class="font-mono font-bold text-blue-700">13 Nov 2026 &bull; 15:00 - 15:30</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-slate-600">Background:</span>
                  <span id="summaryBackdrop" class="font-semibold text-slate-900">Background 1: Electric Blue</span>
                </div>
                <div class="border-t border-slate-300 pt-2 flex justify-between items-baseline">
                  <span class="font-extrabold uppercase text-slate-900">Total Bayar:</span>
                  <span id="summaryTotalPrice" class="text-xl font-black font-mono text-blue-700">Rp 35.000</span>
                </div>
              </div>

              <!-- Static QRIS Card -->
              <div class="border-2 border-slate-900 bg-white p-4 text-center mb-4">
                <div class="flex items-center justify-between text-[10px] font-extrabold uppercase tracking-wider text-slate-700 border-b border-slate-200 pb-2 mb-3">
                  <span>QRIS STATIS RESMI</span>
                  <span class="font-mono text-blue-700">NMID: ID2026KOTHAKPBG01</span>
                </div>
                <div class="font-extrabold text-xs text-slate-900 uppercase tracking-wider">KOTHaK CAFE &amp; PHOTO PURBALINGGA</div>
                <div class="text-[11px] text-slate-500 mb-3">Scan menggunakan Livin' by Mandiri, GoPay, OVO, DANA, ShopeePay, atau Bank Jago</div>

                <!-- Crisp Custom SVG QRIS Barcode -->
                <div class="mx-auto w-44 h-44 border-2 border-slate-900 p-2 bg-white flex items-center justify-center">
                  <svg viewBox="0 0 120 120" class="w-full h-full" role="img" aria-label="QRIS Statis Kothak Photo">
                    <rect width="120" height="120" fill="#FFFFFF"/>
                    <!-- Finder Patterns -->
                    <rect x="8" y="8" width="28" height="28" fill="none" stroke="#0F172A" stroke-width="4"/>
                    <rect x="14" y="14" width="16" height="16" fill="#1D4ED8"/>
                    <rect x="84" y="8" width="28" height="28" fill="none" stroke="#0F172A" stroke-width="4"/>
                    <rect x="90" y="14" width="16" height="16" fill="#1D4ED8"/>
                    <rect x="8" y="84" width="28" height="28" fill="none" stroke="#0F172A" stroke-width="4"/>
                    <rect x="14" y="90" width="16" height="16" fill="#1D4ED8"/>
                    <!-- Data Matrix Modules -->
                    <g fill="#0F172A">
                      <rect x="44" y="8" width="6" height="6"/><rect x="56" y="8" width="6" height="12"/><rect x="68" y="8" width="6" height="6"/>
                      <rect x="44" y="20" width="12" height="6"/><rect x="68" y="20" width="12" height="6"/>
                      <rect x="44" y="32" width="6" height="6"/><rect x="56" y="32" width="18" height="6"/>
                      <rect x="8" y="44" width="12" height="6"/><rect x="26" y="44" width="6" height="12"/><rect x="38" y="44" width="6" height="6"/>
                      <rect x="8" y="56" width="6" height="12"/><rect x="20" y="62" width="12" height="6"/>
                      <rect x="8" y="74" width="18" height="6"/><rect x="32" y="74" width="6" height="6"/>
                      <rect x="84" y="44" width="6" height="12"/><rect x="96" y="44" width="16" height="6"/>
                      <rect x="78" y="56" width="12" height="6"/><rect x="102" y="56" width="10" height="12"/>
                      <rect x="84" y="68" width="12" height="6"/><rect x="78" y="80" width="6" height="12"/>
                      <rect x="44" y="84" width="12" height="6"/><rect x="62" y="84" width="6" height="18"/>
                      <rect x="44" y="96" width="6" height="16"/><rect x="56" y="106" width="18" height="6"/>
                      <rect x="84" y="96" width="12" height="6"/><rect x="102" y="90" width="10" height="10"/>
                      <rect x="90" y="106" width="22" height="6"/>
                    </g>
                    <!-- Center Kothak Badge -->
                    <rect x="46" y="46" width="28" height="28" fill="#1D4ED8" stroke="#0F172A" stroke-width="2"/>
                    <text x="60" y="59" text-anchor="middle" fill="#FFFFFF" font-size="7" font-weight="800" font-family="sans-serif">KOTHaK</text>
                    <text x="60" y="68" text-anchor="middle" fill="#E0E7FF" font-size="6" font-weight="700" font-family="monospace">QRIS</text>
                  </svg>
                </div>
              </div>

              <!-- Upload Proof of Transfer (`payments.proof_image_path`) -->
              <div class="space-y-3">
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-900">
                  Unggah Foto Bukti Transfer (JPG/PNG) *
                </label>
                <div class="flex gap-2">
                  <input
                    type="file"
                    id="proofFileInput"
                    accept="image/*"
                    onchange="handleProofFileChange(event)"
                    class="block w-full text-xs text-slate-700 file:mr-3 file:py-2 file:px-3 file:border-2 file:border-slate-900 file:text-xs file:font-extrabold file:uppercase file:bg-[#F5F4EE] file:text-slate-900 hover:file:bg-blue-50 border-2 border-slate-900 bg-white p-1"
                  />
                  <button
                    type="button"
                    onclick="simulateSampleProof()"
                    class="neo-btn bg-blue-100 hover:bg-blue-200 text-blue-900 px-3 py-1.5 text-[11px] font-extrabold uppercase shrink-0"
                    title="Gunakan contoh bukti transfer untuk simulasi cepat"
                  >
                    + Contoh Bukti
                  </button>
                </div>

                <!-- Preview Uploaded Proof -->
                <div id="proofPreviewBox" class="hidden border-2 border-emerald-700 bg-emerald-50 p-2.5 text-xs flex items-center justify-between">
                  <div class="flex items-center gap-2 truncate">
                    <span class="bg-emerald-600 text-white font-mono text-[10px] font-bold px-1.5 py-0.5">READY</span>
                    <span id="proofFileName" class="font-mono text-slate-800 truncate">bukti_qris_mandiri_35000.jpg</span>
                  </div>
                  <button type="button" onclick="clearProofFile()" class="text-rose-700 font-bold hover:underline ml-2">Hapus</button>
                </div>

                <!-- WhatsApp Number for Notification -->
                <div>
                  <label for="customerWaInput" class="block text-xs font-extrabold uppercase tracking-wider text-slate-900 mb-1">
                    Nomor WhatsApp (Tiket Antrean)
                  </label>
                  <input
                    type="tel"
                    id="customerWaInput"
                    value="0812-3456-7890"
                    placeholder="08xx-xxxx-xxxx"
                    class="w-full bg-[#F5F4EE] border-2 border-slate-900 px-3 py-2 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-blue-700"
                  />
                </div>

                <button
                  type="button"
                  onclick="submitBookingCheckout()"
                  class="neo-btn w-full bg-blue-700 hover:bg-blue-800 text-white py-3.5 px-5 text-xs sm:text-sm font-extrabold uppercase tracking-widest mt-2"
                >
                  KIRIM BUKTI BAYAR &amp; AMBIL ANTREAN ➔
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================================================================== -->
    <!-- SECTION 3: DASBOR SMART QUEUE & SIMULASI VERIFIKASI KASIR             -->
    <!-- ===================================================================== -->
    <section id="queueSection" class="py-14 bg-[#F5F4EE]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
          <div>
            <div class="text-xs font-extrabold uppercase tracking-[0.2em] text-blue-700 mb-1">REAL-TIME SMART QUEUE &amp; ROLE SIMULATION</div>
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">
              Dasbor Antrean Pintar &amp; <span class="font-editorial italic font-normal text-blue-700">Simulasi Kasir</span>
            </h2>
            <p class="text-sm text-slate-600 mt-1">
              Pantau status antrean pelanggan secara langsung. Anda juga dapat menguji tombol aksi <strong>Kasir (Accept Bukti Bayar, Check-In, &amp; Panggil Antrean)</strong> di tabel bawah.
            </p>
          </div>
          <div class="flex flex-wrap items-center gap-2">
            <button
              type="button"
              onclick="advanceNextQueue()"
              class="neo-btn bg-emerald-500 hover:bg-emerald-400 text-slate-900 px-4 py-2.5 text-xs font-extrabold uppercase tracking-wider"
            >
              🔊 [Simulasi Kasir] Panggil Antrean Berikutnya
            </button>
          </div>
        </div>

        <!-- Queue Table -->
        <div class="neo-box bg-white overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-blue-700 text-white border-b-2 border-slate-900 text-[11px] font-extrabold uppercase tracking-wider">
                <th class="py-3.5 px-4">No. Antrean</th>
                <th class="py-3.5 px-4">Pelanggan &amp; WA</th>
                <th class="py-3.5 px-4">Paket &amp; Background</th>
                <th class="py-3.5 px-4">Jadwal Slot</th>
                <th class="py-3.5 px-4">Bukti QRIS (`payments`)</th>
                <th class="py-3.5 px-4">Status Booking</th>
                <th class="py-3.5 px-4 text-right">Simulasi Aksi Kasir</th>
              </tr>
            </thead>
            <tbody id="smartQueueTableBody" class="divide-y-2 divide-slate-900 text-xs">
              <!-- Dynamically rendered by JS -->
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <!-- ===================================================================== -->
  <!-- VIEW 2: STRUKTUR FOLDER PROYEK (XAMPP / LARAGON ARCHITECTURE)         -->
  <!-- ===================================================================== -->
  <section id="viewFolder" class="hidden flex-1 py-12 bg-[#F5F4EE]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
        <div>
          <div class="text-xs font-extrabold uppercase tracking-[0.2em] text-blue-700 mb-1">TUGAS 1A &bull; ARSITEKTUR LOKAL XAMPP / LARAGON</div>
          <h2 class="text-3xl font-extrabold text-slate-900">
            Struktur Folder Proyek <span class="font-mono text-blue-700">kothak-photo/</span>
          </h2>
          <p class="text-sm text-slate-600 mt-1">
            Dirancang modular dengan pola MVC ringan (Native PHP 8.x + PDO MySQL + Tailwind CSS) agar langsung berjalan di <code class="font-mono bg-white px-1.5 py-0.5 border border-slate-400">C:/laragon/www/kothak-photo/</code> atau <code class="font-mono bg-white px-1.5 py-0.5 border border-slate-400">C:/xampp/htdocs/kothak-photo/</code> tanpa konfigurasi rumit.
          </p>
        </div>
        <button type="button" onclick="switchWorkspaceTab('customer')" class="neo-btn bg-white text-slate-900 px-4 py-2.5 text-xs font-extrabold uppercase tracking-wider">
          ← Kembali ke Halaman Beranda Customer
        </button>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Directory Tree Column -->
        <div class="lg:col-span-5 neo-box bg-slate-900 text-slate-100 p-5 font-mono text-xs">
          <div class="flex items-center justify-between border-b border-slate-700 pb-3 mb-3">
            <span class="text-blue-400 font-bold uppercase">📂 C:/laragon/www/kothak-photo/</span>
            <button type="button" onclick="copyFolderTree()" class="bg-blue-700 hover:bg-blue-600 text-white px-2.5 py-1 text-[11px] font-sans font-bold uppercase">
              Salin Pohon Folder
            </button>
          </div>
          <pre id="folderTreePre" class="leading-relaxed overflow-x-auto text-[11px] text-slate-200 select-all">kothak-photo/
├── config/
│   ├── app.php                 # Konstanta base URL, timezone WIB, &amp; WA gateway
│   └── database.php            # Koneksi PDO MySQL (anti SQL-Injection)
├── database/
│   └── kothak_photo_schema.sql # Skema DDL 5 tabel + relasi FK + data awal
├── public/
│   ├── index.php               # Front-controller &amp; router utama
│   ├── assets/
│   │   ├── css/custom.css      # Neo-brutalist utility selaras kothak-cafe
│   │   ├── js/queue-poller.js  # AJAX polling Smart Queue real-time (3 dtk)
│   │   └── img/qris-kothak.png # Gambar barcode QRIS Statis resmi studio
│   └── uploads/
│       └── payment_proofs/     # Direktori penyimpanan bukti transfer QRIS
├── app/
│   ├── Middleware/
│   │   └── RoleMiddleware.php  # Proteksi akses: customer, cashier, admin
│   ├── Models/
│   │   ├── User.php            # Entitas users
│   │   ├── Package.php         # Entitas packages
│   │   ├── Schedule.php        # Entitas schedules &amp; cek kuota slot
│   │   ├── Booking.php         # Entitas bookings + transaksi DB lock
│   │   └── Payment.php         # Entitas payments &amp; status verifikasi
│   └── Controllers/
│       ├── AuthController.php
│       ├── CustomerBookingController.php
│       ├── CashierQueueController.php
│       └── AdminReportController.php
└── views/
    ├── layouts/
    │   ├── header.php          # Sticky header KOTHaK Cafe &amp; Photo
    │   └── footer.php          # Footer Royal Blue (#1D4ED8)
    ├── customer/
    │   ├── home_packages.php   # Halaman Beranda &amp; Pemilihan Paket Foto
    │   ├── checkout_qris.php   # Upload bukti bayar QRIS Statis
    │   └── smart_queue.php     # Pemantauan nomor antrean real-time
    ├── cashier/
    │   └── dashboard.php       # Verifikasi Accept/Reject, Check-in, Panggil
    └── admin/
        ├── packages_crud.php   # CRUD Paket Foto (harga, durasi, status)
        ├── schedules_crud.php  # CRUD Jadwal &amp; Kuota slot harian
        ├── users_crud.php      # Manajemen Pengguna
        └── reports.php         # Grafik jam sibuk, paket terlaris, pendapatan</pre>
        </div>

        <!-- Interactive File Code Inspector -->
        <div class="lg:col-span-7 neo-box bg-white p-5 flex flex-col">
          <div class="flex flex-wrap items-center justify-between border-b-2 border-slate-900 pb-3 mb-4 gap-2">
            <div class="flex flex-wrap gap-1.5">
              <button type="button" onclick="inspectProjectFile('config/database.php')" class="file-tab-btn bg-blue-700 text-white px-3 py-1.5 text-xs font-mono font-bold border border-slate-900">
                config/database.php
              </button>
              <button type="button" onclick="inspectProjectFile('app/Controllers/CustomerBookingController.php')" class="file-tab-btn bg-[#F5F4EE] text-slate-900 px-3 py-1.5 text-xs font-mono font-bold border border-slate-900">
                CustomerBookingController.php
              </button>
              <button type="button" onclick="inspectProjectFile('app/Controllers/CashierQueueController.php')" class="file-tab-btn bg-[#F5F4EE] text-slate-900 px-3 py-1.5 text-xs font-mono font-bold border border-slate-900">
                CashierQueueController.php
              </button>
            </div>
            <button type="button" onclick="copyInspectedFile()" class="neo-btn bg-[#F5F4EE] hover:bg-blue-50 text-slate-900 px-3 py-1 text-xs font-extrabold uppercase">
              Salin Kode File
            </button>
          </div>
          <div id="inspectedFileTitle" class="text-xs font-mono font-bold text-blue-700 mb-2">File: config/database.php</div>
          <pre id="inspectedFileCode" class="bg-slate-900 text-emerald-300 p-4 font-mono text-xs overflow-x-auto leading-relaxed flex-1"></pre>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================================================================== -->
  <!-- VIEW 3: SKEMA DATABASE SQL LENGKAP & ERD (`kothak_photo_schema.sql`)  -->
  <!-- ===================================================================== -->
  <section id="viewSql" class="hidden flex-1 py-12 bg-[#F5F4EE]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
        <div>
          <div class="text-xs font-extrabold uppercase tracking-[0.2em] text-blue-700 mb-1">TUGAS 1B &bull; RELATIONAL DATABASE SCHEMA (MYSQL / MARIADB)</div>
          <h2 class="text-3xl font-extrabold text-slate-900">
            Skema Database SQL Lengkap &amp; Relasi Entitas
          </h2>
          <p class="text-sm text-slate-600 mt-1">
            Mencakup 5 tabel inti (<code class="font-mono">users</code>, <code class="font-mono">packages</code>, <code class="font-mono">schedules</code>, <code class="font-mono">bookings</code>, <code class="font-mono">payments</code>) lengkap dengan <code class="font-mono">FOREIGN KEY</code>, presisi waktu <code class="font-mono">DATETIME(3)</code> untuk pencegahan <code class="font-mono">double-booking</code>, serta data awal.
          </p>
        </div>
        <div class="flex items-center gap-2">
          <button type="button" onclick="copySqlSchema()" class="neo-btn bg-blue-700 text-white px-4 py-2.5 text-xs font-extrabold uppercase tracking-wider">
            📋 Salin Skema SQL Lengkap
          </button>
          <button type="button" onclick="switchWorkspaceTab('customer')" class="neo-btn bg-white text-slate-900 px-4 py-2.5 text-xs font-extrabold uppercase tracking-wider">
            ← Kembali ke Beranda
          </button>
        </div>
      </div>

      <!-- Relational ERD Cards Summary -->
      <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-6 text-xs">
        <div class="neo-box bg-white p-3.5">
          <div class="font-mono font-extrabold text-blue-700 uppercase">1. users</div>
          <div class="text-[11px] text-slate-500 mt-0.5">PK: <code class="font-mono">id</code></div>
          <p class="text-[11px] text-slate-700 mt-1">Menyimpan akun <code class="font-mono">customer</code>, <code class="font-mono">cashier</code>, dan <code class="font-mono">admin</code> beserta nomor WhatsApp untuk pengiriman tiket.</p>
        </div>
        <div class="neo-box bg-white p-3.5">
          <div class="font-mono font-extrabold text-blue-700 uppercase">2. packages</div>
          <div class="text-[11px] text-slate-500 mt-0.5">PK: <code class="font-mono">id</code></div>
          <p class="text-[11px] text-slate-700 mt-1">Katalog paket foto studio, durasi sesi (menit), tarif harga (IDR), dan status aktif (<code class="font-mono">is_active</code>).</p>
        </div>
        <div class="neo-box bg-white p-3.5">
          <div class="font-mono font-extrabold text-blue-700 uppercase">3. schedules</div>
          <div class="text-[11px] text-slate-500 mt-0.5">PK: <code class="font-mono">id</code></div>
          <p class="text-[11px] text-slate-700 mt-1">Slot tanggal harian (<code class="font-mono">date</code>), jam mulai/selesai, dan kuota kapasitas (<code class="font-mono">capacity</code>).</p>
        </div>
        <div class="neo-box bg-white p-3.5">
          <div class="font-mono font-extrabold text-blue-700 uppercase">4. bookings</div>
          <div class="text-[11px] text-slate-500 mt-0.5">FK: <code class="font-mono">user_id, package_id, schedule_id</code></div>
          <p class="text-[11px] text-slate-700 mt-1">Transaksi reservasi dengan <code class="font-mono">booking_date DATETIME(3)</code>, <code class="font-mono">status</code>, dan <code class="font-mono">queue_number</code>.</p>
        </div>
        <div class="neo-box bg-white p-3.5">
          <div class="font-mono font-extrabold text-blue-700 uppercase">5. payments</div>
          <div class="text-[11px] text-slate-500 mt-0.5">FK: <code class="font-mono">booking_id</code> (1-to-1)</div>
          <p class="text-[11px] text-slate-700 mt-1">Unggahan bukti bayar QRIS Statis (<code class="font-mono">proof_image_path</code>) &amp; verifikasi kasir (<code class="font-mono">payment_status</code>).</p>
        </div>
      </div>

      <!-- Full SQL Code Block -->
      <div class="neo-box bg-slate-900 text-slate-100 p-5">
        <div class="flex items-center justify-between border-b border-slate-700 pb-3 mb-3 text-xs font-mono">
          <span class="text-emerald-400">database/kothak_photo_schema.sql (MySQL 8.0+ / MariaDB 10.6+ InnoDB)</span>
          <span class="text-slate-400">Siap di-import via phpMyAdmin / HeidiSQL</span>
        </div>
        <pre id="sqlSchemaPre" class="font-mono text-xs text-slate-200 overflow-x-auto leading-relaxed max-h-[540px] select-all"></pre>
      </div>
    </div>
  </section>

  <!-- ===================================================================== -->
  <!-- FOOTER (IDENTICAL VISUAL STRUCTURE TO KOTHAK-CAFE.FROSTYICE.ID)       -->
  <!-- ===================================================================== -->
  <footer class="bg-blue-700 text-white border-t-2 border-slate-900 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 pb-10 border-b border-blue-400/50">
        <!-- Col 1: Brand -->
        <div class="space-y-4">
          <div class="inline-flex items-center gap-3 bg-white text-blue-700 border-2 border-slate-900 px-3 py-2">
            <span class="font-black text-base tracking-tight">KOTHaK</span>
            <span class="text-xs font-bold uppercase tracking-widest text-slate-900">Cafe &amp; Photo</span>
          </div>
          <p class="text-xs text-blue-100 leading-relaxed">
            Where every cup tells a story and every photo captures a memory. Kafe dan studio photobox terintegrasi pertama di Kabupaten Purbalingga.
          </p>
          <div class="flex items-center gap-2 text-xs font-bold">
            <a href="https://kothak-cafe.frostyice.id/" target="_blank" rel="noopener noreferrer" class="border border-white px-2.5 py-1 hover:bg-white hover:text-blue-700 transition-colors">@kothakcafe</a>
            <a href="https://kothak-cafe.frostyice.id/" target="_blank" rel="noopener noreferrer" class="border border-white px-2.5 py-1 hover:bg-white hover:text-blue-700 transition-colors">@kothakphoto</a>
            <a href="https://kothak-cafe.frostyice.id/" target="_blank" rel="noopener noreferrer" class="border border-white px-2.5 py-1 hover:bg-white hover:text-blue-700 transition-colors">TikTok</a>
          </div>
        </div>

        <!-- Col 2: + Quick Links -->
        <div>
          <h3 class="text-xs font-extrabold uppercase tracking-widest text-blue-200 mb-3">+ Quick Links</h3>
          <ul class="space-y-2 text-xs font-bold uppercase tracking-wider">
            <li><a href="#heroSection" onclick="switchWorkspaceTab('customer')" class="hover:underline">HOME</a></li>
            <li><a href="#packagesSection" onclick="switchWorkspaceTab('customer')" class="hover:underline">PHOTO PACKAGES</a></li>
            <li><a href="#bookingSection" onclick="switchWorkspaceTab('customer')" class="hover:underline">BOOKING &amp; QRIS</a></li>
            <li><a href="#queueSection" onclick="switchWorkspaceTab('customer')" class="hover:underline">SMART QUEUE</a></li>
            <li><a href="https://kothak-cafe.frostyice.id/" target="_blank" rel="noopener noreferrer" class="hover:underline">MAIN CAFE WEBSITE ↗</a></li>
          </ul>
        </div>

        <!-- Col 3: + Connect -->
        <div>
          <h3 class="text-xs font-extrabold uppercase tracking-widest text-blue-200 mb-3">+ Connect</h3>
          <p class="text-xs text-blue-100 leading-relaxed">
            Jl. AW Sumarmo No.39,<br />
            Kembaran Kulon, Purbalingga,<br />
            Jawa Tengah 53319
          </p>
          <p class="text-xs font-mono font-bold text-white mt-3">
            WA / Telp: 0813-2674-377
          </p>
        </div>

        <!-- Col 4: + Hours -->
        <div>
          <h3 class="text-xs font-extrabold uppercase tracking-widest text-blue-200 mb-3">+ Hours</h3>
          <div class="border border-blue-400 bg-blue-800/60 p-3.5 text-xs space-y-1">
            <div class="font-extrabold uppercase tracking-wider text-white">Everyday</div>
            <div class="font-mono text-base font-bold text-blue-200">09:00 - 23:00</div>
            <div class="text-[11px] text-blue-200 pt-1">* Buka setiap hari untuk kopi dan momen foto Anda.</div>
          </div>
        </div>
      </div>

      <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-blue-200 gap-2">
        <div>&copy; 2026 Kothak Kafe &amp; Photo Studio. All rights reserved.</div>
        <div>Where every cup tells a story and every photo captures a memory.</div>
      </div>
    </div>
  </footer>

  <!-- FLOATING WHATSAPP / TICKET STATUS PILL (ROUNDED-FULL AS IN REFERENCE) -->
  <button
    type="button"
    onclick="openTicketModal()"
    class="fixed bottom-5 right-5 z-40 bg-emerald-500 hover:bg-emerald-400 text-slate-900 border-2 border-slate-900 rounded-full px-5 py-3 shadow-[4px_4px_0px_#0F172A] flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider"
  >
    <span class="w-2.5 h-2.5 rounded-full bg-slate-900 animate-ping"></span>
    <span>Tiket WA &amp; Antrean Saya</span>
  </button>

  <!-- MODAL: WHATSAPP TICKET & BOOKING RECEIPT PREVIEW -->
  <div id="ticketModal" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="neo-box bg-[#F5F4EE] max-w-md w-full p-6 relative">
      <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-4">
        <div>
          <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-700">WHATSAPP NOTIFICATION SIMULATOR</span>
          <h3 class="text-lg font-extrabold text-slate-900">Tiket Antrean Kothak Photo</h3>
        </div>
        <button type="button" onclick="closeTicketModal()" class="border-2 border-slate-900 bg-white px-2.5 py-1 text-xs font-extrabold">✕</button>
      </div>

      <div id="ticketModalContent" class="bg-white border-2 border-slate-900 p-4 text-xs space-y-2.5 font-mono">
        <!-- Populated dynamically -->
      </div>

      <div class="mt-4 flex justify-end gap-2">
        <button type="button" onclick="closeTicketModal()" class="neo-btn bg-blue-700 text-white px-4 py-2 text-xs font-extrabold uppercase">
          Tutup Preview
        </button>
      </div>
    </div>
  </div>

  <!-- MODAL: LOGIN / SWITCH ROLE SIMULATION -->
  <div id="authModal" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="neo-box bg-[#F5F4EE] max-w-md w-full p-6">
      <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-4">
        <div>
          <span class="text-[10px] font-extrabold uppercase tracking-widest text-blue-700">SIMULASI HAK AKSES (`users.role`)</span>
          <h3 class="text-lg font-extrabold text-slate-900">Pilih Akun Pengguna Aktif</h3>
        </div>
        <button type="button" onclick="closeAuthModal()" class="border-2 border-slate-900 bg-white px-2.5 py-1 text-xs font-extrabold">✕</button>
      </div>
      <p class="text-xs text-slate-600 mb-4">
        Klik salah satu akun dari tabel <code class="font-mono">users</code> di bawah untuk menguji sesi Pelanggan, Kasir, atau Admin:
      </p>
      <div class="space-y-2.5 text-xs">
        <button type="button" onclick="selectSimulatedUser('customer')" class="w-full text-left border-2 border-slate-900 bg-white hover:bg-blue-50 p-3 flex items-center justify-between">
          <div>
            <div class="font-extrabold text-slate-900">Hana Naila Rahmadina</div>
            <div class="font-mono text-[11px] text-slate-500">hananaeylaa@gmail.com &bull; role: customer</div>
          </div>
          <span class="bg-blue-700 text-white px-2 py-0.5 text-[10px] font-bold uppercase">Pilih</span>
        </button>
        <button type="button" onclick="selectSimulatedUser('cashier')" class="w-full text-left border-2 border-slate-900 bg-white hover:bg-blue-50 p-3 flex items-center justify-between">
          <div>
            <div class="font-extrabold text-slate-900">Raka Pratama (Kasir Studio)</div>
            <div class="font-mono text-[11px] text-slate-500">cashier@kothakphoto.id &bull; role: cashier</div>
          </div>
          <span class="bg-emerald-600 text-white px-2 py-0.5 text-[10px] font-bold uppercase">Pilih</span>
        </button>
        <button type="button" onclick="selectSimulatedUser('admin')" class="w-full text-left border-2 border-slate-900 bg-white hover:bg-blue-50 p-3 flex items-center justify-between">
          <div>
            <div class="font-extrabold text-slate-900">Admin Kothak Studio</div>
            <div class="font-mono text-[11px] text-slate-500">admin@kothakphoto.id &bull; role: admin</div>
          </div>
          <span class="bg-slate-900 text-white px-2 py-0.5 text-[10px] font-bold uppercase">Pilih</span>
        </button>
      </div>
    </div>
  </div>

  <!-- TOAST NOTIFICATION -->
  <div id="toastBanner" class="hidden fixed top-24 right-5 z-50 neo-box bg-slate-900 text-white px-4 py-3 text-xs font-bold max-w-sm">
    <span id="toastMessage">Berhasil!</span>
  </div>

  <!-- ===================================================================== -->
  <!-- APPLICATION STATE & BUSINESS LOGIC SCRIPT                             -->
  <!-- ===================================================================== -->
  <script>
    // 1. Relational Database Entities (`packages`, `schedules`, `bookings`, `payments`)
    const PACKAGES_DATA = [
      {
        id: 1,
        name: 'Kothak Solo / Duo Express',
        category: 'SOLO_DUO',
        backdrop: 'Electric Blue Backdrop',
        duration: 10,
        price: 25000,
        capacityText: '1–2 Orang',
        badge: 'POPULAR DUO',
        is_active: 1,
        description: 'Sesi photobox cepat 10 menit dengan background biru ikonik Kothak, 2 strip cetak 2R, dan seluruh soft file HD via Google Drive/QR.'
      },
      {
        id: 2,
        name: 'Kothak Signature Box (2–4 Orang)',
        category: 'GROUP',
        backdrop: 'Electric Blue Backdrop',
        duration: 15,
        price: 35000,
        capacityText: '2–4 Orang',
        badge: 'BEST SELLER',
        is_active: 1,
        description: 'Paket favorit pengunjung Kothak Purbalingga: sesi 15 menit, bebas pilih properti estetik, 2 cetak 4R, soft file HD + Boomerang GIF.'
      },
      {
        id: 3,
        name: 'Warm Minimalist Portrait Session',
        category: 'SOLO_DUO',
        backdrop: 'Warm Cream Minimalist',
        duration: 15,
        price: 35000,
        capacityText: '1–3 Orang',
        badge: 'AESTHETIC CREAM',
        is_active: 1,
        description: 'Sesi 15 menit di studio Background 2 bernuansa Warm Cream Minimalist yang hangat dan bersih, cocok untuk foto profil & pasangan.'
      },
      {
        id: 4,
        name: 'Bestie Squad Frame (4–6 Orang)',
        category: 'GROUP',
        backdrop: 'Warm Cream Minimalist',
        duration: 20,
        price: 50000,
        capacityText: '4–6 Orang',
        badge: 'SQUAD CHOICE',
        is_active: 1,
        description: 'Sesi lega 20 menit untuk geng kampus atau keluarga. Termasuk 4 lembar cetak strip 4R, properti lengkap, dan semua soft file original.'
      },
      {
        id: 5,
        name: 'Kopi & Frame Couple Bundle',
        category: 'BUNDLING',
        backdrop: 'Electric Blue Backdrop',
        duration: 15,
        price: 65000,
        capacityText: '2 Orang + 2 Kopi',
        badge: 'COFFEE + PHOTO',
        is_active: 1,
        description: 'Bundling hemat 1 sesi Kothak Signature Photobox (15 menit, cetak 2 strip + soft file) ditambah 2 Iced Signature Coffee dari Kothak Cafe.'
      },
      {
        id: 6,
        name: 'Kothak Hangout Party Package',
        category: 'BUNDLING',
        backdrop: 'Warm Cream Minimalist',
        duration: 25,
        price: 95000,
        capacityText: '4–6 Orang + Snack & Minuman',
        badge: 'COMPLETE BUNDLE',
        is_active: 1,
        description: 'Sesi photobox 25 menit (4 cetak strip + soft file) lengkap dengan paket camilan sharing platter & 4 minuman segar Kothak Cafe.'
      }
    ];

    const SCHEDULES_DATA = [
      { id: 101, start_time: '13:00', end_time: '13:30', capacity: 3, booked: 3 },
      { id: 102, start_time: '14:00', end_time: '14:30', capacity: 3, booked: 2 },
      { id: 103, start_time: '15:00', end_time: '15:30', capacity: 3, booked: 1 },
      { id: 104, start_time: '16:00', end_time: '16:30', capacity: 3, booked: 0 },
      { id: 105, start_time: '17:00', end_time: '17:30', capacity: 3, booked: 2 },
      { id: 106, start_time: '19:00', end_time: '19:30', capacity: 3, booked: 1 },
      { id: 107, start_time: '20:00', end_time: '20:30', capacity: 3, booked: 0 },
      { id: 108, start_time: '21:00', end_time: '21:30', capacity: 3, booked: 1 }
    ];

    let bookingsState = [
      {
        id: 501,
        queue_number: 'KTH-012',
        customer_name: 'Jeni Rahmawati',
        wa_number: '0813-8899-1122',
        package_name: 'Kothak Signature Box (2–4 Orang)',
        backdrop: 'Background 1: Electric Blue',
        slot_label: '13 Nov 2026 • 14:00 - 14:30',
        proof_image_path: 'uploads/payment_proofs/qris_jeni_35k.jpg',
        payment_status: 'verified',
        status: 'in_studio'
      },
      {
        id: 502,
        queue_number: 'KTH-013',
        customer_name: 'Zainab Feizia',
        wa_number: '0857-2345-6789',
        package_name: 'Bestie Squad Frame (4–6 Orang)',
        backdrop: 'Background 2: Warm Cream',
        slot_label: '13 Nov 2026 • 14:00 - 14:30',
        proof_image_path: 'uploads/payment_proofs/qris_zainab_50k.png',
        payment_status: 'verified',
        status: 'checked_in'
      },
      {
        id: 503,
        queue_number: 'KTH-014',
        customer_name: 'Salsabila Firzah',
        wa_number: '0812-9988-7766',
        package_name: 'Kopi & Frame Couple Bundle',
        backdrop: 'Background 1: Electric Blue',
        slot_label: '13 Nov 2026 • 15:00 - 15:30',
        proof_image_path: 'uploads/payment_proofs/qris_salsa_65k.jpg',
        payment_status: 'pending',
        status: 'waiting_verification'
      }
    ];

    let activeCategory = 'ALL';
    let selectedPackageId = 2;
    let selectedScheduleId = 103;
    let uploadedProofName = '';
    let latestTicketBooking = bookingsState[0];

    // SQL Schema String (`database/kothak_photo_schema.sql`)
    const SQL_SCHEMA_TEXT = `-- ============================================================================
-- KOTHAK PHOTO SMART QUEUE & BOOKING SYSTEM — MYSQL RELATIONAL SCHEMA
-- Environment : XAMPP / Laragon (MySQL 8.0+ / MariaDB 10.6+ InnoDB)
-- Referensi UI: https://kothak-cafe.frostyice.id/
-- ============================================================================

CREATE DATABASE IF NOT EXISTS kothak_photo_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE kothak_photo_db;

-- 1. Tabel users (Pelanggan, Kasir, Admin)
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone_whatsapp VARCHAR(25) DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('customer', 'cashier', 'admin') NOT NULL DEFAULT 'customer',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_role (role)
) ENGINE=InnoDB;

-- 2. Tabel packages (Manajemen Paket Foto Studio)
CREATE TABLE packages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  description TEXT NOT NULL,
  duration INT UNSIGNED NOT NULL COMMENT 'Durasi sesi foto dalam satuan menit',
  price DECIMAL(10, 2) NOT NULL COMMENT 'Harga paket dalam Rupiah (IDR)',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_packages_active (is_active)
) ENGINE=InnoDB;

-- 3. Tabel schedules (Jadwal & Kuota Slot Harian Studio)
CREATE TABLE schedules (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  capacity INT UNSIGNED NOT NULL DEFAULT 3 COMMENT 'Kuota maksimal reservasi pada slot waktu ini',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_schedule_slot (date, start_time, end_time),
  INDEX idx_schedule_date (date)
) ENGINE=InnoDB;

-- 4. Tabel bookings (Transaksi Reservasi & Nomor Antrean Pintar)
-- Menggunakan DATETIME(3) (presisi milidetik) untuk mencegah race-condition saat klik bersamaan
CREATE TABLE bookings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  package_id INT UNSIGNED NOT NULL,
  schedule_id INT UNSIGNED NOT NULL,
  booking_date DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  status ENUM(
    'waiting_verification',
    'confirmed',
    'checked_in',
    'in_studio',
    'completed',
    'cancelled'
  ) NOT NULL DEFAULT 'waiting_verification',
  queue_number VARCHAR(20) NOT NULL,
  studio_backdrop VARCHAR(80) DEFAULT 'Electric Blue Backdrop',
  whatsapp_sent_at DATETIME DEFAULT NULL,
  checked_in_at DATETIME DEFAULT NULL,
  UNIQUE KEY uq_date_queue_number (schedule_id, queue_number),
  INDEX idx_bookings_status (status),
  CONSTRAINT fk_bookings_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_bookings_package
    FOREIGN KEY (package_id) REFERENCES packages(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_bookings_schedule
    FOREIGN KEY (schedule_id) REFERENCES schedules(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 5. Tabel payments (Bukti Pembayaran QRIS Statis & Verifikasi Kasir)
CREATE TABLE payments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id INT UNSIGNED NOT NULL UNIQUE,
  proof_image_path VARCHAR(255) NOT NULL COMMENT 'Path file bukti transfer QRIS di public/uploads/payment_proofs/',
  payment_status ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
  verified_by INT UNSIGNED DEFAULT NULL COMMENT 'ID user kasir yang memverifikasi',
  verified_at DATETIME DEFAULT NULL,
  rejection_note VARCHAR(255) DEFAULT NULL,
  created_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  INDEX idx_payments_status (payment_status),
  CONSTRAINT fk_payments_booking
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_payments_verifier
    FOREIGN KEY (verified_by) REFERENCES users(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================================
-- DATA AWAL (SEEDER) UNTUK PENGUJIAN LOKAL DI XAMPP / LARAGON
-- ============================================================================
INSERT INTO users (name, email, phone_whatsapp, password, role) VALUES
('Hana Naila Rahmadina', 'hananaeylaa@gmail.com', '081234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
('Raka Pratama (Kasir)', 'cashier@kothakphoto.id', '08132674377', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cashier'),
('Admin Kothak Studio', 'admin@kothakphoto.id', '08132674377', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO packages (name, description, duration, price, is_active) VALUES
('Kothak Solo / Duo Express', 'Sesi photobox 10 menit, 2 strip cetak 2R, soft file HD via QR.', 10, 25000.00, 1),
('Kothak Signature Box (2-4 Orang)', 'Sesi photobox 15 menit, 2 cetak 4R, soft file HD + GIF.', 15, 35000.00, 1),
('Warm Minimalist Portrait Session', 'Sesi 15 menit di Background Warm Cream Minimalist + cetak 4R.', 15, 35000.00, 1),
('Bestie Squad Frame (4-6 Orang)', 'Sesi 20 menit untuk 4-6 orang, 4 cetak strip 4R + semua soft file.', 20, 50000.00, 1),
('Kopi & Frame Couple Bundle', 'Sesi Signature Photobox 15 menit + 2 Iced Signature Coffee Kothak.', 15, 65000.00, 1);

INSERT INTO schedules (date, start_time, end_time, capacity) VALUES
('2026-11-13', '14:00:00', '14:30:00', 3),
('2026-11-13', '15:00:00', '15:30:00', 3),
('2026-11-13', '16:00:00', '16:30:00', 3),
('2026-11-13', '17:00:00', '17:30:00', 3);`;

    // Sample Project Files for Inspector
    const PROJECT_FILES_CODE = {
      'config/database.php': `<?php
// File: config/database.php
// Koneksi PDO MySQL untuk XAMPP / Laragon (Support Transaksi & Millisecond Lock)

declare(strict_types=1);

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host = '127.0.0.1';
            $db   = 'kothak_photo_db';
            $user = 'root';
            $pass = ''; // Default XAMPP / Laragon kosong
            $dsn  = "mysql:host={$host};dbname={$db};charset=utf8mb4";

            self::$instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$instance;
    }
}`,
      'app/Controllers/CustomerBookingController.php': `<?php
// File: app/Controllers/CustomerBookingController.php
// Menangani proses Pilih Paket -> Pilih Jadwal -> Upload Bukti QRIS Statis (Anti Double-Booking)

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

class CustomerBookingController {
    public function storeBookingWithPayment(int $userId, int $packageId, int $scheduleId, array $proofFile): array {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            // 1. Kunci baris jadwal (FOR UPDATE) agar klik bersamaan di milidetik yang sama tidak bentrok
            $stmtSlot = $pdo->prepare("SELECT capacity FROM schedules WHERE id = :id FOR UPDATE");
            $stmtSlot->execute(['id' => $scheduleId]);
            $schedule = $stmtSlot->fetch();

            $stmtCount = $pdo->prepare("
                SELECT COUNT(*) AS total_booked
                FROM bookings
                WHERE schedule_id = :schedule_id AND status != 'cancelled'
            ");
            $stmtCount->execute(['schedule_id' => $scheduleId]);
            $booked = (int) $stmtCount->fetchColumn();

            if ($booked >= (int) $schedule['capacity']) {
                throw new RuntimeException('Kuota slot jadwal ini baru saja penuh. Silakan pilih slot waktu lain.');
            }

            // 2. Generate nomor antrean otomatis (Format: KTH-001)
            $queueNumber = sprintf('KTH-%03d', $booked + 12);

            // 3. Simpan ke tabel bookings dengan presisi waktu milidetik
            $stmtBooking = $pdo->prepare("
                INSERT INTO bookings (user_id, package_id, schedule_id, booking_date, status, queue_number)
                VALUES (:uid, :pid, :sid, NOW(3), 'waiting_verification', :qnum)
            ");
            $stmtBooking->execute([
                'uid'  => $userId,
                'pid'  => $packageId,
                'sid'  => $scheduleId,
                'qnum' => $queueNumber,
            ]);
            $bookingId = (int) $pdo->lastInsertId();

            // 4. Unggah bukti pembayaran QRIS Statis ke public/uploads/payment_proofs/
            $ext = pathinfo($proofFile['name'], PATHINFO_EXTENSION) ?: 'jpg';
            $fileName = 'qris_' . $bookingId . '_' . time() . '.' . $ext;
            $relativePath = 'uploads/payment_proofs/' . $fileName;
            move_uploaded_file($proofFile['tmp_name'], __DIR__ . '/../../public/' . $relativePath);

            $stmtPayment = $pdo->prepare("
                INSERT INTO payments (booking_id, proof_image_path, payment_status, created_at)
                VALUES (:bid, :path, 'pending', NOW(3))
            ");
            $stmtPayment->execute(['bid' => $bookingId, 'path' => $relativePath]);

            $pdo->commit();
            return ['success' => true, 'queue_number' => $queueNumber, 'booking_id' => $bookingId];
        } catch (Throwable $e) {
            $pdo->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}`,
      'app/Controllers/CashierQueueController.php': `<?php
// File: app/Controllers/CashierQueueController.php
// Verifikasi Bukti Transfer QRIS (Accept/Reject), Kirim Tiket WA, Check-In, & Panggil Antrean

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

class CashierQueueController {
    public function verifyPayment(int $bookingId, int $cashierId, string $decision): bool {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $paymentStatus = ($decision === 'accept') ? 'verified' : 'rejected';
            $bookingStatus = ($decision === 'accept') ? 'confirmed' : 'cancelled';

            $stmtPay = $pdo->prepare("
                UPDATE payments
                SET payment_status = :pstatus, verified_by = :cid, verified_at = NOW()
                WHERE booking_id = :bid
            ");
            $stmtPay->execute(['pstatus' => $paymentStatus, 'cid' => $cashierId, 'bid' => $bookingId]);

            $stmtBook = $pdo->prepare("
                UPDATE bookings
                SET status = :bstatus, whatsapp_sent_at = IF(:bstatus2 = 'confirmed', NOW(), NULL)
                WHERE id = :bid
            ");
            $stmtBook->execute(['bstatus' => $bookingStatus, 'bstatus2' => $bookingStatus, 'bid' => $bookingId]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            return false;
        }
    }
}`
    };

    function formatRupiah(num) {
      return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    function showToast(msg) {
      const banner = document.getElementById('toastBanner');
      const text = document.getElementById('toastMessage');
      text.textContent = msg;
      banner.classList.remove('hidden');
      clearTimeout(window._toastTimer);
      window._toastTimer = setTimeout(() => {
        banner.classList.add('hidden');
      }, 3600);
    }

    function switchWorkspaceTab(tab) {
      const views = {
        customer: document.getElementById('viewCustomer'),
        folder: document.getElementById('viewFolder'),
        sql: document.getElementById('viewSql')
      };
      const btns = {
        customer: document.getElementById('tabBtnCustomer'),
        folder: document.getElementById('tabBtnFolder'),
        sql: document.getElementById('tabBtnSql')
      };

      Object.keys(views).forEach((key) => {
        if (key === tab) {
          views[key].classList.remove('hidden');
          btns[key].className = 'px-2.5 py-1 font-bold uppercase tracking-wider bg-blue-600 text-white border border-blue-400';
        } else {
          views[key].classList.add('hidden');
          btns[key].className = 'px-2.5 py-1 font-bold uppercase tracking-wider bg-slate-800 text-slate-300 hover:text-white border border-slate-700';
        }
      });
    }

    function toggleMobileMenu() {
      const panel = document.getElementById('mobileMenuPanel');
      panel.classList.toggle('hidden');
    }

    function setCategoryFilter(cat) {
      activeCategory = cat;
      document.querySelectorAll('.cat-filter-btn').forEach((btn) => {
        if (btn.getAttribute('data-cat') === cat) {
          btn.className = 'cat-filter-btn bg-blue-700 text-white border-2 border-slate-900 px-4 py-2 text-xs font-extrabold uppercase tracking-wider';
        } else {
          btn.className = 'cat-filter-btn bg-white text-slate-900 hover:bg-blue-50 border-2 border-slate-900 px-4 py-2 text-xs font-extrabold uppercase tracking-wider';
        }
      });
      renderPackages();
    }

    function renderPackages() {
      const searchVal = (document.getElementById('packageSearchInput').value || '').toLowerCase();
      const backdropVal = document.getElementById('backdropFilterSelect').value;
      const grid = document.getElementById('packagesGrid');

      const filtered = PACKAGES_DATA.filter((pkg) => {
        const matchCat = activeCategory === 'ALL' || pkg.category === activeCategory;
        const matchBackdrop = backdropVal === 'ALL' || pkg.backdrop === backdropVal;
        const matchSearch =
          pkg.name.toLowerCase().includes(searchVal) ||
          pkg.description.toLowerCase().includes(searchVal) ||
          pkg.capacityText.toLowerCase().includes(searchVal);
        return matchCat && matchBackdrop && matchSearch;
      });

      if (filtered.length === 0) {
        grid.innerHTML = `
          <div class="col-span-full neo-box bg-white p-8 text-center">
            <div class="text-sm font-extrabold text-slate-900 uppercase">Paket Tidak Ditemukan</div>
            <p class="text-xs text-slate-600 mt-1">Coba atur ulang kata kunci pencarian atau pilih kategori "ALL PACKAGES".</p>
          </div>
        `;
        return;
      }

      grid.innerHTML = filtered
        .map((pkg) => {
          const isSelected = pkg.id === selectedPackageId;
          const isBlueBg = pkg.backdrop.includes('Electric Blue');
          return `
            <article class="neo-box bg-white flex flex-col justify-between transition-transform hover:-translate-y-1 ${isSelected ? 'ring-4 ring-blue-700' : ''}">
              <div>
                <!-- Card Top Visual Header -->
                <div class="${isBlueBg ? 'bg-blue-700 text-white' : 'bg-[#EAE6DF] text-slate-900'} border-b-2 border-slate-900 p-4 flex items-center justify-between">
                  <span class="bg-slate-900 text-white text-[10px] font-extrabold uppercase tracking-widest px-2.5 py-1 border border-white">
                    ${pkg.badge}
                  </span>
                  <span class="font-mono text-xs font-extrabold px-2 py-0.5 border ${isBlueBg ? 'border-white bg-blue-800 text-white' : 'border-slate-900 bg-white text-slate-900'}">
                    ⏱ ${pkg.duration} MENIT
                  </span>
                </div>

                <!-- Card Content -->
                <div class="p-5">
                  <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                    <span>${pkg.backdrop}</span>
                    <span>👥 ${pkg.capacityText}</span>
                  </div>
                  <h3 class="text-lg font-extrabold text-slate-900 leading-snug">${pkg.name}</h3>
                  <p class="text-xs text-slate-600 mt-2 leading-relaxed">${pkg.description}</p>

                  <div class="mt-4 pt-3 border-t border-slate-200 flex flex-wrap gap-1.5 text-[10px] font-bold uppercase text-slate-700">
                    <span class="bg-[#F5F4EE] border border-slate-900 px-2 py-0.5">✓ Instant Print</span>
                    <span class="bg-[#F5F4EE] border border-slate-900 px-2 py-0.5">✓ All Soft Files HD</span>
                    <span class="bg-[#F5F4EE] border border-slate-900 px-2 py-0.5">✓ Free Props</span>
                  </div>
                </div>
              </div>

              <!-- Card Footer Price & Action -->
              <div class="p-5 pt-0 flex items-center justify-between gap-3">
                <div>
                  <div class="text-[10px] font-bold uppercase text-slate-500">HARGA / SESI</div>
                  <div class="text-xl font-black font-mono text-blue-700">${formatRupiah(pkg.price)}</div>
                </div>
                <button
                  type="button"
                  onclick="selectPackageForBooking(${pkg.id})"
                  class="neo-btn ${isSelected ? 'bg-slate-900 text-white' : 'bg-blue-700 hover:bg-blue-800 text-white'} px-4 py-2.5 text-xs font-extrabold uppercase tracking-wider"
                >
                  ${isSelected ? '✓ PAKET TERPILIH' : 'PILIH PAKET INI ➔'}
                </button>
              </div>
            </article>
          `;
        })
        .join('');
    }

    function selectPackageForBooking(pkgId) {
      selectedPackageId = pkgId;
      const pkg = PACKAGES_DATA.find((p) => p.id === pkgId) || PACKAGES_DATA[0];

      document.getElementById('selectedPkgBadge').textContent = pkg.badge;
      document.getElementById('selectedPkgDuration').textContent = pkg.duration + ' Menit / Sesi (' + pkg.capacityText + ')';
      document.getElementById('selectedPkgName').textContent = pkg.name;
      document.getElementById('selectedPkgDesc').textContent = pkg.description;
      document.getElementById('selectedPkgPrice').textContent = formatRupiah(pkg.price);

      const backdropSelect = document.getElementById('studioBackdropSelect');
      if (pkg.backdrop.includes('Electric Blue')) {
        backdropSelect.value = 'Background 1: Electric Blue (Signature)';
      } else {
        backdropSelect.value = 'Background 2: Warm Cream Minimalist';
      }

      renderPackages();
      updateCheckoutSummary();
      document.getElementById('bookingSection').scrollIntoView({ behavior: 'smooth' });
      showToast('Paket "' + pkg.name + '" dipilih. Silakan pilih slot waktu.');
    }

    function renderScheduleSlots() {
      const dateVal = document.getElementById('bookingDateInput').value || '2026-11-13';
      document.getElementById('slotDateLabel').textContent = dateVal;
      const grid = document.getElementById('scheduleSlotsGrid');

      grid.innerHTML = SCHEDULES_DATA.map((slot) => {
        const remaining = slot.capacity - slot.booked;
        const isFull = remaining <= 0;
        const isSelected = slot.id === selectedScheduleId && !isFull;

        if (isFull) {
          return `
            <button type="button" disabled class="border-2 border-slate-300 bg-slate-100 text-slate-400 p-3 text-left cursor-not-allowed">
              <div class="font-mono text-xs font-bold line-through">${slot.start_time} - ${slot.end_time}</div>
              <div class="text-[10px] font-bold uppercase mt-1 text-rose-500">PENUH (0/${slot.capacity} Slot)</div>
            </button>
          `;
        }

        return `
          <button
            type="button"
            onclick="selectScheduleSlot(${slot.id})"
            class="border-2 border-slate-900 p-3 text-left transition-transform ${
              isSelected
                ? 'bg-blue-700 text-white shadow-[3px_3px_0px_#0F172A]'
                : 'bg-white hover:bg-blue-50 text-slate-900'
            }"
          >
            <div class="font-mono text-xs font-extrabold">${slot.start_time} - ${slot.end_time} WIB</div>
            <div class="text-[10px] font-bold uppercase mt-1 ${isSelected ? 'text-blue-200' : 'text-emerald-700'}">
              Tersedia: ${remaining}/${slot.capacity} Kuota
            </div>
          </button>
        `;
      }).join('');

      updateCheckoutSummary();
    }

    function selectScheduleSlot(slotId) {
      selectedScheduleId = slotId;
      renderScheduleSlots();
    }

    function updateCheckoutSummary() {
      const pkg = PACKAGES_DATA.find((p) => p.id === selectedPackageId) || PACKAGES_DATA[0];
      const slot = SCHEDULES_DATA.find((s) => s.id === selectedScheduleId) || SCHEDULES_DATA[2];
      const dateVal = document.getElementById('bookingDateInput').value || '2026-11-13';
      const backdropVal = document.getElementById('studioBackdropSelect').value;

      document.getElementById('summaryPkgName').textContent = pkg.name;
      document.getElementById('summarySlotTime').textContent = dateVal + ' • ' + slot.start_time + ' - ' + slot.end_time;
      document.getElementById('summaryBackdrop').textContent = backdropVal;
      document.getElementById('summaryTotalPrice').textContent = formatRupiah(pkg.price);
    }

    function handleProofFileChange(event) {
      const file = event.target.files && event.target.files[0];
      if (!file) return;
      uploadedProofName = 'uploads/payment_proofs/' + file.name;
      document.getElementById('proofFileName').textContent = file.name;
      document.getElementById('proofPreviewBox').classList.remove('hidden');
      showToast('File bukti transfer "' + file.name + '" siap diunggah.');
    }

    function simulateSampleProof() {
      const pkg = PACKAGES_DATA.find((p) => p.id === selectedPackageId) || PACKAGES_DATA[0];
      const sampleName = 'bukti_qris_kothak_' + pkg.price + '.jpg';
      uploadedProofName = 'uploads/payment_proofs/' + sampleName;
      document.getElementById('proofFileName').textContent = sampleName;
      document.getElementById('proofPreviewBox').classList.remove('hidden');
      showToast('Contoh bukti transfer QRIS berhasil dilampirkan.');
    }

    function clearProofFile() {
      uploadedProofName = '';
      document.getElementById('proofFileInput').value = '';
      document.getElementById('proofPreviewBox').classList.add('hidden');
    }

    function submitBookingCheckout() {
      if (!uploadedProofName) {
        simulateSampleProof();
      }
      const pkg = PACKAGES_DATA.find((p) => p.id === selectedPackageId) || PACKAGES_DATA[0];
      const slot = SCHEDULES_DATA.find((s) => s.id === selectedScheduleId) || SCHEDULES_DATA[2];
      const dateVal = document.getElementById('bookingDateInput').value || '2026-11-13';
      const waNumber = document.getElementById('customerWaInput').value || '0812-3456-7890';
      const customerName = document.getElementById('summaryCustomerName').textContent;
      const backdropVal = document.getElementById('studioBackdropSelect').value;

      if (slot.booked >= slot.capacity) {
        showToast('Maaf, slot waktu ini sudah penuh. Silakan pilih jam lain.');
        return;
      }

      slot.booked += 1;
      const nextNum = 12 + bookingsState.length;
      const newQueueCode = 'KTH-0' + nextNum;

      const newBooking = {
        id: 500 + bookingsState.length + 1,
        queue_number: newQueueCode,
        customer_name: customerName,
        wa_number: waNumber,
        package_name: pkg.name,
        backdrop: backdropVal,
        slot_label: dateVal + ' • ' + slot.start_time + ' - ' + slot.end_time,
        proof_image_path: uploadedProofName,
        payment_status: 'pending',
        status: 'waiting_verification'
      };

      bookingsState.push(newBooking);
      latestTicketBooking = newBooking;
      clearProofFile();
      renderScheduleSlots();
      renderSmartQueueTable();
      openTicketModal(newBooking.id);
      showToast('Reservasi ' + newQueueCode + ' berhasil dibuat! Menunggu verifikasi Kasir.');
    }

    function renderSmartQueueTable() {
      const tbody = document.getElementById('smartQueueTableBody');
      tbody.innerHTML = bookingsState
        .map((b) => {
          let payBadge = '';
          if (b.payment_status === 'verified') {
            payBadge = '<span class="bg-emerald-100 text-emerald-800 border border-emerald-700 px-2 py-0.5 font-mono text-[10px] font-bold uppercase">VERIFIED</span>';
          } else if (b.payment_status === 'pending') {
            payBadge = '<span class="bg-amber-100 text-amber-800 border border-amber-700 px-2 py-0.5 font-mono text-[10px] font-bold uppercase">PENDING REVIEW</span>';
          } else {
            payBadge = '<span class="bg-rose-100 text-rose-800 border border-rose-700 px-2 py-0.5 font-mono text-[10px] font-bold uppercase">REJECTED</span>';
          }

          let statusBadge = '';
          if (b.status === 'in_studio') {
            statusBadge = '<span class="bg-blue-700 text-white px-2.5 py-1 font-extrabold text-[10px] uppercase tracking-wider">📸 SEDANG FOTO DI STUDIO</span>';
          } else if (b.status === 'checked_in') {
            statusBadge = '<span class="bg-emerald-500 text-slate-900 border border-slate-900 px-2.5 py-1 font-extrabold text-[10px] uppercase tracking-wider">✓ SUDAH CHECK-IN (MENUNGGU)</span>';
          } else if (b.status === 'confirmed') {
            statusBadge = '<span class="bg-blue-100 text-blue-900 border border-blue-700 px-2.5 py-1 font-extrabold text-[10px] uppercase tracking-wider">TIKET WA TERKIRIM</span>';
          } else if (b.status === 'waiting_verification') {
            statusBadge = '<span class="bg-amber-100 text-amber-900 border border-amber-700 px-2.5 py-1 font-extrabold text-[10px] uppercase tracking-wider">MENUNGGU KASIR</span>';
          } else if (b.status === 'completed') {
            statusBadge = '<span class="bg-slate-200 text-slate-700 px-2.5 py-1 font-extrabold text-[10px] uppercase tracking-wider">SELESAI</span>';
          } else {
            statusBadge = '<span class="bg-rose-100 text-rose-800 px-2.5 py-1 font-extrabold text-[10px] uppercase tracking-wider">DITOLAK</span>';
          }

          let cashierActions = '';
          if (b.status === 'waiting_verification') {
            cashierActions = `
              <div class="flex justify-end gap-1.5">
                <button type="button" onclick="cashierVerifyBooking(${b.id}, 'accept')" class="bg-emerald-500 hover:bg-emerald-400 text-slate-900 border border-slate-900 px-2.5 py-1 text-[10px] font-extrabold uppercase">
                  Accept &amp; Kirim WA
                </button>
                <button type="button" onclick="cashierVerifyBooking(${b.id}, 'reject')" class="bg-rose-600 hover:bg-rose-500 text-white border border-slate-900 px-2.5 py-1 text-[10px] font-extrabold uppercase">
                  Reject
                </button>
              </div>
            `;
          } else if (b.status === 'confirmed') {
            cashierActions = `
              <div class="flex justify-end gap-1.5">
                <button type="button" onclick="cashierCheckInCustomer(${b.id})" class="bg-blue-700 hover:bg-blue-800 text-white border border-slate-900 px-2.5 py-1 text-[10px] font-extrabold uppercase">
                  📍 Check-In Tiba
                </button>
                <button type="button" onclick="openTicketModal(${b.id})" class="bg-[#F5F4EE] text-slate-900 border border-slate-900 px-2 py-1 text-[10px] font-bold uppercase">
                  Tiket WA
                </button>
              </div>
            `;
          } else {
            cashierActions = `
              <div class="flex justify-end">
                <button type="button" onclick="openTicketModal(${b.id})" class="bg-[#F5F4EE] hover:bg-blue-50 text-slate-900 border border-slate-900 px-2.5 py-1 text-[10px] font-extrabold uppercase">
                  Lihat Tiket WA
                </button>
              </div>
            `;
          }

          return `
            <tr class="hover:bg-[#FAF8F5]">
              <td class="py-3.5 px-4 font-mono font-black text-sm text-blue-700">${b.queue_number}</td>
              <td class="py-3.5 px-4">
                <div class="font-extrabold text-slate-900">${b.customer_name}</div>
                <div class="font-mono text-[11px] text-slate-500">${b.wa_number}</div>
              </td>
              <td class="py-3.5 px-4">
                <div class="font-bold text-slate-900">${b.package_name}</div>
                <div class="text-[11px] text-slate-500">${b.backdrop}</div>
              </td>
              <td class="py-3.5 px-4 font-mono text-slate-800">${b.slot_label}</td>
              <td class="py-3.5 px-4">
                <div>${payBadge}</div>
                <div class="font-mono text-[10px] text-slate-500 mt-0.5 truncate max-w-[160px]">${b.proof_image_path}</div>
              </td>
              <td class="py-3.5 px-4">${statusBadge}</td>
              <td class="py-3.5 px-4 text-right">${cashierActions}</td>
            </tr>
          `;
        })
        .join('');

      // Update Hero Monitor Readouts
      const activeStudio = bookingsState.find((x) => x.status === 'in_studio');
      const nextWaiting = bookingsState.find((x) => x.status === 'checked_in' || x.status === 'confirmed');
      const waitingList = bookingsState.filter((x) => x.status === 'checked_in' || x.status === 'confirmed' || x.status === 'waiting_verification');

      document.getElementById('heroActiveQueue').textContent = activeStudio ? activeStudio.queue_number : 'READY';
      document.getElementById('heroActivePackage').textContent = activeStudio ? activeStudio.package_name : 'Studio Siap Digunakan';
      document.getElementById('heroNextQueue').textContent = nextWaiting ? nextWaiting.queue_number : '-';
      document.getElementById('heroWaitingCount').textContent = waitingList.length + ' pelanggan';
      document.getElementById('heroWaitMinutes').textContent = String(Math.max(5, waitingList.length * 10));
    }

    function cashierVerifyBooking(bookingId, decision) {
      const target = bookingsState.find((b) => b.id === bookingId);
      if (!target) return;
      if (decision === 'accept') {
        target.payment_status = 'verified';
        target.status = 'confirmed';
        latestTicketBooking = target;
        renderSmartQueueTable();
        openTicketModal(target.id);
        showToast('Bukti bayar ' + target.queue_number + ' diterima! Notifikasi WhatsApp otomatis dikirim.');
      } else {
        target.payment_status = 'rejected';
        target.status = 'cancelled';
        renderSmartQueueTable();
        showToast('Bukti bayar ' + target.queue_number + ' ditolak oleh Kasir.');
      }
    }

    function cashierCheckInCustomer(bookingId) {
      const target = bookingsState.find((b) => b.id === bookingId);
      if (!target) return;
      target.status = 'checked_in';
      renderSmartQueueTable();
      showToast('Pelanggan ' + target.queue_number + ' (' + target.customer_name + ') berhasil Check-In di lokasi!');
    }

    function advanceNextQueue() {
      const currentInStudio = bookingsState.find((b) => b.status === 'in_studio');
      if (currentInStudio) {
        currentInStudio.status = 'completed';
      }
      const nextCandidate = bookingsState.find((b) => b.status === 'checked_in') || bookingsState.find((b) => b.status === 'confirmed');
      if (nextCandidate) {
        nextCandidate.status = 'in_studio';
        renderSmartQueueTable();
        showToast('Memanggil nomor antrean ' + nextCandidate.queue_number + ' (' + nextCandidate.customer_name + ') ke Photobox Studio!');
      } else {
        renderSmartQueueTable();
        showToast('Tidak ada antrean berikutnya yang sudah terverifikasi/check-in.');
      }
    }

    function openTicketModal(bookingId) {
      const b = bookingId
        ? bookingsState.find((item) => item.id === bookingId) || latestTicketBooking
        : latestTicketBooking;
      const box = document.getElementById('ticketModalContent');
      box.innerHTML = `
        <div class="text-emerald-700 font-bold border-b border-slate-300 pb-1.5">
          [WHATSAPP GATEWAY — KOTHaK CAFE &amp; PHOTO]
        </div>
        <div>Halo <strong>${b.customer_name}</strong> (${b.wa_number}),</div>
        <div>Reservasi Photobox Studio Anda tercatat di sistem Smart Queue kami:</div>
        <div class="bg-[#F5F4EE] border border-slate-900 p-3 space-y-1">
          <div>NOMOR ANTREAN : <strong class="text-blue-700 text-sm">${b.queue_number}</strong></div>
          <div>PAKET FOTO    : ${b.package_name}</div>
          <div>BACKGROUND    : ${b.backdrop}</div>
          <div>JADWAL SLOT   : ${b.slot_label}</div>
          <div>STATUS BAYAR  : <strong>${b.payment_status.toUpperCase()}</strong></div>
          <div>STATUS ANTREAN: <strong>${b.status.toUpperCase()}</strong></div>
        </div>
        <div class="text-[11px] text-slate-600">
          📍 Lokasi: Jl. AW Sumarmo No.39, Kembaran Kulon, Purbalingga. Tunjukkan tiket ini ke Kasir saat tiba di lokasi untuk Check-In.
        </div>
      `;
      document.getElementById('ticketModal').classList.remove('hidden');
    }

    function closeTicketModal() {
      document.getElementById('ticketModal').classList.add('hidden');
    }

    function openAuthModal() {
      document.getElementById('authModal').classList.remove('hidden');
    }

    function closeAuthModal() {
      document.getElementById('authModal').classList.add('hidden');
    }

    function selectSimulatedUser(roleKey) {
      const roleMap = {
        customer: { label: 'Hana Naila [Customer]', name: 'Hana Naila Rahmadina', wa: '0812-3456-7890' },
        cashier: { label: 'Raka Pratama [Cashier]', name: 'Raka Pratama', wa: '0813-2674-377' },
        admin: { label: 'Admin Kothak [Admin]', name: 'Admin Kothak Studio', wa: '0813-2674-377' }
      };
      const picked = roleMap[roleKey] || roleMap.customer;
      document.getElementById('currentUserLabel').textContent = picked.label;
      document.getElementById('summaryCustomerName').textContent = picked.name;
      document.getElementById('customerWaInput').value = picked.wa;
      closeAuthModal();
      showToast('Beralih sesi pengguna ke: ' + picked.label);
    }

    function inspectProjectFile(filePath) {
      document.getElementById('inspectedFileTitle').textContent = 'File: ' + filePath;
      document.getElementById('inspectedFileCode').textContent = PROJECT_FILES_CODE[filePath] || '';
    }

    function copyTextSafe(text, label) {
      const ta = document.createElement('textarea');
      ta.value = text;
      document.body.appendChild(ta);
      ta.select();
      try {
        document.execCommand('copy');
        showToast(label + ' berhasil disalin ke clipboard!');
      } catch (err) {
        showToast('Gagal menyalin teks.');
      }
      document.body.removeChild(ta);
    }

    function copyFolderTree() {
      copyTextSafe(document.getElementById('folderTreePre').textContent, 'Struktur folder');
    }

    function copyInspectedFile() {
      copyTextSafe(document.getElementById('inspectedFileCode').textContent, 'Kode sumber PHP');
    }

    function copySqlSchema() {
      copyTextSafe(SQL_SCHEMA_TEXT, 'Skema SQL MySQL lengkap');
    }

    // Initialize App on Load
    window.addEventListener('DOMContentLoaded', () => {
      document.getElementById('sqlSchemaPre').textContent = SQL_SCHEMA_TEXT;
      inspectProjectFile('config/database.php');
      renderPackages();
      renderScheduleSlots();
      renderSmartQueueTable();
    });
  </script>
</body>
</html>
