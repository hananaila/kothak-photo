@extends('layouts.admin')

@section('page_title', 'Manajemen Jadwal Slot & Kapasitas (FR-02)')

@section('content')
<div class="space-y-6 max-w-7xl">

  <!-- TOP ACTION BAR: DATE PICKER & GENERATOR -->
  <div class="neo-box bg-white p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <form method="GET" action="{{ route('admin.schedules.index') }}" class="flex items-center gap-3 text-xs">
      <label for="date" class="font-black uppercase text-slate-800">Pilih Tanggal Studio:</label>
      <input
        type="date"
        id="date"
        name="date"
        value="{{ $selectedDate }}"
        onchange="this.form.submit()"
        class="border-2 border-slate-900 px-3 py-1.5 font-mono font-bold bg-[#F8FAFC]"
      />
      <span class="text-slate-600 font-bold hidden sm:inline">
        📅 {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}
      </span>
    </form>

    <div class="flex items-center gap-2">
      <button type="button" onclick="openBatchModal()" class="neo-btn bg-emerald-500 hover:bg-emerald-600 text-slate-900 px-3.5 py-2 text-xs font-black uppercase tracking-wider flex items-center gap-1.5">
        <span>⚡</span> Generator Slot Otomatis
      </button>
      <button type="button" onclick="openSingleSlotModal()" class="neo-btn bg-blue-700 hover:bg-blue-800 text-white px-3.5 py-2 text-xs font-black uppercase tracking-wider flex items-center gap-1.5">
        <span>➕</span> Buat 1 Slot Manual
      </button>
    </div>
  </div>

  <!-- SLOTS GRID TABLE -->
  <div class="neo-box bg-white overflow-hidden">
    <div class="bg-slate-900 text-white px-5 py-3.5 font-black uppercase text-xs tracking-wider flex items-center justify-between">
      <span>Daftar Slot Waktu — {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</span>
      <span class="font-mono text-slate-400 text-xs">{{ $schedules->count() }} Slot Waktu</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-[#F8FAFC] border-b-2 border-slate-900 uppercase font-black text-slate-700">
            <th class="p-3.5">Slot Waktu</th>
            <th class="p-3.5">Kapasitas Maksimal</th>
            <th class="p-3.5">Terpesona / Terisi</th>
            <th class="p-3.5">Sisa Kuota Tersedia</th>
            <th class="p-3.5">Status Ketersediaan</th>
            <th class="p-3.5 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-slate-900">
          @forelse($schedules as $s)
          @php
            $rem = max(0, $s->capacity - $s->total_booked);
            $isAvail = $rem > 0;
          @endphp
          <tr class="hover:bg-slate-50 transition-colors {{ !$isAvail ? 'bg-rose-50/50' : '' }}">
            <td class="p-3.5 font-mono font-black text-blue-700 text-sm">
              {{ $s->formatted_time }}
            </td>
            <td class="p-3.5 font-mono font-bold">{{ $s->capacity }} Booking</td>
            <td class="p-3.5 font-mono font-bold text-slate-900">{{ $s->total_booked }} Booking Terdaftar</td>
            <td class="p-3.5 font-mono font-black {{ $isAvail ? 'text-emerald-700' : 'text-rose-600' }}">
              {{ $rem }} Slot Tersisa
            </td>
            <td class="p-3.5">
              @if($isAvail)
                <span class="bg-emerald-100 text-emerald-800 border border-emerald-400 px-2.5 py-0.5 text-[10px] font-black uppercase">
                  Tersedia untuk Pelanggan
                </span>
              @else
                <span class="bg-rose-100 text-rose-800 border border-rose-400 px-2.5 py-0.5 text-[10px] font-black uppercase">
                  Kapasitas Penuh
                </span>
              @endif
            </td>
            <td class="p-3.5 text-right">
              @if($s->total_booked == 0)
                <form action="{{ route('admin.schedules.destroy', $s->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus slot waktu ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="neo-btn bg-rose-600 hover:bg-rose-700 text-white px-2.5 py-1 text-[11px] font-bold border border-slate-900">
                    Hapus Slot
                  </button>
                </form>
              @else
                <span class="text-[11px] text-slate-400 font-bold">Ada Reservasi</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="p-8 text-center text-slate-500 font-bold">
              Belum ada slot waktu pada tanggal ini. Gunakan tombol "Generator Slot Otomatis" di atas!
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- MODAL: BATCH GENERATOR SLOT OTOMATIS -->
<div id="batchModal" class="fixed inset-0 bg-slate-900/80 z-50 hidden flex items-center justify-center p-4">
  <div class="neo-box bg-white max-w-md w-full p-6 space-y-4">
    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3">
      <div>
        <span class="text-[10px] font-black uppercase text-emerald-700">GENERATOR CEPAT</span>
        <h3 class="text-lg font-black text-slate-900">Buat Slot Massal Per Hari</h3>
      </div>
      <button onclick="closeBatchModal()" class="text-xl font-black text-slate-500 hover:text-black">✕</button>
    </div>

    <form action="{{ route('admin.schedules.batch') }}" method="POST" class="space-y-3 text-xs">
      @csrf
      <input type="hidden" name="date" value="{{ $selectedDate }}" />

      <div class="bg-blue-50 border border-blue-200 p-2 text-blue-900 font-bold">
        Tanggal Target: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-black uppercase text-slate-900 mb-1">Jam Mulai (Pukul)</label>
          <input type="number" name="start_hour" value="10" min="0" max="23" required class="w-full border-2 border-slate-900 p-2 font-mono font-bold" />
        </div>
        <div>
          <label class="block font-black uppercase text-slate-900 mb-1">Jam Selesai (Pukul)</label>
          <input type="number" name="end_hour" value="21" min="1" max="24" required class="w-full border-2 border-slate-900 p-2 font-mono font-bold" />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-black uppercase text-slate-900 mb-1">Interval Slot (Menit)</label>
          <select name="duration_minutes" class="w-full border-2 border-slate-900 p-2 font-bold bg-white">
            <option value="30" selected>Setiap 30 Menit</option>
            <option value="45">Setiap 45 Menit</option>
            <option value="60">Setiap 60 Menit (1 Jam)</option>
          </select>
        </div>
        <div>
          <label class="block font-black uppercase text-slate-900 mb-1">Kapasitas per Slot</label>
          <input type="number" name="capacity" value="3" min="1" max="20" required class="w-full border-2 border-slate-900 p-2 font-mono font-bold" />
        </div>
      </div>

      <div class="pt-3 border-t-2 border-slate-900 flex gap-2">
        <button type="submit" class="flex-1 neo-btn bg-emerald-500 hover:bg-emerald-600 text-slate-900 py-2.5 font-black uppercase text-xs">
          Generate Semua Slot Otomatis ⚡
        </button>
        <button type="button" onclick="closeBatchModal()" class="neo-btn bg-white hover:bg-slate-100 px-4 py-2.5 font-bold uppercase text-xs">
          Batal
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: 1 SLOT MANUAL -->
<div id="singleSlotModal" class="fixed inset-0 bg-slate-900/80 z-50 hidden flex items-center justify-center p-4">
  <div class="neo-box bg-white max-w-sm w-full p-6 space-y-4">
    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3">
      <h3 class="text-lg font-black text-slate-900">Tambah 1 Slot Waktu</h3>
      <button onclick="closeSingleSlotModal()" class="text-xl font-black text-slate-500 hover:text-black">✕</button>
    </div>

    <form action="{{ route('admin.schedules.store') }}" method="POST" class="space-y-3 text-xs">
      @csrf
      <input type="hidden" name="date" value="{{ $selectedDate }}" />

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-black uppercase text-slate-900 mb-1">Jam Mulai</label>
          <input type="time" name="start_time" required value="13:00" class="w-full border-2 border-slate-900 p-2 font-mono font-bold" />
        </div>
        <div>
          <label class="block font-black uppercase text-slate-900 mb-1">Jam Selesai</label>
          <input type="time" name="end_time" required value="13:30" class="w-full border-2 border-slate-900 p-2 font-mono font-bold" />
        </div>
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Kapasitas Maksimal Booking</label>
        <input type="number" name="capacity" value="3" min="1" max="20" required class="w-full border-2 border-slate-900 p-2 font-mono font-bold" />
      </div>

      <div class="pt-3 border-t-2 border-slate-900 flex gap-2">
        <button type="submit" class="flex-1 neo-btn bg-blue-700 hover:bg-blue-800 text-white py-2.5 font-black uppercase text-xs">
          Simpan Slot
        </button>
        <button type="button" onclick="closeSingleSlotModal()" class="neo-btn bg-white hover:bg-slate-100 px-4 py-2.5 font-bold uppercase text-xs">
          Batal
        </button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  function openBatchModal() { document.getElementById('batchModal').classList.remove('hidden'); }
  function closeBatchModal() { document.getElementById('batchModal').classList.add('hidden'); }

  function openSingleSlotModal() { document.getElementById('singleSlotModal').classList.remove('hidden'); }
  function closeSingleSlotModal() { document.getElementById('singleSlotModal').classList.add('hidden'); }
</script>
@endpush
@endsection
