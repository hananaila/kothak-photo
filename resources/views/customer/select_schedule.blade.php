@extends('layouts.app')

@section('title', 'Pilih Jadwal Slot — KOTHaK Photo')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

  <div class="border-2 border-slate-900 bg-white p-5 sm:p-7 shadow-[6px_6px_0px_#1D4ED8]">
    <!-- HEADER -->
    <div class="border-b-2 border-slate-900 pb-5 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <span class="text-xs font-black uppercase tracking-widest text-blue-700">LANGKAH 2 DARI 3 &bull; PEMILIHAN JADWAL & SLOT</span>
        <h1 class="text-2xl sm:text-3xl font-black mt-1 text-slate-900">{{ $package->name }}</h1>
        <p class="text-xs text-slate-600 mt-1">
          Durasi sesi: <strong>{{ $package->duration }} Menit</strong> &bull; Kapasitas: <strong>1 - {{ $package->max_participants }} Orang</strong>
        </p>
      </div>
      <div class="sm:text-right bg-[#F5F4EE] border-2 border-slate-900 p-3 shadow-[2px_2px_0px_#0F172A] shrink-0">
        <span class="text-[10px] font-bold text-slate-500 uppercase">Harga Paket:</span>
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
        <span class="text-[11px] font-bold text-slate-600">
          📅 {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}
        </span>
      </form>
    </div>

    <!-- BOOKING FORM -->
    <form action="{{ route('booking.store') }}" method="POST" class="space-y-6" id="bookingForm">
      @csrf
      <input type="hidden" name="package_id" value="{{ $package->id }}" />
      <input type="hidden" id="basePackagePrice" value="{{ $package->price }}" />

      <!-- SLOT PICKER (FR-02 & Anti-Double Booking constraint) -->
      <div>
        <div class="flex items-center justify-between mb-2.5">
          <label class="block text-xs font-black uppercase tracking-wider text-slate-900">
            1. Pilih Slot Waktu Tersedia (Anti-Double Booking):
          </label>
          <span class="text-[10px] text-slate-500 font-mono">Kapasitas Maksimal: 3 Booking / Slot</span>
        </div>

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
                  class="mt-0.5 accent-blue-700 w-4 h-4"
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
            <div class="col-span-3 text-center py-6 text-slate-500 font-bold bg-[#F5F4EE] border-2 border-slate-900">
              Tidak ada jadwal slot untuk tanggal ini. Silakan ganti tanggal lain.
            </div>
          @endforelse
        </div>
      </div>

      <!-- BACKDROP & PARTICIPANTS -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="studio_backdrop" class="block text-xs font-black uppercase tracking-wider mb-2 text-slate-900">
            2. Pilihan Studio Background:
          </label>
          <select
            name="studio_backdrop"
            id="studio_backdrop"
            class="w-full border-2 border-slate-900 bg-white p-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 shadow-[2px_2px_0px_#0F172A]"
          >
            <option value="Electric Blue Backdrop">Electric Blue Backdrop (Signature KOTHaK)</option>
            <option value="Warm Cream Minimalist">Warm Cream Minimalist (Aesthetic Korean Style)</option>
            <option value="Classic Neutral Grey">Classic Neutral Grey Studio</option>
            <option value="Butter Yellow Pop">Butter Yellow Pop Vibes</option>
          </select>
        </div>

        <div>
          <label for="participants_count" class="block text-xs font-black uppercase tracking-wider mb-2 text-slate-900">
            3. Jumlah Peserta Foto:
          </label>
          <input
            type="number"
            id="participants_count"
            name="participants_count"
            value="{{ $package->max_participants }}"
            min="1"
            max="12"
            class="w-full border-2 border-slate-900 bg-white p-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 shadow-[2px_2px_0px_#0F172A]"
          />
        </div>
      </div>

      <!-- ADD-ONS SELECTION (FR-03) -->
      @if($addOns->isNotEmpty())
      <div class="border-2 border-slate-900 p-4 bg-[#F5F4EE]">
        <label class="block text-xs font-black uppercase tracking-wider mb-2 text-slate-900">
          4. Tambah Layanan Ekstra (Add-Ons):
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          @foreach($addOns as $ao)
          <label class="flex items-start gap-2.5 bg-white border border-slate-900 p-3 cursor-pointer hover:bg-blue-50">
            <input
              type="checkbox"
              name="add_ons[]"
              value="{{ $ao->id }}"
              data-price="{{ $ao->price }}"
              onchange="recalculateTotal()"
              class="addon-checkbox mt-1 accent-blue-700 w-4 h-4"
            />
            <div class="text-xs">
              <div class="font-black text-slate-900">{{ $ao->name }}</div>
              <div class="text-[11px] text-slate-600">{{ $ao->description }}</div>
              <div class="font-mono font-bold text-blue-700 mt-0.5">+ Rp {{ number_format($ao->price, 0, ',', '.') }}</div>
            </div>
          </label>
          @endforeach
        </div>
      </div>
      @endif

      <!-- CUSTOMER CONTACT -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-[#F5F4EE] border-2 border-slate-900 p-4">
        <div>
          <label class="block text-xs font-black uppercase tracking-wider mb-1.5 text-slate-900">
            Nama Pemesan:
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
            Nomor WhatsApp (Untuk Tiket & Pengingat Antrean):
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

      <!-- TOTAL PRICE DISPLAY & SUBMIT -->
      <div class="border-2 border-slate-900 bg-white p-4 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-[4px_4px_0px_#0F172A]">
        <div>
          <div class="text-[10px] font-bold uppercase text-slate-500">Estimasi Total Pembayaran:</div>
          <div class="text-2xl font-black font-mono text-blue-700" id="liveTotalDisplay">
            Rp {{ number_format($package->price, 0, ',', '.') }}
          </div>
        </div>

        <button
          type="submit"
          class="w-full sm:w-auto neo-btn bg-blue-700 hover:bg-blue-800 text-white py-3.5 px-8 text-xs font-black uppercase tracking-widest text-center"
        >
          Konfirmasi Jadwal &amp; Lanjut QRIS &rarr;
        </button>
      </div>
    </form>
  </div>

</div>

@push('scripts')
<script>
  function recalculateTotal() {
    const basePrice = parseFloat(document.getElementById('basePackagePrice').value) || 0;
    const checkboxes = document.querySelectorAll('.addon-checkbox:checked');
    let addonsTotal = 0;

    checkboxes.forEach(cb => {
      addonsTotal += parseFloat(cb.dataset.price) || 0;
    });

    const grandTotal = basePrice + addonsTotal;
    document.getElementById('liveTotalDisplay').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
  }
</script>
@endpush
@endsection
