<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Login Staf &amp; Admin — KOTHaK Photo Studio</title>
  <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpeg') }}" />

  <!-- Tailwind CSS & Official Fonts -->
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;900&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&family=Playfair+Display:ital,wght@0,600;1,400;1,600&display=swap" rel="stylesheet" />

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Inter"', 'system-ui', 'sans-serif'],
            heading: ['"DM Sans"', 'system-ui', 'sans-serif'],
            serif: ['"Playfair Display"', 'Georgia', 'serif'],
            mono: ['"JetBrains Mono"', 'monospace']
          }
        }
      }
    };
  </script>

  <style>
    body {
      background-color: #F5F1E8;
      color: #1A1A2E;
      font-family: 'Inter', system-ui, sans-serif;
    }
    .font-heading { font-family: 'DM Sans', system-ui, sans-serif; }
    .font-serif { font-family: 'Playfair Display', Georgia, serif; }
    .font-mono { font-family: 'JetBrains Mono', monospace; }
  </style>
</head>
<body class="min-h-screen bg-[#F5F1E8] text-[#1A1A2E] flex items-center justify-center p-4 sm:p-6">

  <!-- LOGIN CARD: CLEAN, MINIMALIS & PROPORSIONAL LEBIH BESAR -->
  <div class="max-w-4xl w-full bg-white rounded-3xl border border-[#1E3A8A]/15 shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-12 min-h-[480px]">

    <!-- SEBELAH KIRI: LOGO KOTHAK PHOTO & IDENTITAS (BERSIH & ELEGAN) -->
    <div class="md:col-span-5 bg-[#1E3A8A] text-white p-10 sm:p-12 flex flex-col items-center justify-center text-center relative overflow-hidden">
      <!-- Glow lembut -->
      <div class="absolute -top-12 -left-12 w-48 h-48 bg-[#2D2DB0] rounded-full blur-2xl opacity-50 pointer-events-none"></div>
      <div class="absolute -bottom-12 -right-12 w-48 h-48 bg-blue-400 rounded-full blur-2xl opacity-20 pointer-events-none"></div>

      <div class="relative z-10 flex flex-col items-center">
        <!-- Logo KOTHaK -->
        <div class="bg-white p-2.5 rounded-2xl shadow-lg border-2 border-white/20 mb-5">
          <img src="{{ asset('images/logo.jpeg') }}" alt="KOTHaK Photo" class="w-24 h-24 sm:w-28 sm:h-28 object-contain rounded-xl" />
        </div>

        <!-- Nama Brand -->
        <div class="font-heading font-black text-2xl sm:text-3xl tracking-tight text-white flex items-center gap-1.5 leading-none">
          <span>KOTHaK</span>
          <span class="font-serif italic font-normal text-2xl sm:text-3xl text-[#F5F1E8]">Photo</span>
        </div>

        <p class="text-xs font-mono uppercase tracking-[0.2em] text-amber-300 font-bold mt-2.5">
          Purbalingga
        </p>

        <span class="mt-5 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 text-[11px] font-heading font-bold uppercase tracking-wider text-slate-200">
          Portal Internal Staf
        </span>
      </div>
    </div>

    <!-- SEBELAH KANAN: FORM LOGIN LANGSUNG RAPI (EMAIL & PASSWORD) -->
    <div class="md:col-span-7 p-8 sm:p-12 flex flex-col justify-center">
      <div>
        
        <h1 class="text-2xl sm:text-3xl font-heading font-black text-[#1A1A2E] leading-tight">
          Login Staf &amp; Admin
        </h1>
        <p class="text-sm text-slate-500 font-sans mt-1">
          Masuk ke akun kasir atau admin studio
        </p>

        <!-- Flash Messages -->
        @if(session('error'))
          <div class="mt-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-black font-bold">&times;</button>
          </div>
        @endif
        @if(session('success'))
          <div class="mt-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-black font-bold">&times;</button>
          </div>
        @endif

        <!-- FORM LOGIN LANGSUNG RAPI -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-5 mt-6">
          @csrf

          <div>
            <label for="email" class="block text-xs font-heading font-bold uppercase tracking-wider text-slate-600 mb-1.5">
              Alamat Email
            </label>
            <input
              type="email"
              id="email"
              name="email"
              value="{{ old('email', 'cashier@kothakphoto.id') }}"
              required
              autocomplete="email"
              placeholder="nama@kothakphoto.id"
              class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm font-medium text-[#1A1A2E] bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1E3A8A] focus:border-transparent transition-all"
            />
            @error('email')
              <p class="text-rose-600 text-xs mt-1.5 font-bold">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="password" class="block text-xs font-heading font-bold uppercase tracking-wider text-slate-600 mb-1.5">
              Kata Sandi
            </label>
            <div class="relative">
              <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Masukkan kata sandi"
                class="w-full rounded-xl border border-slate-300 px-4 pr-11 py-3 text-sm font-medium text-[#1A1A2E] bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1E3A8A] focus:border-transparent transition-all"
              />
              <button
                type="button"
                onclick="togglePassword()"
                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                title="Lihat / Sembunyikan Sandi"
              >
                <svg id="eyeOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg id="eyeClosed" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between text-xs pt-1">
            <label class="flex items-center gap-2 cursor-pointer font-heading font-medium text-[#1A1A2E]">
              <input type="checkbox" name="remember" class="accent-[#1E3A8A] w-4 h-4 rounded cursor-pointer" />
              <span>Ingat saya</span>
            </label>
          </div>

          <button
            type="submit"
            class="w-full bg-[#1E3A8A] hover:bg-[#2D2DB0] text-white py-3.5 px-4 text-xs sm:text-sm font-heading font-black uppercase tracking-wider text-center rounded-xl shadow-md hover:shadow-lg transition-all cursor-pointer mt-2"
          >
            Masuk
          </button>
        </form>

        <!-- Akses Cepat Login 1-Klik untuk Uji Coba & Demo -->
        <div class="mt-5 pt-4 border-t border-slate-100 space-y-2">
          <span class="block text-[11px] font-heading font-bold uppercase text-slate-400 text-center tracking-wider">
            Akses Cepat Pengujian (1-Klik)
          </span>
          <div class="grid grid-cols-2 gap-2 text-xs font-heading font-bold">
            <a href="{{ route('auth.quick-login', 'admin') }}" class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-[#1E3A8A] text-slate-700 hover:text-white transition-all text-center flex items-center justify-center gap-1.5 shadow-2xs">
              <span>Login Admin</span> &rarr;
            </a>
            <a href="{{ route('auth.quick-login', 'cashier') }}" class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-[#1E3A8A] text-slate-700 hover:text-white transition-all text-center flex items-center justify-center gap-1.5 shadow-2xs">
              <span>Login Kasir</span> &rarr;
            </a>
          </div>
        </div>
      </div>

    </div>

  </div>

  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      const open = document.getElementById('eyeOpen');
      const closed = document.getElementById('eyeClosed');

      if (input.type === 'password') {
        input.type = 'text';
        open.classList.add('hidden');
        closed.classList.remove('hidden');
      } else {
        input.type = 'password';
        open.classList.remove('hidden');
        closed.classList.add('hidden');
      }
    }
  </script>
</body>
</html>
