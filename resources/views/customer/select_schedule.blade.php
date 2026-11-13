@extends('layouts.app')

@section('title', 'Pilih Jadwal Slot — KOTHaK Photo')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

  <div class="border-2 border-slate-900 bg-white p-6 shadow-[6px_6px_0px_#1D4ED8]">
    <!-- HEADER -->
    <div class="border-b-2 border-slate-900 pb-4 mb-6 flex flex-wrap items-center justify-between gap-4">
      <div>
        <span class="text-xs font-black uppercase tracking-widest text-blue-700">LANGKAH 2 DARI 3 &bull; PEMILIHAN JADWAL STUDIO</span>
        <h1 class="text-2xl md:text-3xl font-black mt-1 text-slate-900">{{ $package->name }}</h1>
        <p class="text-xs text-slate-600 mt-1">Durasi sesi: <strong>{{ $package->duration }} Menit</strong> &bull; {{ $package->description }}</p>
      </div>
      <div class="text-right">
        <span class="text-xs font-bold text-slate-500 uppercase">Total Biaya:</span>
        <div class="font-mono font-black text-2xl text-blue-700">
          Rp {{ number_format($package->price, 0, ',', '.') }}
        </div>
      </div>
    </div>

    <!-- DATE SELECTOR FORM -->
    <div class="bg-[#F5F4EE] border-2 border-slate-900 p-4 mb-6">
      <form method="GET" action="{{ route('schedule.select', $package->id) }}" class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <label for="date" class="text-xs font-black uppercase tracking-wider text-slate-800">
            Pilih Tanggal Sesi:
          </label>
          <input
            type="date"
            id="date"
            name="date"
            value="{{ $selectedDate }}"
            onchange="this.form.submit()"
            min="{{ date('Y-m-d') }}"
            class="border-2 border-slate-900 px-3 py-1.5 text-xs font-bold font-mono bg-white focus:outline-none focus:ring-2 focus:ring-blue-600"
          />
        </div>
        <span class="text-[11px] font-bold text-slate-500">
          Ganti tanggal untuk melihat ketersediaan slot hari lain
        </span>
      </form>
    </div>

    <!-- BOOKING FORM -->
    <form action="{{ route('booking.store') }}" method="POST" class="space-y-6">
      @csrf
      <input type="hidden" name="package_id" value="{{ $package->id }}" />

      <!-- SLOT PICKER -->
      <div>
        <label class="block text-xs font-black uppercase tracking-wider mb-2.5 text-slate-900">
          Pilih Slot Waktu Tersedia ({{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}):
        </label>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          @forelse($schedules as $slot)
            @php
              $rem = max(0, $slot->capacity - $slot->total_booked);
              $isAvail = $rem > 0;
            @endphp
            <label class="border-2 border-slate-900 p-3.5 block relative transition-all {{ $isAvail ? 'bg-white hover:bg-blue-50 cursor-pointer shadow-[3px_3px_0px_#0F172A]' : 'bg-slate-200 opacity-60 cursor-not-allowed' }}">
              <div class="flex items-start justify-between">
                <input
                  type="radio"
                  name="schedule_id"
                  value="{{ $slot->id }}"
                  {{ $isAvail ? 'required' : 'disabled' }}
                  class="mt-0.5 accent-blue-700"
                />
                <span class="text-[10px] font-mono font-black uppercase px-1.5 py-0.5 {{ $isAvail ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-rose-100 text-rose-800 border border-rose-300' }}">
                  {{ $isAvail ? "{$rem}/{$slot->capacity} Slot" : 'Penuh' }}
                </span>
              </div>
              <div class="font-mono font-black text-sm text-slate-900 mt-2">
                {{ substr($slot->start_time, 0, 5) }} - {{ substr($slot->end_time, 0, 5) }} WIB
              </div>
              <div class="text-[10px] font-bold uppercase mt-1 {{ $isAvail ? 'text-emerald-700' : 'text-rose-600' }}">
                {{ $isAvail ? 'Tersedia untuk Booking' : 'Slot Waktu Penuh' }}
              </div>
            </label>
          @empty
            <div class="col-span-3 text-center py-6 text-slate-500 font-bold">
              Tidak ada jadwal slot untuk tanggal ini.
            </div>
          @endforelse
        </div>
      </div>

      <!-- BACKDROP SELECTION -->
      <div>
        <label for="studio_backdrop" class="block text-xs font-black uppercase tracking-wider mb-2 text-slate-900">
          Pilihan Studio Background:
        </label>
        <select
          name="studio_backdrop"
          id="studio_backdrop"
          class="w-full border-2 border-slate-900 bg-white p-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 shadow-[3px_3px_0px_#0F172A]"
        >
          <option value="Electric Blue Backdrop">Electric Blue Backdrop (Signature KOTHaK)</option>
          <option value="Warm Cream Minimalist">Warm Cream Minimalist (Aesthetic Korean Style)</option>
          <option value="Classic Grey Studio">Classic Neutral Grey Studio</option>
          <option value="Butter Yellow Pop">Butter Yellow Pop Vibes</option>
        </select>
      </div>

      <!-- CUSTOMER CONTACT -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-[#F5F4EE] border-2 border-slate-900 p-4">
        <div>
          <label class="block text-xs font-black uppercase tracking-wider mb-1.5 text-slate-900">
            Nama Lengkap Pelanggan:
          </label>
          <input
            type="text"
            name="name"
            value="{{ Auth::check() ? Auth::user()->name : 'Hana Naila Rahmadina' }}"
            required
            class="w-full border-2 border-slate-900 bg-white p-2 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-blue-600"
          />
        </div>
        <div>
          <label class="block text-xs font-black uppercase tracking-wider mb-1.5 text-slate-900">
            Nomor WhatsApp (Untuk Notifikasi Antrean):
          </label>
          <input
            type="text"
            name="phone_whatsapp"
            value="{{ Auth::check() ? Auth::user()->phone_whatsapp : '081234567890' }}"
            required
            class="w-full border-2 border-slate-900 bg-white p-2 text-xs font-bold font-mono focus:outline-none focus:ring-2 focus:ring-blue-600"
          />
        </div>
      </div>

      <!-- SUBMIT BUTTON -->
      <button
        type="submit"
        class="w-full neo-btn bg-blue-700 hover:bg-blue-800 text-white border-2 border-slate-900 py-3.5 px-6 text-xs font-black uppercase tracking-widest text-center"
      >
        Konfirmasi Jadwal &amp; Lanjut ke Pembayaran QRIS &rarr;
      </button>
    </form>
  </div>

</div>
@endsection

