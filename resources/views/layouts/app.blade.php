<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'KOTHaK Photo Studio — Smart Queue & Online Booking')</title>
  <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpeg') }}" />

  <!-- Tailwind CSS & Official Brand Google Fonts -->
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
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
            'soft': '0 4px 20px -2px rgba(30, 58, 138, 0.08)',
            'soft-lg': '0 10px 30px -4px rgba(30, 58, 138, 0.12)',
            'card': '0 2px 12px -2px rgba(26, 26, 46, 0.06)'
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

    /* Modern Soft & Harmonious UI Elements */
    .kothak-card {
      background-color: #FFFFFF;
      border: 1px solid #E7E2D8;
      border-radius: 1rem; /* rounded-2xl */
      box-shadow: 0 4px 20px -2px rgba(26, 26, 46, 0.05), 0 1px 3px rgba(26, 26, 46, 0.02);
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .kothak-card:hover {
      box-shadow: 0 10px 30px -4px rgba(30, 58, 138, 0.10);
      transform: translateY(-2px);
    }

    .kothak-card-cream {
      background-color: #F3EFE6;
      border: 1px solid #E2DDD3;
      border-radius: 1rem;
      box-shadow: 0 4px 15px -2px rgba(26, 26, 46, 0.04);
    }

    /* Buttons with smooth rounded corners */
    .kothak-btn-primary {
      background-color: #1E3A8A;
      color: #FFFFFF;
      border: 1px solid #1E3A8A;
      border-radius: 0.75rem; /* rounded-xl */
      font-family: 'DM Sans', sans-serif;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25);
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .kothak-btn-primary:hover:not(:disabled) {
      background-color: #2D2DB0;
      border-color: #2D2DB0;
      transform: translateY(-1.5px);
      box-shadow: 0 6px 18px rgba(45, 45, 176, 0.35);
    }
    .kothak-btn-primary:active:not(:disabled) {
      transform: translateY(0.5px);
      box-shadow: 0 2px 6px rgba(30, 58, 138, 0.2);
    }

    .kothak-btn-secondary {
      background-color: #EDE8DC;
      color: #1E3A8A;
      border: 1px solid rgba(30, 58, 138, 0.25);
      border-radius: 0.75rem;
      font-family: 'DM Sans', sans-serif;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      transition: all 0.2s ease;
    }
    .kothak-btn-secondary:hover:not(:disabled) {
      background-color: #1E3A8A;
      color: #FFFFFF;
      border-color: #1E3A8A;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(30, 58, 138, 0.18);
    }

    .kothak-btn-white {
      background-color: #FFFFFF;
      color: #1E3A8A;
      border: 1px solid rgba(30, 58, 138, 0.2);
      border-radius: 0.75rem;
      font-family: 'DM Sans', sans-serif;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      transition: all 0.2s ease;
    }
    .kothak-btn-white:hover:not(:disabled) {
      background-color: #E8EEF9;
      border-color: #1E3A8A;
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(30, 58, 138, 0.12);
    }

    /* Soft modern overrides for legacy neo-box / neo-btn */
    .neo-box {
      border: 1px solid rgba(30, 58, 138, 0.18);
      border-radius: 1rem;
      box-shadow: 0 4px 16px rgba(30, 58, 138, 0.06);
    }
    .neo-btn {
      border: 1px solid rgba(30, 58, 138, 0.3);
      border-radius: 0.75rem;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .neo-btn:hover:not(:disabled) {
      transform: translateY(-1.5px);
      box-shadow: 0 4px 12px rgba(30, 58, 138, 0.15);
    }

    #mobileMenuPanel { transition: max-height 0.3s ease; overflow: hidden; max-height: 0; }
    #mobileMenuPanel.open { max-height: 500px; }
  </style>
  @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-[#F8F7F4] text-[#1A1A2E] selection:bg-[#1E3A8A] selection:text-white">

  <!-- CLEAN APP HEADER (FULL-WIDTH EDGE-TO-EDGE: SAMPAI POJOK KIRI & KANAN) -->
  <header class="sticky top-0 z-50 bg-[#F8F7F4] border-b border-[#E7E2D8] shadow-xs">
    <div class="w-full px-4 sm:px-8 lg:px-12 h-16 sm:h-20 flex items-center justify-between gap-3">

      <!-- Brand Logo & Title: Clean, Focused -->
      <a href="{{ route('packages.index') }}" class="flex items-center gap-3 group focus:outline-none shrink-0">
        <div class="bg-white p-1.5 rounded-xl border border-[#1E3A8A]/20 transition-all duration-300 group-hover:scale-105 shadow-sm">
          <img src="{{ asset('images/logo.jpeg') }}" alt="KOTHaK Photo Studio" class="h-9 sm:h-11 w-auto object-contain rounded-lg" />
        </div>
        <div>
          <div class="font-heading font-black text-base sm:text-xl tracking-tight text-[#1E3A8A] leading-none flex items-center gap-1.5">
            <span>KOTHaK</span>
            <span class="text-[#1A1A2E] font-serif italic text-lg sm:text-2xl font-normal">Photo</span>
          </div>
          <div class="text-[9px] sm:text-[10px] font-heading font-bold uppercase tracking-[0.16em] text-[#1A1A2E]/70 mt-0.5">
            Photobox &bull; Purbalingga
          </div>
        </div>
      </a>

    </div>
  </header>

  <!-- NOTIFICATION FLASH MESSAGES -->
  <div class="w-full px-4 sm:px-8 lg:px-12 mt-4">
    @if(session('error'))
      <div class="bg-rose-50 border border-rose-300 rounded-2xl p-4 text-xs font-bold text-rose-950 shadow-soft flex items-center justify-between mb-3">
        <div class="flex items-center gap-2.5 min-w-0">
          <span class="bg-rose-600 text-white px-2.5 py-1 rounded-full text-[10px] font-heading font-black uppercase shrink-0">PERINGATAN</span>
          <span class="truncate">{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="font-bold text-rose-800 hover:text-black ml-2 shrink-0 text-base leading-none">&times;</button>
      </div>
    @endif
    @if(session('info'))
      <div class="bg-[#E8EEF9] border border-[#1E3A8A]/30 rounded-2xl p-4 text-xs font-bold text-[#1E3A8A] shadow-soft flex items-center justify-between mb-3">
        <div class="flex items-center gap-2.5 min-w-0">
          <span class="bg-[#1E3A8A] text-white px-2.5 py-1 rounded-full text-[10px] font-heading font-black uppercase shrink-0">INFO</span>
          <span class="truncate">{{ session('info') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="font-bold text-[#1E3A8A] hover:text-black ml-2 shrink-0 text-base leading-none">&times;</button>
      </div>
    @endif
  </div>

  <!-- MAIN BODY CONTENT -->
  <main class="flex-1 w-full px-4 sm:px-8 lg:px-12 py-4 sm:py-6 pb-12 sm:pb-16">
    @yield('content')
  </main>

  <script>
    function toggleMobileMenu() {
      document.getElementById('mobileMenuPanel').classList.toggle('open');
    }
  </script>
  @include('partials.kothak_dialog')
  @stack('scripts')
</body>
</html>
