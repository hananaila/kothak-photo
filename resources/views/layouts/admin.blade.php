<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'Admin Panel — KOTHaK Photo Studio')</title>

  <!-- Tailwind CSS & Chart.js -->
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    body { background-color: #F8FAFC; color: #0F172A; font-family: 'Plus Jakarta Sans', sans-serif; }
    .neo-box { border: 2px solid #0F172A; box-shadow: 4px 4px 0px #0F172A; }
    .neo-btn { border: 2px solid #0F172A; box-shadow: 3px 3px 0px #0F172A; transition: transform 0.12s ease, box-shadow 0.12s ease; }
    .neo-btn:hover:not(:disabled) { transform: translate(-1px, -1px); box-shadow: 5px 5px 0px #0F172A; }
    .neo-btn:active:not(:disabled) { transform: translate(2px, 2px); box-shadow: 1px 1px 0px #0F172A; }
  </style>
  @stack('styles')
</head>
<body class="min-h-screen flex flex-col md:flex-row selection:bg-blue-700 selection:text-white">

  <!-- SIDEBAR NAVIGATION -->
  <aside class="w-full md:w-64 bg-slate-900 text-white shrink-0 border-r-2 border-slate-900 flex flex-col">
    <!-- Brand -->
    <div class="p-4 sm:p-5 border-b border-slate-800 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img src="{{ asset('assets/img/logo kothak cafe.jpg') }}" alt="Logo" class="w-9 h-9 object-cover border border-white" />
        <div>
          <div class="font-extrabold text-sm tracking-tight text-white">KOTHaK Studio</div>
          <div class="text-[10px] font-mono text-amber-400 font-bold uppercase tracking-wider">Admin Panel</div>
        </div>
      </div>
      <a href="{{ route('home') }}" class="text-xs text-slate-400 hover:text-white" title="Lihat Website Toko">↗</a>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 p-3 space-y-1 text-xs font-bold uppercase tracking-wider">
      <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-amber-400 text-slate-900 font-black' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <span>📊</span> <span>Analitik & Laporan</span>
      </a>

      <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-amber-400 text-slate-900 font-black' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <span>👥</span> <span>Kelola Pengguna (FR-01)</span>
      </a>

      <a href="{{ route('admin.packages.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition-colors {{ request()->routeIs('admin.packages.*') ? 'bg-amber-400 text-slate-900 font-black' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <span>📸</span> <span>Paket & Layanan (FR-07)</span>
      </a>

      <a href="{{ route('admin.schedules.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition-colors {{ request()->routeIs('admin.schedules.*') ? 'bg-amber-400 text-slate-900 font-black' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <span>📅</span> <span>Jadwal & Slot (FR-02)</span>
      </a>

      <div class="pt-4 pb-1 border-t border-slate-800 text-[10px] text-slate-500 px-3 font-mono">
        OPERATOR & LAYAR
      </div>

      <a href="{{ route('cashier.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-emerald-400 hover:bg-slate-800 hover:text-emerald-300">
        <span>💵</span> <span>Dasbor Kasir (FR-08)</span>
      </a>

      <a href="{{ route('queue.display') }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded text-blue-400 hover:bg-slate-800 hover:text-blue-300">
        <span>📺</span> <span>Layar Antrean TV (FR-04)</span>
      </a>

      <a href="{{ route('packages.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-400 hover:bg-slate-800 hover:text-white">
        <span>🏪</span> <span>Tampilan Pelanggan</span>
      </a>
    </nav>

    <!-- User Profile & Logout -->
    <div class="p-3 border-t border-slate-800 bg-slate-950 text-xs">
      <div class="flex items-center justify-between mb-2">
        <div class="truncate">
          <div class="font-bold text-white truncate">{{ auth()->user()->name }}</div>
          <div class="text-[10px] text-amber-400 font-mono">{{ auth()->user()->email }}</div>
        </div>
      </div>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="w-full text-center bg-rose-600 hover:bg-rose-700 text-white py-1.5 font-bold uppercase tracking-wider text-[10px]">
          Keluar (Logout)
        </button>
      </form>
    </div>
  </aside>

  <!-- MAIN ADMIN CONTENT AREA -->
  <div class="flex-1 flex flex-col min-w-0">
    <!-- Topbar -->
    <header class="bg-white border-b-2 border-slate-900 px-4 sm:px-6 py-3.5 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <h2 class="text-base sm:text-lg font-black text-slate-900">
          @yield('page_title', 'Studio Administration')
        </h2>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('admin.reports.print') }}" target="_blank" class="neo-btn bg-white hover:bg-slate-50 text-slate-900 px-3 py-1.5 text-xs font-bold flex items-center gap-1.5">
          <span>🖨️</span> <span class="hidden sm:inline">Cetak / PDF</span>
        </a>
        <a href="{{ route('admin.reports.csv') }}" class="neo-btn bg-emerald-500 hover:bg-emerald-600 text-slate-900 px-3 py-1.5 text-xs font-bold flex items-center gap-1.5">
          <span>📥</span> <span class="hidden sm:inline">Ekspor CSV</span>
        </a>
      </div>
    </header>

    <!-- Flash Messages -->
    <div class="px-4 sm:px-6 pt-4">
      @if(session('success'))
        <div class="bg-emerald-100 border-2 border-slate-900 p-3 text-xs font-bold text-emerald-900 shadow-[3px_3px_0px_#0F172A] flex items-center justify-between mb-3">
          <div class="flex items-center gap-2"><span class="bg-emerald-600 text-white px-2 py-0.5 text-[10px] font-black uppercase">BERHASIL</span> <span>{{ session('success') }}</span></div>
          <button onclick="this.parentElement.remove()" class="font-bold text-slate-600 hover:text-black">✕</button>
        </div>
      @endif
      @if(session('error'))
        <div class="bg-rose-100 border-2 border-slate-900 p-3 text-xs font-bold text-rose-900 shadow-[3px_3px_0px_#0F172A] flex items-center justify-between mb-3">
          <div class="flex items-center gap-2"><span class="bg-rose-600 text-white px-2 py-0.5 text-[10px] font-black uppercase">PERINGATAN</span> <span>{{ session('error') }}</span></div>
          <button onclick="this.parentElement.remove()" class="font-bold text-slate-600 hover:text-black">✕</button>
        </div>
      @endif
      @if(session('info'))
        <div class="bg-blue-100 border-2 border-slate-900 p-3 text-xs font-bold text-blue-900 shadow-[3px_3px_0px_#0F172A] flex items-center justify-between mb-3">
          <div class="flex items-center gap-2"><span class="bg-blue-600 text-white px-2 py-0.5 text-[10px] font-black uppercase">INFO</span> <span>{{ session('info') }}</span></div>
          <button onclick="this.parentElement.remove()" class="font-bold text-slate-600 hover:text-black">✕</button>
        </div>
      @endif
    </div>

    <!-- Page Content -->
    <main class="flex-1 p-4 sm:p-6 overflow-y-auto">
      @yield('content')
    </main>
  </div>

  @stack('scripts')
</body>
</html>
