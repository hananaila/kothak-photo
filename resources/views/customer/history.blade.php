@extends('layouts.app')

@section('title', 'Riwayat Kunjungan & Tiket Antrean — KOTHaK Photo')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-2 border-slate-900 pb-4">
    <div>
      <h1 class="text-2xl sm:text-3xl font-black text-slate-900">Riwayat Reservasi & Antrean Saya</h1>
      <p class="text-xs text-slate-600 mt-1">Pantau status verifikasi kasir, tiket digital, dan antrean studio photobox kamu.</p>
    </div>
    <a href="{{ route('packages.index') }}" class="neo-btn bg-blue-700 text-white px-4 py-2 text-xs font-black uppercase tracking-wider shrink-0 text-center">
      + Reservasi Baru
    </a>
  </div>

  <!-- Filter Status Tabs -->
  <div class="flex flex-wrap gap-2 text-xs font-bold uppercase">
    <a href="{{ route('booking.history') }}" class="px-3 py-1.5 border-2 border-slate-900 {{ !request('status') ? 'bg-blue-700 text-white shadow-[2px_2px_0px_#0F172A]' : 'bg-white text-slate-800' }}">Semua</a>
    <a href="{{ route('booking.history', ['status' => 'waiting_verification']) }}" class="px-3 py-1.5 border-2 border-slate-900 {{ request('status') === 'waiting_verification' ? 'bg-amber-400 text-slate-900 shadow-[2px_2px_0px_#0F172A]' : 'bg-white text-slate-800' }}">Menunggu Verifikasi</a>
    <a href="{{ route('booking.history', ['status' => 'confirmed']) }}" class="px-3 py-1.5 border-2 border-slate-900 {{ request('status') === 'confirmed' ? 'bg-blue-600 text-white shadow-[2px_2px_0px_#0F172A]' : 'bg-white text-slate-800' }}">Terkonfirmasi (Lunas)</a>
    <a href="{{ route('booking.history', ['status' => 'checked_in']) }}" class="px-3 py-1.5 border-2 border-slate-900 {{ request('status') === 'checked_in' ? 'bg-purple-600 text-white shadow-[2px_2px_0px_#0F172A]' : 'bg-white text-slate-800' }}">Check-In Siap</a>
    <a href="{{ route('booking.history', ['status' => 'completed']) }}" class="px-3 py-1.5 border-2 border-slate-900 {{ request('status') === 'completed' ? 'bg-emerald-600 text-white shadow-[2px_2px_0px_#0F172A]' : 'bg-white text-slate-800' }}">Selesai</a>
  </div>

  <!-- Mobile Cards (< md) -->
  <div class="space-y-4 md:hidden">
    @forelse($bookings as $b)
    <div class="border-2 border-slate-900 bg-white p-4 shadow-[4px_4px_0px_#1D4ED8] space-y-3">
      <div class="flex items-center justify-between border-b border-slate-200 pb-2">
        <span class="font-mono font-black text-blue-700 text-base">{{ $b->queue_number }}</span>
        <span class="border px-2 py-0.5 text-[10px] font-black uppercase {{ $b->status_badge_class }}">
          {{ $b->status_label }}
        </span>
      </div>

      <div class="text-xs space-y-1.5">
        <div class="flex justify-between">
          <span class="text-slate-500 font-bold">Paket:</span>
          <strong class="text-slate-900 text-right">{{ $b->package->name }}</strong>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500 font-bold">Jadwal:</span>
          <span class="font-mono text-right">{{ \Carbon\Carbon::parse($b->schedule->date)->translatedFormat('d M Y') }} &bull; {{ $b->schedule->formatted_time }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500 font-bold">Background:</span>
          <span class="text-right">{{ $b->studio_backdrop }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500 font-bold">Total:</span>
          <strong class="font-mono text-blue-700">Rp {{ number_format($b->total_price, 0, ',', '.') }}</strong>
        </div>
        <div class="flex justify-between items-center pt-1 border-t border-slate-200">
          <span class="text-slate-500 font-bold">Bukti Transfer:</span>
          @if($b->payment && $b->payment->proof_image_path)
            <a href="{{ asset($b->payment->proof_image_path) }}" target="_blank" class="text-blue-700 font-mono underline font-bold">Lihat Bukti 📷</a>
          @else
            <a href="{{ route('payment.checkout', $b->id) }}" class="text-amber-700 font-bold underline">Unggah Sekarang ➔</a>
          @endif
        </div>
      </div>

      <div class="pt-2 border-t border-slate-200 flex items-center justify-between gap-2">
        <a href="{{ route('booking.ticket', $b->id) }}" class="flex-1 neo-btn bg-slate-900 text-white text-center py-2 text-[11px] font-black uppercase tracking-wider">
          🎫 Tiket Digital
        </a>
        <a href="https://api.whatsapp.com/send?phone=628132674377&text=Halo%20Kasir%20Kothak%20Photo,%20saya%20ingin%20konfirmasi%20antrean%20{{ $b->queue_number }}" target="_blank" class="neo-btn bg-emerald-500 hover:bg-emerald-600 text-slate-900 px-3 py-2 text-[11px] font-black">
          WA Kasir
        </a>
      </div>
    </div>
    @empty
    <div class="border-2 border-slate-900 bg-white p-8 text-center text-slate-500 font-bold">
      Belum ada riwayat reservasi pada filter ini.
    </div>
    @endforelse
  </div>

  <!-- Desktop Table (>= md) -->
  <div class="neo-box bg-white overflow-hidden hidden md:block">
    <table class="w-full text-left border-collapse text-xs">
      <thead>
        <tr class="bg-blue-700 text-white border-b-2 border-slate-900 uppercase font-black tracking-wider">
          <th class="p-4">No. Antrean</th>
          <th class="p-4">Paket Foto</th>
          <th class="p-4">Jadwal Sesi</th>
          <th class="p-4">Total Biaya</th>
          <th class="p-4">Bukti QRIS</th>
          <th class="p-4">Status</th>
          <th class="p-4 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y-2 divide-slate-900">
        @forelse($bookings as $b)
        <tr class="hover:bg-slate-50 transition-colors">
          <td class="p-4 font-mono font-black text-blue-700 text-sm">
            {{ $b->queue_number }}
            <div class="text-[10px] font-mono text-slate-400">#{{ $b->id }}</div>
          </td>
          <td class="p-4">
            <div class="font-bold text-slate-900">{{ $b->package->name }}</div>
            <div class="text-[11px] text-slate-500">🎨 {{ $b->studio_backdrop }}</div>
          </td>
          <td class="p-4 font-mono">
            <div class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($b->schedule->date)->translatedFormat('d F Y') }}</div>
            <div class="text-blue-700 font-bold">{{ $b->schedule->formatted_time }}</div>
          </td>
          <td class="p-4 font-mono font-black text-slate-900">
            Rp {{ number_format($b->total_price, 0, ',', '.') }}
          </td>
          <td class="p-4">
            @if($b->payment && $b->payment->proof_image_path)
              <a href="{{ asset($b->payment->proof_image_path) }}" target="_blank" class="inline-flex items-center gap-1 font-mono text-blue-700 underline font-bold">
                <span>📷</span> Pratinjau
              </a>
              <div class="text-[10px] uppercase font-bold text-slate-500 mt-0.5">{{ $b->payment->status_label }}</div>
            @else
              <a href="{{ route('payment.checkout', $b->id) }}" class="text-amber-700 font-bold underline text-[11px]">
                Unggah Bukti ➔
              </a>
            @endif
          </td>
          <td class="p-4">
            <span class="border px-2.5 py-1 text-[10px] font-black uppercase inline-block {{ $b->status_badge_class }}">
              {{ $b->status_label }}
            </span>
          </td>
          <td class="p-4 text-right space-x-1">
            <a href="{{ route('booking.ticket', $b->id) }}" class="neo-btn bg-white hover:bg-slate-100 text-slate-900 border border-slate-900 px-3 py-1.5 text-[11px] font-black uppercase inline-block">
              🎫 Tiket
            </a>
            <a href="https://api.whatsapp.com/send?phone=628132674377&text=Halo%20Kasir%20Kothak%20Photo,%20saya%20ingin%20konfirmasi%20antrean%20{{ $b->queue_number }}" target="_blank" class="neo-btn bg-emerald-500 hover:bg-emerald-600 text-slate-900 border border-slate-900 px-2.5 py-1.5 text-[11px] font-black uppercase inline-block" title="Hubungi Kasir via WhatsApp">
              💬 WA
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="p-8 text-center text-slate-500 font-bold">
            Belum ada riwayat booking. Silakan lakukan reservasi paket photobox.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>
@endsection
