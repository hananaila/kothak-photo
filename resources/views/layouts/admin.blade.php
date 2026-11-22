<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'Admin Panel — KOTHaK Photo Studio')</title>
  <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpeg') }}" />

  <!-- Tailwind CSS & Chart.js -->
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..900;1,9..40,400..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=JetBrains+Mono:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet" />

  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              blue: '#1E3A8A',
              'blue-vivid': '#2D2DB0',
              cream: '#F5F1E8',
              'cream-dark': '#EDE8DC',
              white: '#FFFFFF',
              'near-black': '#1A1A2E',
              'blue-light': '#E8EEF9',
            }
          },
          fontFamily: {
            sans: ['"Inter"', 'system-ui', 'sans-serif'],
            heading: ['"DM Sans"', 'system-ui', 'sans-serif'],
            serif: ['"Playfair Display"', 'Georgia', 'serif'],
            mono: ['"JetBrains Mono"', 'monospace']
          },
          boxShadow: {
            soft: '0 4px 20px -2px rgba(30, 58, 138, 0.08)',
          }
        }
      }
    };
  </script>

  <style>
    body {
      background-color: #F8F7F4;
      color: #1A1A2E;
      font-family: 'Inter', system-ui, sans-serif;
    }
    .font-heading { font-family: 'DM Sans', system-ui, sans-serif; }
    .font-serif { font-family: 'Playfair Display', Georgia, serif; }
    .font-mono { font-family: 'JetBrains Mono', monospace; }

    .shadow-soft {
      box-shadow: 0 4px 20px -2px rgba(26, 26, 46, 0.05), 0 1px 3px rgba(26, 26, 46, 0.02);
    }
  </style>
  @stack('styles')
</head>
<body class="h-screen overflow-hidden flex flex-col md:flex-row bg-[#F8F7F4] text-[#1A1A2E] selection:bg-[#1E3A8A] selection:text-white">

  <!-- SIDEBAR NAVIGATION: TETAP DIAM (STICKY / FIXED 100vh), LOGOUT SELALU TERLIHAT DI BAWAH -->
  <aside class="w-full md:w-64 h-auto md:h-screen bg-[#1E3A8A] text-white shrink-0 flex flex-col justify-between border-r border-white/10 shadow-lg z-30">
    
    <div class="flex-1 flex flex-col min-h-0">
      <!-- Brand Header: Tinggi & Border disamakan persis dengan Topbar kanan (h-16 sm:h-20) -->
      <div class="h-16 sm:h-20 px-5 border-b border-white/10 flex items-center shrink-0">
        <div class="flex items-center gap-3">
          <div class="bg-white p-1 rounded-xl shadow-xs shrink-0">
            <img src="{{ asset('images/logo.jpeg') }}" alt="KOTHaK Photo" class="w-9 h-9 object-contain rounded-lg" />
          </div>
          <div>
            <div class="font-heading font-black text-sm tracking-tight text-white flex items-center gap-1 leading-none">
              <span>KOTHaK</span>
              <span class="font-serif italic font-normal text-white">Studio</span>
            </div>
            <div class="text-[10px] font-heading font-bold uppercase tracking-[0.14em] text-white/80 mt-0.5">
              Admin Panel
            </div>
          </div>
        </div>
      </div>

      <!-- Navigation Menu: Scrollable jika layar pendek, tidak menggeser footer logout -->
      <nav class="p-3.5 space-y-1.5 text-xs font-heading font-bold uppercase tracking-wider overflow-y-auto flex-1">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#2D2DB0] text-white shadow-xs font-black' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
          <span>Analitik &amp; Laporan</span>
        </a>

        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.users.*') ? 'bg-[#2D2DB0] text-white shadow-xs font-black' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
          <span>Kelola Staf &amp; Akun</span>
        </a>

        <a href="{{ route('admin.packages.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.packages.*') ? 'bg-[#2D2DB0] text-white shadow-xs font-black' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
          <span>Manajemen Booth</span>
        </a>

        <!-- Subheading Kasir & Toko -->
        <div class="pt-5 pb-1 px-3 text-[10px] font-mono text-white/50 tracking-wider">
          KASIR &amp; TOKO
        </div>

        <a href="{{ route('cashier.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition-colors">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
          <span>Dasbor Kasir</span>
        </a>

        <a href="{{ route('packages.index') }}" target="_blank" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition-colors">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h2m4 0h6a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
          <span>Tampilan Pelanggan</span>
        </a>
      </nav>
    </div>

    <!-- User Profile & Logout at Bottom: Selalu Terpajang di Bawah Layar -->
    <div class="p-4 border-t border-white/10 bg-[#162B66] shrink-0">
      <div class="mb-3">
        <div class="font-heading font-bold text-xs text-white truncate">{{ auth()->user()->name }}</div>
      </div>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="w-full text-center bg-rose-600 hover:bg-rose-700 text-white py-2 rounded-xl font-heading font-black uppercase tracking-wider text-[11px] shadow-sm transition-all cursor-pointer">
          Keluar
        </button>
      </form>
    </div>
  </aside>

  <!-- MAIN ADMIN CONTENT AREA: DIBAGI DUA: HEADER PERMANEN & KONTEN SCROLL TEPAT DI BAWAH GARIS -->
  <div class="flex-1 h-screen flex flex-col min-w-0 bg-[#F8F7F4] overflow-hidden">

    <!-- TOPBAR: PERMANEN DI ATAS (TIDAK BERGESER & BERWARNA SOLID AGAR TIDAK TEMBUS) -->
    <header class="bg-[#F8F7F4] border-b border-[#E7E2D8] h-16 sm:h-20 px-6 sm:px-8 flex items-center justify-between shadow-2xs shrink-0 z-20">
      <div>
        <h1 class="text-base sm:text-lg font-heading font-black text-[#1A1A2E] leading-tight">
          @yield('page_title', 'Studio Administration')
        </h1>
        <p class="text-xs text-slate-500 font-sans mt-0.5 hidden sm:block">
          Portal manajemen operasional internal KOTHaK Photo Studio
        </p>
      </div>

      <div class="flex items-center gap-2 sm:gap-3">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white text-[#1E3A8A] border border-[#E7E2D8] shadow-xs text-xs font-mono font-bold">
          <svg class="w-3.5 h-3.5 text-[#1E3A8A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
        </div>
      </div>
    </header>

    <!-- SCROLLABLE CONTENT AREA: MULAI PERSIS DARI BAWAH GARIS BATAS NAVBAR -->
    <div class="flex-1 overflow-y-auto min-h-0 flex flex-col">
      <!-- Flash Messages -->
      <div class="px-6 pt-5">
        @if(session('success'))
          <div class="bg-emerald-50 border border-emerald-300 rounded-2xl p-4 text-xs font-bold text-emerald-950 shadow-soft flex items-center justify-between mb-3">
            <div class="flex items-center gap-2.5 min-w-0">
              <span class="bg-emerald-600 text-white px-2.5 py-1 rounded-full text-[10px] font-heading font-black uppercase shrink-0">BERHASIL</span>
              <span class="truncate">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="font-bold text-emerald-800 hover:text-black ml-2 shrink-0 text-base leading-none cursor-pointer">&times;</button>
          </div>
        @endif
        @if(session('error'))
          <div class="bg-rose-50 border border-rose-300 rounded-2xl p-4 text-xs font-bold text-rose-950 shadow-soft flex items-center justify-between mb-3">
            <div class="flex items-center gap-2.5 min-w-0">
              <span class="bg-rose-600 text-white px-2.5 py-1 rounded-full text-[10px] font-heading font-black uppercase shrink-0">PERINGATAN</span>
              <span class="truncate">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="font-bold text-rose-800 hover:text-black ml-2 shrink-0 text-base leading-none cursor-pointer">&times;</button>
          </div>
        @endif
        @if(session('info'))
          <div class="bg-[#E8EEF9] border border-[#1E3A8A]/30 rounded-2xl p-4 text-xs font-bold text-[#1E3A8A] shadow-soft flex items-center justify-between mb-3">
            <div class="flex items-center gap-2.5 min-w-0">
              <span class="bg-[#1E3A8A] text-white px-2.5 py-1 rounded-full text-[10px] font-heading font-black uppercase shrink-0">INFO</span>
              <span class="truncate">{{ session('info') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="font-bold text-[#1E3A8A] hover:text-black ml-2 shrink-0 text-base leading-none cursor-pointer">&times;</button>
          </div>
        @endif
      </div>

      <!-- Page Content View -->
      <main class="flex-1 p-5 sm:p-7 pb-16">
        @yield('content')
      </main>
    </div>

  </div>

  @include('partials.kothak_dialog')

  @stack('scripts')
</body>
</html>