@extends('layouts.app')

@section('title', 'Dasbor Kasir — KOTHaK Photo')

@section('content')
<div class="space-y-6 sm:space-y-8 max-w-7xl mx-auto">

  <!-- CASHIER HEADER -->
  <div class="border-2 border-slate-900 bg-white p-4 sm:p-6 shadow-[4px_4px_0px_#1D4ED8] sm:shadow-[6px_6px_0px_#1D4ED8]">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 sm:gap-6">
      <div>
        <div class="inline-flex items-center gap-2 bg-emerald-100 border border-emerald-400 px-2.5 sm:px-3 py-1 text-[10px] sm:text-xs font-bold text-emerald-900 uppercase tracking-widest mb-2">
          <span>MODUL KASIR & OPERATOR</span>
        </div>
        <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-slate-900">
          Dasbor Verifikasi & Antrean
        </h1>
        <p class="text-[11px] sm:text-xs text-slate-600 mt-1">
          Kelola bukti pembayaran, check‑in pelanggan, dan giliran sesi studio.
        </p>
      </div>
      <!-- CALL NEXT BUTTON -->
      <form action="{{ route('cashier.call-next') }}" method="POST">
        @csrf
        <button type="submit" class="neo-btn bg-emerald-500 hover:bg-emerald-600 text-slate-900 border-2 border-slate-900 py-3 sm:py-3.5 px-4 sm:px-6 font-black uppercase tracking-wider text-[11px] sm:text-xs flex items-center gap-2 w-full sm:w-auto justify-center">
          <span class="text-lg">📢</span>
          <span>Panggil Berikutnya</span>
        </button>
      </form>
    </div>

    <!-- CURRENT STUDIO STATUS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mt-5 sm:mt-6 pt-5 sm:pt-6 border-t-2 border-slate-900">
      <div class="border-2 border-slate-900 bg-[#F5F4EE] p-3 text-center shadow-[3px_3px_0px_#0F172A]">
        <div class="text-[9px] sm:text-[10px] font-black uppercase text-slate-500 tracking-wider">SEDANG DI STUDIO</div>
        <div class="text-xl sm:text-2xl font-black font-mono text-emerald-600 mt-1">
          {{ $inStudio?->queue_number ?? 'KOSONG' }}
        </div>
        <div class="text-[10px] sm:text-[11px] font-bold text-slate-700 truncate mt-0.5">
          {{ $inStudio?->user?->name ?? 'Studio Siap' }}
        </div>
      </div>
      <div class="border-2 border-slate-900 bg-blue-50 p-3 text-center shadow-[3px_3px_0px_#1D4ED8]">
        <div class="text-[9px] sm:text-[10px] font-black uppercase text-blue-800 tracking-wider">BERIKUTNYA</div>
        <div class="text-xl sm:text-2xl font-black font-mono text-blue-700 mt-1">
          {{ $nextQueue?->queue_number ?? 'TIDAK ADA' }}
        </div>
        <div class="text-[10px] sm:text-[11px] font-bold text-slate-700 truncate mt-0.5">
          {{ $nextQueue?->user?->name ?? 'Semua terlayani' }}
        </div>
      </div>
      <div class="border-2 border-slate-900 bg-amber-50 p-3 text-center shadow-[3px_3px_0px_#D97706]">
        <div class="text-[9px] sm:text-[10px] font-black uppercase text-amber-800 tracking-wider">RESERVASI HARI INI</div>
        <div class="text-xl sm:text-2xl font-black font-mono text-amber-700 mt-1">
          {{ $todayBookings->count() }}
        </div>
        <div class="text-[10px] sm:text-[11px] font-bold text-slate-700 mt-0.5">
          {{ \Carbon\Carbon::parse($today)->translatedFormat('d F Y') }}
        </div>
      </div>
    </div>
  </div>

  <!-- SECTION 1: VERIFIKASI PEMBAYARAN -->
  <section class="border-2 border-slate-900 bg-white shadow-[4px_4px_0px_#1D4ED8] sm:shadow-[6px_6px_0px_#1D4ED8]">
    <div class="bg-slate-900 text-white px-4 sm:px-5 py-3 font-black uppercase text-[11px] sm:text-xs tracking-wider flex items-center justify-between gap-2">
      <span>1. Verifikasi Pembayaran QRIS</span>
      <span class="font-mono text-slate-400 text-[10px] sm:text-[11px]">{{ $payments->count() }} Transaksi</span>
    </div>

    <!-- Mobile Cards (< md) -->
    <div class="p-3 space-y-3 md:hidden">
      @forelse($payments as $pay)
      <div class="border-2 border-slate-900 bg-[#F5F4EE] p-3 text-xs space-y-2">
        <div class="flex items-center justify-between">
          <span class="font-mono font-black text-blue-700 text-sm">{{ $pay->booking?->queue_number ?? '-' }}</span>
          @if($pay->payment_status === 'verified')
            <span class="bg-emerald-100 text-emerald-800 border border-emerald-400 px-2 py-0.5 text-[10px] font-black uppercase">Lunas</span>
          @elseif($pay->payment_status === 'pending')
            <span class="bg-amber-100 text-amber-900 border border-amber-400 px-2 py-0.5 text-[10px] font-black uppercase">Menunggu</span>
          @else
            <span class="bg-rose-100 text-rose-800 border border-rose-400 px-2 py-0.5 text-[10px] font-black uppercase">Ditolak</span>
          @endif
        </div>
        <div class="flex justify-between gap-2">
          <span class="text-slate-500">Pelanggan:</span>
          <strong class="truncate">{{ $pay->booking?->user?->name ?? '-' }}</strong>
        </div>
        @if($pay->proof_image_path)
        <div class="flex justify-between gap-2">
          <span class="text-slate-500">Bukti:</span>
          <a href="{{ asset($pay->proof_image_path) }}" target="_blank" class="text-blue-700 underline font-mono text-[11px]">📷 Lihat</a>
        </div>
        @endif
        @if($pay->payment_status === 'pending' && $pay->booking)
        <form action="{{ route('cashier.verify', $pay->booking->id) }}" method="POST" class="flex gap-2 pt-1">
          @csrf
          <button type="submit" name="decision" value="accept" class="flex-1 bg-emerald-500 hover:bg-emerald-600 border border-slate-900 px-3 py-2 text-[10px] font-black uppercase text-slate-900 shadow-[2px_2px_0px_#0F172A] text-center">Terima</button>
          <button type="submit" name="decision" value="reject" class="flex-1 bg-rose-600 hover:bg-rose-700 text-white border border-slate-900 px-3 py-2 text-[10px] font-black uppercase shadow-[2px_2px_0px_#0F172A] text-center">Tolak</button>
        </form>
        @endif
      </div>
      @empty
      <div class="text-center text-slate-500 font-bold py-6">Belum ada transaksi.</div>
      @endforelse
    </div>

    <!-- Desktop Table (>= md) -->
    <div class="overflow-x-auto hidden md:block">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-[#F5F4EE] border-b-2 border-slate-900 uppercase font-black text-slate-700">
            <th class="p-3">Antrean</th>
            <th class="p-3">Pelanggan</th>
            <th class="p-3">Bukti</th>
            <th class="p-3">Status</th>
            <th class="p-3 text-right">Keputusan</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-slate-900">
          @forelse($payments as $pay)
          <tr class="hover:bg-slate-50">
            <td class="p-3 font-mono font-black text-blue-700 text-sm">{{ $pay->booking?->queue_number ?? '-' }}</td>
            <td class="p-3">
              <div class="font-bold text-slate-900">{{ $pay->booking?->user?->name ?? '-' }}</div>
              <div class="text-[11px] font-mono text-slate-500">{{ $pay->booking?->user?->phone_whatsapp ?? '-' }}</div>
            </td>
            <td class="p-3">
              @if($pay->proof_image_path)
                <a href="{{ asset($pay->proof_image_path) }}" target="_blank" class="inline-flex items-center gap-1 font-mono text-blue-700 underline font-bold">
                  <span>📷</span> Lihat
                </a>
              @else
                -
              @endif
            </td>
            <td class="p-3">
              @if($pay->payment_status === 'verified')
                <span class="bg-emerald-100 text-emerald-800 border border-emerald-400 px-2 py-0.5 text-[10px] font-black uppercase">Lunas</span>
              @elseif($pay->payment_status === 'pending')
                <span class="bg-amber-100 text-amber-900 border border-amber-400 px-2 py-0.5 text-[10px] font-black uppercase">Menunggu</span>
              @else
                <span class="bg-rose-100 text-rose-800 border border-rose-400 px-2 py-0.5 text-[10px] font-black uppercase">Ditolak</span>
              @endif
            </td>
            <td class="p-3 text-right">
              @if($pay->payment_status === 'pending' && $pay->booking)
                <form action="{{ route('cashier.verify', $pay->booking->id) }}" method="POST" class="inline-flex gap-1.5">
                  @csrf
                  <button type="submit" name="decision" value="accept" class="bg-emerald-500 hover:bg-emerald-600 border border-slate-900 px-3 py-1 text-[11px] font-black uppercase text-slate-900 shadow-[2px_2px_0px_#0F172A]">Terima</button>
                  <button type="submit" name="decision" value="reject" class="bg-rose-600 hover:bg-rose-700 text-white border border-slate-900 px-3 py-1 text-[11px] font-black uppercase shadow-[2px_2px_0px_#0F172A]">Tolak</button>
                </form>
              @else
                <span class="text-slate-400 text-[11px] font-bold">Sudah Diproses</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="p-6 text-center text-slate-500 font-bold">Belum ada transaksi.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <!-- SECTION 2: CHECK‑IN PELANGGAN -->
  <section class="border-2 border-slate-900 bg-white shadow-[4px_4px_0px_#0F172A] sm:shadow-[6px_6px_0px_#0F172A]">
    <div class="bg-blue-700 text-white px-4 sm:px-5 py-3 font-black uppercase text-[11px] sm:text-xs tracking-wider flex items-center justify-between gap-2">
      <span>2. Check‑in Pelanggan</span>
      <span class="font-mono text-blue-200 text-[10px] sm:text-[11px]">{{ $today }}</span>
    </div>

    <!-- Mobile Cards (< md) -->
    <div class="p-3 space-y-3 md:hidden">
      @forelse($todayBookings as $tb)
      <div class="border-2 border-slate-900 bg-[#F5F4EE] p-3 text-xs space-y-2">
        <div class="flex items-center justify-between">
          <span class="font-mono font-black text-blue-700 text-sm">{{ $tb->queue_number }}</span>
          <span class="font-bold uppercase text-[10px]">{{ $tb->booking_status ?? $tb->status }}</span>
        </div>
        <div class="flex justify-between gap-2">
          <span class="text-slate-500">Pelanggan:</span>
          <strong class="truncate">{{ $tb->user?->name ?? '-' }}</strong>
        </div>
        <div class="flex justify-between gap-2">
          <span class="text-slate-500">Jadwal:</span>
          <span class="font-mono">{{ substr($tb->schedule?->start_time ?? '',0,5) }} - {{ substr($tb->schedule?->end_time ?? '',0,5) }}</span>
        </div>
        <form action="{{ route('cashier.checkin', $tb->id) }}" method="POST" class="pt-1">
          @csrf
          <button type="submit" class="w-full bg-blue-700 text-white border border-slate-900 px-3 py-2 font-black uppercase text-[10px] shadow-[2px_2px_0px_#0F172A]">Check‑in</button>
        </form>
      </div>
      @empty
      <div class="text-center text-slate-500 font-bold py-6">Tidak ada antrean hari ini.</div>
      @endforelse
    </div>

    <!-- Desktop Table (>= md) -->
    <div class="overflow-x-auto hidden md:block">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-[#F5F4EE] border-b-2 border-slate-900 uppercase font-black text-slate-700">
            <th class="p-3">Antrean</th>
            <th class="p-3">Pelanggan</th>
            <th class="p-3">Slot Waktu</th>
            <th class="p-3">Status</th>
            <th class="p-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-slate-900">
          @forelse($todayBookings as $tb)
          <tr class="hover:bg-slate-50">
            <td class="p-3 font-mono font-black text-blue-700 text-sm">{{ $tb->queue_number }}</td>
            <td class="p-3">
              <div class="font-bold text-slate-900">{{ $tb->user?->name ?? '-' }}</div>
              <div class="text-[11px] font-mono text-slate-500">{{ $tb->user?->phone_whatsapp ?? '-' }}</div>
            </td>
            <td class="p-3 font-mono text-slate-700">{{ substr($tb->schedule?->start_time ?? '',0,5) }} - {{ substr($tb->schedule?->end_time ?? '',0,5) }} WIB</td>
            <td class="p-3 font-bold">{{ $tb->booking_status ?? $tb->status }}</td>
            <td class="p-3 text-right">
              <form action="{{ route('cashier.checkin', $tb->id) }}" method="POST" class="inline-flex">
                @csrf
                <button type="submit" class="bg-blue-700 text-white border border-slate-900 px-3 py-1 font-black uppercase text-[11px] shadow-[2px_2px_0px_#0F172A]">Check‑in</button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="p-6 text-center text-slate-500 font-bold">Tidak ada antrean hari ini.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

</div>
@endsection
