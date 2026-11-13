@extends('layouts.app')

@section('title', 'Daftar Akun Baru — KOTHaK Photo')

@section('content')
<div class="max-w-md mx-auto my-6">
  <div class="border-2 border-slate-900 bg-white p-6 shadow-[6px_6px_0px_#1D4ED8]">
    
    <div class="border-b-2 border-slate-900 pb-3 mb-5 text-center">
      <span class="text-[10px] font-black uppercase tracking-widest text-blue-700">REGISTRASI PELANGGAN</span>
      <h1 class="text-2xl font-black text-slate-900 mt-1">Buat Akun Baru</h1>
      <p class="text-xs text-slate-500 mt-0.5">Dapatkan kemudahan reservasi photobox dan tiket digital</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
      @csrf

      <div>
        <label for="name" class="block text-xs font-black uppercase tracking-wider text-slate-900 mb-1">
          Nama Lengkap:
        </label>
        <input
          type="text"
          id="name"
          name="name"
          value="{{ old('name') }}"
          required
          class="w-full border-2 border-slate-900 p-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600"
        />
        @error('name')
          <p class="text-rose-600 text-xs mt-1 font-bold">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="email" class="block text-xs font-black uppercase tracking-wider text-slate-900 mb-1">
          Alamat Email:
        </label>
        <input
          type="email"
          id="email"
          name="email"
          value="{{ old('email') }}"
          required
          class="w-full border-2 border-slate-900 p-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600"
        />
        @error('email')
          <p class="text-rose-600 text-xs mt-1 font-bold">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="phone_whatsapp" class="block text-xs font-black uppercase tracking-wider text-slate-900 mb-1">
          Nomor WhatsApp:
        </label>
        <input
          type="text"
          id="phone_whatsapp"
          name="phone_whatsapp"
          value="{{ old('phone_whatsapp') }}"
          required
          placeholder="081234567890"
          class="w-full border-2 border-slate-900 p-2.5 text-xs font-bold font-mono text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600"
        />
        @error('phone_whatsapp')
          <p class="text-rose-600 text-xs mt-1 font-bold">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="password" class="block text-xs font-black uppercase tracking-wider text-slate-900 mb-1">
          Kata Sandi (Min. 6 Karakter):
        </label>
        <input
          type="password"
          id="password"
          name="password"
          required
          class="w-full border-2 border-slate-900 p-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600"
        />
        @error('password')
          <p class="text-rose-600 text-xs mt-1 font-bold">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="password_confirmation" class="block text-xs font-black uppercase tracking-wider text-slate-900 mb-1">
          Ulangi Kata Sandi:
        </label>
        <input
          type="password"
          id="password_confirmation"
          name="password_confirmation"
          required
          class="w-full border-2 border-slate-900 p-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600"
        />
      </div>

      <button
        type="submit"
        class="w-full neo-btn bg-blue-700 hover:bg-blue-800 text-white border-2 border-slate-900 py-3 text-xs font-black uppercase tracking-widest text-center"
      >
        Daftar Akun &rarr;
      </button>
    </form>

    <div class="mt-5 pt-4 border-t-2 border-slate-900 text-center text-xs text-slate-600 font-medium">
      Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-700 font-bold underline">Masuk di sini</a>
    </div>

  </div>
</div>
@endsection

