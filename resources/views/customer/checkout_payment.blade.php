@extends('layouts.app')

@section('title', 'Checkout & Pembayaran QRIS — KOTHaK Photo')

@section('content')
<div class="max-w-4xl mx-auto">
  <div class="border-2 border-slate-900 bg-white p-4 sm:p-6 shadow-[4px_4px_0px_#1D4ED8] sm:shadow-[6px_6px_0px_#1D4ED8]">

    <!-- HEADER -->
    <div class="border-b-2 border-slate-900 pb-4 mb-5 sm:mb-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <span class="text-[10px] sm:text-xs font-black uppercase tracking-widest text-blue-700">LANGKAH 3 DARI 3 • CHECKOUT</span>
          <h1 class="text-xl sm:text-2xl md:text-3xl font-black mt-1 text-slate-900">Konfirmasi Pembayaran QRIS</h1>
        </div>
        <div class="bg-blue-700 text-white font-mono font-black text-xs sm:text-sm px-3 sm:px-4 py-1.5 border-2 border-slate-900 shadow-[2px_2px_0px_#0F172A] shrink-0 text-center">
          NO. ANTREAN: {{ $booking->queue_number }}
        </div>
      </div>
    </div>

    <!-- MAIN TWO-COLUMN LAYOUT -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6 items-start">

      <!-- LEFT: ORDER DETAILS -->
      <div class="bg-[#F5F4EE] border-2 border-slate-900 p-4 sm:p-5 space-y-2.5 sm:space-y-3 text-xs shadow-[3px_3px_0px_#0F172A]">
        <div class="font-black uppercase tracking-wider text-blue-700 border-b-2 border-slate-900 pb-2 flex items-center justify-between gap-2">
          <span>RINCIAN PESANAN</span>
          <span class="font-mono text-[10px] sm:text-[11px] text-slate-600">ID: #{{ $booking->id }}</span>
        </div>

        <div class="flex justify-between py-1 border-b border-slate-200 gap-2">
          <span class="text-slate-600 font-bold shrink-0">Pelanggan:</span>
          <strong class="text-slate-900 text-right truncate">{{ $booking->user->name }}</strong>
        </div>

        <div class="flex justify-between py-1 border-b border-slate-200 gap-2">
          <span class="text-slate-600 font-bold shrink-0">Paket:</span>
          <strong class="text-slate-900 text-right truncate">{{ $booking->package->name }}</strong>
        </div>

        <div class="flex justify-between py-1 border-b border-slate-200 gap-2">
          <span class="text-slate-600 font-bold shrink-0">Tanggal:</span>
          <strong class="font-mono text-right">{{ \Carbon\Carbon::parse($booking->schedule->date)->translatedFormat('d F Y') }}</strong>
        </div>

        <div class="flex justify-between py-1 border-b border-slate-200 gap-2">
          <span class="text-slate-600 font-bold shrink-0">Jam:</span>
          <strong class="font-mono text-blue-700 text-right">{{ substr($booking->schedule->start_time,0,5) }} - {{ substr($booking->schedule->end_time,0,5) }} WIB</strong>
        </div>

        <div class="flex justify-between py-1 border-b border-slate-200 gap-2">
          <span class="text-slate-600 font-bold shrink-0">Background:</span>
          <strong class="text-right truncate">{{ $booking->studio_backdrop }}</strong>
        </div>

        <div class="border-t-2 border-slate-900 pt-3 flex justify-between items-baseline gap-2">
          <span class="font-black uppercase shrink-0">Total:</span>
          <span class="text-lg sm:text-xl font-black font-mono text-blue-700">Rp {{ number_format($booking->package->price,0,',','.') }}</span>
        </div>

        <div class="bg-blue-50 border border-blue-200 p-2 sm:p-2.5 text-[10px] sm:text-[11px] text-blue-900 font-medium">
          💡 <strong>Petunjuk:</strong> Pindai QRIS di samping, lalu unggah bukti transfer.
        </div>
      </div>

      <!-- RIGHT: QRIS CODE & PROOF UPLOAD -->
      <div class="space-y-4">
        <!-- QRIS DISPLAY BOX -->
        <div class="border-2 border-slate-900 p-4 sm:p-5 text-center bg-white shadow-[3px_3px_0px_#0F172A]">
          <div class="text-[10px] sm:text-xs font-black uppercase tracking-wider text-slate-900">KOTHaK CAFE &amp; PHOTO</div>
          <div class="text-[10px] sm:text-[11px] font-mono text-blue-700 font-bold mt-0.5">NMID: ID2026KOTHAKPBG01</div>

          <div class="my-3 flex justify-center">
            <img src="{{ asset('assets/img/qris-kothak.png') }}" alt="QRIS Statis KOTHaK Photo" class="w-36 h-36 sm:w-48 sm:h-48 object-contain border-2 border-slate-900 p-2 bg-white" />
          </div>

          <div class="text-[10px] sm:text-[11px] text-slate-600 font-bold">
            Scan QRIS untuk nominal <strong class="text-blue-700">Rp {{ number_format($booking->package->price,0,',','.') }}</strong>
          </div>
        </div>

        <!-- UPLOAD FORM -->
        <form action="{{ route('payment.upload', $booking->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3 bg-[#F5F4EE] border-2 border-slate-900 p-3 sm:p-4 shadow-[3px_3px_0px_#0F172A]">
          @csrf
          <div>
            <label class="block text-[10px] sm:text-xs font-black uppercase tracking-wider text-slate-900 mb-1.5">
              Upload Bukti Transfer (JPG/PNG/WEBP) *
            </label>
            <input type="file" name="proof_image" accept="image/jpeg,image/png,image/webp" required class="block w-full text-[11px] sm:text-xs border-2 border-slate-900 bg-white p-1.5 sm:p-2 file:mr-2 file:py-1 file:px-2 sm:file:px-3 file:border-2 file:border-slate-900 file:font-black file:uppercase file:bg-blue-100 file:text-blue-900 file:text-[10px] sm:file:text-xs cursor-pointer" />
            <div class="text-[10px] text-slate-500 mt-1 font-medium">Maks: 5 MB</div>
          </div>

          <button type="submit" class="w-full neo-btn bg-blue-700 hover:bg-blue-800 text-white border-2 border-slate-900 py-2.5 sm:py-3 px-4 text-[11px] sm:text-xs font-black uppercase tracking-widest shadow-[4px_4px_0px_#0F172A]">
            Kirim Bukti Pembayaran →
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
