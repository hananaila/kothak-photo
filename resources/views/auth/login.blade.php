@extends('layouts.app')

@section('title', 'Login Pengguna — KOTHaK Photo')

@section('content')
<div class="max-w-md mx-auto my-6">
  <div class="border-2 border-slate-900 bg-white p-6 shadow-[6px_6px_0px_#1D4ED8]">
    
    <div class="border-b-2 border-slate-900 pb-3 mb-5 text-center">
      <span class="text-[10px] font-black uppercase tracking-widest text-blue-700">AUTENTIKASI SISTEM</span>
      <h1 class="text-2xl font-black text-slate-900 mt-1">Masuk ke Akun Anda</h1>
      <p class="text-xs text-slate-500 mt-0.5">Akses riwayat reservasi dan antrean photobox studio</p>
    </div>

    <!-- QUICK 1-CLICK ROLE DEMO LOGINS -->
    <div class="bg-[#F5F4EE] border-2 border-slate-900 p-3 mb-5 space-y-2">
      <div class="text-[10px] font-black uppercase text-slate-600 tracking-wider">Akses Demo Cepat (1-Klik):</div>
      <div class="grid grid-cols-3 gap-2 text-center text-xs">
        <a href="{{ route('auth.quick-login', 'customer') }}" class="neo-btn bg-white hover:bg-blue-50 border border-slate-900 py-1.5 font-bold text-blue-700">
          Customer
        </a>
        <a href="{{ route('auth.quick-login', 'cashier') }}" class="neo-btn bg-white hover:bg-emerald-50 border border-slate-900 py-1.5 font-bold text-emerald-700">
          Kasir
        </a>
        <a href="{{ route('auth.quick-login', 'admin') }}" class="neo-btn bg-white hover:bg-slate-100 border border-slate-900 py-1.5 font-bold text-slate-800">
          Admin
        </a>
      </div>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
      @csrf

      <div>
        <label for="email" class="block text-xs font-black uppercase tracking-wider text-slate-900 mb-1">
          Alamat Email:
        </label>
        <input
          type="email"
          id="email"
          name="email"
          value="{{ old('email', 'hananaeylaa@gmail.com') }}"
          required
          class="w-full border-2 border-slate-900 p-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600"
        />
        @error('email')
          <p class="text-rose-600 text-xs mt-1 font-bold">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="password" class="block text-xs font-black uppercase tracking-wider text-slate-900 mb-1">
          Kata Sandi:
        </label>
        <input
          type="password"
          id="password"
          name="password"
          value="password"
          required
          class="w-full border-2 border-slate-900 p-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600"
        />
      </div>

      <div class="flex items-center justify-between text-xs">
        <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-700">
          <input type="checkbox" name="remember" class="accent-blue-700" />
          <span>Ingat Saya</span>
        </label>
      </div>

      <button
        type="submit"
        class="w-full neo-btn bg-blue-700 hover:bg-blue-800 text-white border-2 border-slate-900 py-3 text-xs font-black uppercase tracking-widest text-center"
      >
        Masuk Sekarang &rarr;
      </button>
    </form>

    <div class="mt-5 pt-4 border-t-2 border-slate-900 text-center text-xs text-slate-600 font-medium">
      Belum punya akun? <a href="{{ route('register') }}" class="text-blue-700 font-bold underline">Daftar Akun Baru</a>
    </div>

  </div>
</div>
@endsection

