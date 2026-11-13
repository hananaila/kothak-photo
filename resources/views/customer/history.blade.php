@extends('layouts.app')

@section('title', 'Riwayat Transaksi & Antrean — KOTHaK Photo')

@section('content')
<div class="max-w-6xl mx-auto">
  <h1 class="text-xl sm:text-2xl font-extrabold mb-4">Riwayat Transaksi & Status Booking</h1>

  <!-- Mobile Cards (visible < md) -->
  <div class="space-y-3 md:hidden">
    @forelse($transactions as $trx)
    <div class="border-2 border-slate-900 bg-white p-4 shadow-[3px_3px_0px_#1D4ED8]">
      <div class="flex items-center justify-between mb-2">
        <span class="font-mono font-black text-blue-700 text-sm">{{ $trx['queue_number'] }}</span>
        <span class="text-[10px] font-bold uppercase px-2 py-0.5 border
          @if($trx['booking_status'] === 'confirmed') bg-emerald-100 text-emerald-800 border-emerald-400
          @elseif($trx['booking_status'] === 'pending') bg-amber-100 text-amber-800 border-amber-400
          @else bg-slate-100 text-slate-700 border-slate-400
          @endif">{{ $trx['booking_status'] }}</span>
      </div>
      <div class="text-xs space-y-1.5">
        <div class="flex justify-between gap-2">
          <span class="text-slate-500">Paket:</span>
          <strong class="text-right truncate">{{ $trx['package_name'] }}</strong>
        </div>
        <div class="flex justify-between gap-2">
          <span class="text-slate-500">Jadwal:</span>
          <span class="font-mono text-right">{{ $trx['schedule_date'] }} • {{ substr($trx['start_time'],0,5) }}-{{ substr($trx['end_time'],0,5) }}</span>
        </div>
        <div class="flex justify-between gap-2">
          <span class="text-slate-500">Harga:</span>
          <strong class="font-mono">Rp {{ number_format((float) $trx['total_price'],0,',','.') }}</strong>
        </div>
        <div class="flex justify-between gap-2 items-center">
          <span class="text-slate-500">Bukti:</span>
          @if($trx['proof_image_path'] && $trx['proof_image_path'] !== '-')
            <a href="{{ asset($trx['proof_image_path']) }}" target="_blank" class="underline text-blue-700 font-mono text-[11px]">Lihat</a>
          @else
            <span class="text-slate-400">-</span>
          @endif
        </div>
        <div class="flex justify-between gap-2">
          <span class="text-slate-500">Pembayaran:</span>
          <span class="font-bold">{{ $trx['payment_status'] }}</span>
        </div>
      </div>
    </div>
    @empty
    <div class="border-2 border-slate-900 bg-white p-6 text-center text-slate-500 font-bold text-sm">
      Belum ada transaksi.
    </div>
    @endforelse
  </div>

  <!-- Desktop Table (visible >= md) -->
  <div class="border-2 border-slate-900 bg-white shadow-[6px_6px_0px_#1D4ED8] overflow-x-auto hidden md:block">
    <table class="w-full text-left border-collapse text-xs">
      <thead>
        <tr class="bg-blue-700 text-white border-b-2 border-slate-900 uppercase font-extrabold">
          <th class="py-3 px-4">Antrean</th>
          <th class="py-3 px-4">Paket</th>
          <th class="py-3 px-4">Tanggal & Jam</th>
          <th class="py-3 px-4">Harga</th>
          <th class="py-3 px-4">Bukti</th>
          <th class="py-3 px-4">Pembayaran</th>
          <th class="py-3 px-4">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y-2 divide-slate-900">
        @forelse($transactions as $trx)
        <tr class="hover:bg-slate-50">
          <td class="py-3 px-4 font-mono font-black text-blue-700">{{ $trx['queue_number'] }}</td>
          <td class="py-3 px-4 font-bold">{{ $trx['package_name'] }}</td>
          <td class="py-3 px-4 font-mono">
            {{ $trx['schedule_date'] }} • {{ substr($trx['start_time'],0,5) }}-{{ substr($trx['end_time'],0,5) }}
          </td>
          <td class="py-3 px-4 font-mono font-bold">Rp {{ number_format((float) $trx['total_price'],0,',','.') }}</td>
          <td class="py-3 px-4 font-mono text-[11px]">
            @if($trx['proof_image_path'] && $trx['proof_image_path'] !== '-')
              <a href="{{ asset($trx['proof_image_path']) }}" target="_blank" class="underline text-blue-700 font-mono">Lihat Bukti</a>
            @else
              -
            @endif
          </td>
          <td class="py-3 px-4 font-mono font-bold">{{ $trx['payment_status'] }}</td>
          <td class="py-3 px-4 font-bold uppercase">{{ $trx['booking_status'] }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="p-6 text-center text-slate-500 font-bold">Belum ada transaksi.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
