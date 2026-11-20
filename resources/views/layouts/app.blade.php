<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'KOTHaK Cafe & Photo — Smart Queue & Online Booking')</title>

  <!-- Tailwind CSS & Google Fonts -->
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet" />

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
    body { background-color: #F5F4EE; color: #0F172A; font-family: 'Plus Jakarta Sans', sans-serif; }
    .font-editorial { font-family: 'Instrument Serif', Georgia, serif; }
    .neo-box { border: 2px solid #0F172A; box-shadow: 4px 4px 0px #1D4ED8; }
    .neo-box-dark { border: 2px solid #1D4ED8; box-shadow: 4px 4px 0px #0F172A; }
    .neo-box-black { border: 2px solid #0F172A; box-shadow: 4px 4px 0px #0F172A; }
    .neo-btn { border: 2px solid #0F172A; box-shadow: 3px 3px 0px #0F172A; transition: transform 0.12s ease, box-shadow 0.12s ease; }
    .neo-btn:hover:not(:disabled) { transform: translate(-1px, -1px); box-shadow: 5px 5px 0px #0F172A; }
    .neo-btn:active:not(:disabled) { transform: translate(2px, 2px); box-shadow: 1px 1px 0px #0F172A; }
    .checker-white { background-image: linear-gradient(45deg, rgba(255,255,255,0.12) 25%, transparent 25%), linear-gradient(-45deg, rgba(255,255,255,0.12) 25%, transparent 25%), linear-gradient(45deg, transparent 75%, rgba(255,255,255,0.12) 75%), linear-gradient(-45deg, transparent 75%, rgba(255,255,255,0.12) 75%); background-size: 24px 24px; background-position: 0 0, 0 12px, 12px -12px, -12px 0px; }

    #mobileMenuPanel { transition: max-height 0.3s ease; overflow: hidden; max-height: 0; }
    #mobileMenuPanel.open { max-height: 500px; }
  </style>
  @stack('styles')
</head>
<body class="min-h-screen flex flex-col selection:bg-blue-700 selection:text-white">

  <!-- TOP BAR: QUICK DEMO ROLE SWITCHER (For Testing & Presentation) -->
  <div class="bg-slate-900 text-white text-[11px] py-1.5 px-3 sm:px-6 border-b border-slate-800">
    <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
      <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        <span class="font-bold text-slate-300">Demo Role Switch:</span>
        <div class="inline-flex items-center gap-1 font-mono">
          <a href="{{ route('auth.quick-login', 'customer') }}" class="px-2 py-0.5 rounded {{ auth()->check() && auth()->user()->role === 'customer' ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Pelanggan</a>
          <span class="text-slate-600">|</span>
          <a href="{{ route('auth.quick-login', 'cashier') }}" class="px-2 py-0.5 rounded {{ auth()->check() && auth()->user()->role === 'cashier' ? 'bg-emerald-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Kasir</a>
          <span class="text-slate-600">|</span>
          <a href="{{ route('auth.quick-login', 'admin') }}" class="px-2 py-0.5 rounded {{ auth()->check() && auth()->user()->role === 'admin' ? 'bg-amber-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Admin Studio</a>
        </div>
      </div>
      <div class="flex items-center gap-3 text-slate-400">
        <a href="{{ route('queue.display') }}" target="_blank" class="hover:text-blue-300 flex items-center gap-1 font-bold">
          <span>📺</span> Layar Antrean TV
        </a>
      </div>
    </div>
  </div>

  <!-- STICKY HEADER — RESPONSIVE -->
  <header class="sticky top-0 z-40 bg-[#F5F4EE]/95 backdrop-blur-sm border-b-2 border-blue-700">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between gap-2">
      <!-- Brand Logo -->
      <a href="{{ route('packages.index') }}" class="flex items-center gap-2 sm:gap-3 group focus:outline-none min-w-0 shrink">
        <img src="{{ asset('assets/img/logo kothak cafe.jpg') }}" alt="KOTHaK Cafe & Photo" class="w-9 h-9 sm:w-11 sm:h-11 object-cover border-2 border-slate-900 shadow-[2px_2px_0px_#1D4ED8] shrink-0" />
        <div class="min-w-0">
          <div class="font-extrabold text-sm sm:text-xl tracking-tight text-slate-900 leading-none truncate">
            KOTHaK <span class="text-blue-700">Photo</span>
          </div>
          <div class="text-[9px] sm:text-[11px] font-semibold uppercase tracking-widest text-slate-600 mt-0.5 truncate hidden sm:block">
            Smart Queue &bull; Photobox Purbalingga
          </div>
        </div>
      </a>

      <!-- Desktop Nav Links -->
      <nav class="hidden lg:flex items-center gap-4 xl:gap-6 text-xs font-extrabold tracking-widest uppercase shrink-0">
        <a href="{{ route('packages.index') }}" class="text-slate-900 hover:text-blue-700 py-1 {{ request()->routeIs('packages.index') ? 'border-b-2 border-blue-700 text-blue-700' : 'border-b-2 border-transparent' }}">KATALOG PAKET</a>
        <a href="{{ route('booking.history') }}" class="text-slate-700 hover:text-blue-700 py-1 {{ request()->routeIs('booking.history') ? 'border-b-2 border-blue-700 text-blue-700' : 'border-b-2 border-transparent' }}">RIWAYAT & TIKET</a>
        <a href="{{ route('queue.display') }}" target="_blank" class="text-slate-700 hover:text-blue-700 py-1 border-b-2 border-transparent flex items-center gap-1.5">
          <span class="w-2 h-2 bg-emerald-500 inline-block animate-pulse"></span>
          LAYAR ANTREAN ↗
        </a>

        @if(auth()->check() && (auth()->user()->isCashier() || auth()->user()->isAdmin()))
          <a href="{{ route('cashier.dashboard') }}" class="bg-emerald-500 hover:bg-emerald-600 text-slate-900 px-3 py-1.5 border-2 border-slate-900 shadow-[2px_2px_0px_#0F172A] font-black">
            MODUL KASIR
          </a>
        @endif

        @if(auth()->check() && auth()->user()->isAdmin())
          <a href="{{ route('admin.dashboard') }}" class="bg-amber-400 hover:bg-amber-500 text-slate-900 px-3 py-1.5 border-2 border-slate-900 shadow-[2px_2px_0px_#0F172A] font-black">
            ADMIN STUDIO
          </a>
        @endif
      </nav>

      <!-- Right Side: User Profile & Auth / Hamburger -->
      <div class="flex items-center gap-2 shrink-0">
        @auth
          <div class="relative group">
            <div class="neo-btn bg-white px-2.5 sm:px-3 py-1.5 sm:py-2 text-[10px] sm:text-xs font-extrabold uppercase tracking-wider flex items-center gap-1.5 cursor-pointer">
              <span class="w-2 h-2 {{ auth()->user()->isAdmin() ? 'bg-amber-500' : (auth()->user()->isCashier() ? 'bg-emerald-500' : 'bg-blue-700') }} shrink-0"></span>
              <span class="truncate max-w-[120px] sm:max-w-[160px]">{{ auth()->user()->name }}</span>
              <span class="text-[9px] bg-slate-100 border border-slate-300 px-1 py-0.2 uppercase font-mono">{{ auth()->user()->role }}</span>
            </div>
            <!-- Dropdown -->
            <div class="absolute right-0 top-full mt-1 w-48 bg-white border-2 border-slate-900 shadow-[4px_4px_0px_#0F172A] hidden group-hover:block z-50 text-xs">
              <div class="p-2 border-b border-slate-200 bg-slate-50">
                <div class="font-bold text-slate-900 truncate">{{ auth()->user()->name }}</div>
                <div class="text-[10px] font-mono text-slate-500 truncate">{{ auth()->user()->email }}</div>
              </div>
              <a href="{{ route('booking.history') }}" class="block px-3 py-2 text-slate-800 hover:bg-blue-50 font-bold">Riwayat Booking Saya</a>
              @if(auth()->user()->isCashier())
                <a href="{{ route('cashier.dashboard') }}" class="block px-3 py-2 text-emerald-800 hover:bg-emerald-50 font-bold">Dasbor Kasir</a>
              @endif
              @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-amber-900 hover:bg-amber-50 font-bold">Dasbor Admin Studio</a>
              @endif
              <form action="{{ route('logout') }}" method="POST" class="border-t border-slate-200">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 text-rose-700 hover:bg-rose-50 font-bold">Keluar (Logout)</button>
              </form>
            </div>
          </div>
        @else
          <a href="{{ route('login') }}" class="neo-btn bg-white hover:bg-blue-50 text-slate-900 px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-black uppercase tracking-wider">
            MASUK
          </a>
          <a href="{{ route('register') }}" class="neo-btn bg-blue-700 hover:bg-blue-800 text-white px-3 sm:px-4 py-1.5 sm:py-2 text-[10px] sm:text-xs font-black uppercase tracking-wider hidden sm:inline-block">
            DAFTAR
          </a>
        @endauth

        <button type="button" onclick="toggleMobileMenu()" class="lg:hidden border-2 border-slate-900 bg-white p-1.5 sm:p-2 text-slate-900" aria-label="Toggle Menu">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="square" d="M4 6h16M4 12h16M4 18h16" /></svg>
        </button>
      </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div id="mobileMenuPanel" class="lg:hidden bg-[#F5F4EE] border-t-2 border-slate-900 px-4 space-y-1 text-xs font-extrabold uppercase tracking-widest">
      <a href="{{ route('packages.index') }}" class="block py-2.5 text-blue-700 border-b border-slate-200">KATALOG PAKET FOTO</a>
      <a href="{{ route('booking.history') }}" class="block py-2.5 text-slate-900 border-b border-slate-200">RIWAYAT & TIKET</a>
      <a href="{{ route('queue.display') }}" target="_blank" class="block py-2.5 text-slate-900 border-b border-slate-200">LAYAR ANTREAN TV ↗</a>
      @if(auth()->check() && (auth()->user()->isCashier() || auth()->user()->isAdmin()))
        <a href="{{ route('cashier.dashboard') }}" class="block py-2.5 text-emerald-800 font-black border-b border-slate-200">MODUL KASIR</a>
      @endif
      @if(auth()->check() && auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="block py-2.5 text-amber-800 font-black border-b border-slate-200">ADMIN STUDIO</a>
      @endif
      @auth
        <form action="{{ route('logout') }}" method="POST" class="pt-2 pb-3">
          @csrf
          <button type="submit" class="w-full text-left font-black text-rose-700">KELUAR (LOGOUT)</button>
        </form>
      @else
        <div class="flex gap-2 pt-2 pb-3">
          <a href="{{ route('login') }}" class="flex-1 text-center py-2 bg-white border border-slate-900 font-bold">MASUK</a>
          <a href="{{ route('register') }}" class="flex-1 text-center py-2 bg-blue-700 text-white border border-slate-900 font-bold">DAFTAR</a>
        </div>
      @endauth
    </div>
  </header>

  <!-- NOTIFICATION FLASH MESSAGES -->
  <div class="max-w-7xl mx-auto w-full px-3 sm:px-4 mt-3">
    @if(session('success'))
      <div class="bg-emerald-100 border-2 border-slate-900 p-3 sm:p-4 text-xs font-bold text-emerald-900 shadow-[4px_4px_0px_#0F172A] flex items-center justify-between mb-3">
        <div class="flex items-center gap-2 min-w-0">
          <span class="bg-emerald-600 text-white px-2 py-0.5 text-[10px] font-black uppercase shrink-0">BERHASIL</span>
          <span class="truncate">{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="font-bold text-slate-600 hover:text-black ml-2 shrink-0">✕</button>
      </div>
    @endif
    @if(session('error'))
      <div class="bg-rose-100 border-2 border-slate-900 p-3 sm:p-4 text-xs font-bold text-rose-900 shadow-[4px_4px_0px_#0F172A] flex items-center justify-between mb-3">
        <div class="flex items-center gap-2 min-w-0">
          <span class="bg-rose-600 text-white px-2 py-0.5 text-[10px] font-black uppercase shrink-0">PERINGATAN</span>
          <span class="truncate">{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="font-bold text-slate-600 hover:text-black ml-2 shrink-0">✕</button>
      </div>
    @endif
    @if(session('info'))
      <div class="bg-blue-100 border-2 border-slate-900 p-3 sm:p-4 text-xs font-bold text-blue-900 shadow-[4px_4px_0px_#0F172A] flex items-center justify-between mb-3">
        <div class="flex items-center gap-2 min-w-0">
          <span class="bg-blue-600 text-white px-2 py-0.5 text-[10px] font-black uppercase shrink-0">INFO</span>
          <span class="truncate">{{ session('info') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="font-bold text-slate-600 hover:text-black ml-2 shrink-0">✕</button>
      </div>
    @endif
  </div>

  <!-- MAIN BODY CONTENT -->
  <main class="flex-1 max-w-7xl w-full mx-auto px-3 sm:px-4 md:px-6 py-4 md:py-6">
    @yield('content')
  </main>

  <!-- FOOTER -->
  <footer class="bg-white border-t-2 border-slate-900 mt-12 text-xs">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 py-8 flex flex-col md:flex-row items-center justify-between gap-4">
      <div class="space-y-1 text-center md:text-left">
        <div class="font-extrabold text-sm tracking-tight text-slate-900">KOTHaK Cafe &amp; Photo Studio &bull; Purbalingga</div>
        <div class="text-slate-600 text-[11px]">Jl. AW Sumarmo No.39, Kembaran Kulon, Kec. Purbalingga, Kabupaten Purbalingga, Jawa Tengah 53319</div>
        <div class="text-slate-500 text-[10px]">Jam Buka: Setiap Hari 09.00 - 23.00 WIB &bull; WhatsApp: +62 813-2674-377</div>
      </div>
      <div class="font-mono text-[11px] text-slate-600 text-center md:text-right space-y-1">
        <div>Smart Queue &amp; Online Booking Platform</div>
        <div class="text-blue-700 font-bold">NMID: ID2026KOTHAKPBG01 &bull; QRIS Standar Bank Indonesia</div>
      </div>
    </div>
  </footer>

  <script>
    function toggleMobileMenu() {
      document.getElementById('mobileMenuPanel').classList.toggle('open');
    }
  </script>
  @stack('scripts')
</body>
</html>
