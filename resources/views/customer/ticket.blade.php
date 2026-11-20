@extends('layouts.app')

@section('title', 'E-Ticket Antrean Studio — ' . $booking->queue_number)

@section('content')
<div class="max-w-xl mx-auto my-4 sm:my-8 space-y-4">

  <!-- Action Bar -->
  <div class="flex items-center justify-between no-print">
    <a href="{{ route('booking.history') }}" class="text-xs font-black text-blue-700 hover:underline flex items-center gap-1">
      &larr; Kembali ke Riwayat
    </a>
    <button onclick="window.print()" class="neo-btn bg-white hover:bg-slate-100 text-slate-900 px-4 py-2 text-xs font-black uppercase tracking-wider flex items-center gap-1.5">
      <span>🖨️</span> Cetak / Simpan PDF
    </button>
  </div>

  <!-- DIGITAL BOARDING PASS / E-TICKET -->
  <div class="border-4 border-slate-900 bg-white shadow-[8px_8px_0px_#1D4ED8] overflow-hidden">
    <!-- Header Ticket -->
    <div class="bg-blue-700 text-white p-5 border-b-4 border-slate-900 relative">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <img src="{{ asset('assets/img/logo kothak cafe.jpg') }}" alt="Logo" class="w-8 h-8 object-cover border border-white" />
          <span class="font-black text-sm uppercase tracking-wider">KOTHaK Photo Studio</span>
        </div>
        <span class="text-[10px] font-mono font-bold bg-white text-blue-900 px-2 py-0.5 border border-slate-900 uppercase">
          E-TICKET RESMI
        </span>
      </div>
      <div class="mt-4 text-center">
        <div class="text-[11px] font-bold text-blue-200 uppercase tracking-widest">NOMOR ANTREAN STUDIO</div>
        <div class="text-4xl sm:text-5xl font-black font-mono tracking-tight text-white mt-1">
          {{ $booking->queue_number }}
        </div>
        <div class="inline-block mt-2 px-3 py-0.5 text-xs font-black uppercase rounded {{ $booking->status_badge_class }}">
          Status: {{ $booking->status_label }}
        </div>
      </div>
    </div>

    <!-- Ticket Body -->
    <div class="p-6 space-y-4 text-xs">
      <div class="grid grid-cols-2 gap-4 pb-4 border-b-2 border-slate-900">
        <div>
          <div class="text-[10px] font-black uppercase text-slate-500">NAMA PELANGGAN</div>
          <div class="font-extrabold text-sm text-slate-900 mt-0.5">{{ $booking->user->name }}</div>
          <div class="font-mono text-[11px] text-slate-600">{{ $booking->user->phone_whatsapp }}</div>
        </div>
        <div>
          <div class="text-[10px] font-black uppercase text-slate-500">PAKET FOTO</div>
          <div class="font-extrabold text-sm text-blue-700 mt-0.5">{{ $booking->package->name }}</div>
          <div class="text-[11px] text-slate-600">{{ $booking->package->duration }} Menit Sesi Foto</div>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4 pb-4 border-b-2 border-slate-900">
        <div>
          <div class="text-[10px] font-black uppercase text-slate-500">TANGGAL SESI</div>
          <div class="font-mono font-black text-slate-900 mt-0.5 text-sm">
            {{ \Carbon\Carbon::parse($booking->schedule->date)->translatedFormat('d F Y') }}
          </div>
        </div>
        <div>
          <div class="text-[10px] font-black uppercase text-slate-500">JAM SESI</div>
          <div class="font-mono font-black text-blue-700 mt-0.5 text-sm">
            {{ $booking->schedule->formatted_time }}
          </div>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4 pb-4 border-b-2 border-slate-900">
        <div>
          <div class="text-[10px] font-black uppercase text-slate-500">TEMA BACKGROUND</div>
          <div class="font-extrabold text-slate-900 mt-0.5">{{ $booking->studio_backdrop }}</div>
        </div>
        <div>
          <div class="text-[10px] font-black uppercase text-slate-500">STATUS PEMBAYARAN</div>
          <div class="font-bold text-slate-900 mt-0.5">
            {{ $booking->payment ? $booking->payment->status_label : 'Menunggu Bukti Transfer' }}
          </div>
        </div>
      </div>

      <!-- Add-ons if any -->
      @if($booking->bookingAddOns->isNotEmpty())
      <div class="pb-4 border-b-2 border-slate-900">
        <div class="text-[10px] font-black uppercase text-slate-500 mb-1">LAYANAN TAMBAHAN</div>
        @foreach($booking->bookingAddOns as $bao)
        <div class="flex justify-between text-slate-700 font-medium">
          <span>• {{ $bao->addOn->name }}</span>
          <span class="font-mono font-bold">+ Rp {{ number_format($bao->subtotal, 0, ',', '.') }}</span>
        </div>
        @endforeach
      </div>
      @endif

      <!-- Simulated Barcode / QR Section -->
      <div class="pt-2 text-center bg-[#F5F4EE] border-2 border-slate-900 p-4 space-y-2">
        <div class="font-mono font-black text-sm tracking-widest text-slate-900">
          * {{ $booking->queue_number }} - {{ $booking->id }} *
        </div>
        <!-- Barcode lines simulation -->
        <div class="h-12 w-64 mx-auto flex items-center justify-center gap-1">
          @for($i = 0; $i < 36; $i++)
            <div class="h-full {{ $i % 3 == 0 ? 'w-1 bg-slate-900' : ($i % 2 == 0 ? 'w-0.5 bg-slate-800' : 'w-1.5 bg-slate-900') }}"></div>
          @endfor
        </div>
        <p class="text-[10px] text-slate-500">Tunjukkan tiket digital ini kepada kasir saat tiba di KOTHaK Photo Studio untuk check-in.</p>
      </div>

      <div class="text-[11px] text-slate-600 bg-blue-50 border border-blue-200 p-2.5 space-y-1">
        <div class="font-bold text-blue-900">⚠️ Ketentuan Kunjungan:</div>
        <div>1. Harap hadir 10 menit sebelum slot waktu sesi dimulai.</div>
        <div>2. Check-in dilakukan di kasir dengan menyebutkan nomor antrean.</div>
      </div>
    </div>
  </div>

</div>

@push('styles')
<style>
  @media print {
    body { background: white !important; }
    header, footer, .no-print { display: none !important; }
  }
</style>
@endpush
@endsection
