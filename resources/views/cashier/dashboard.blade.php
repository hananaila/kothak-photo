@extends('layouts.app')

@section('title', 'Dasbor Kasir & Antrean Booth — KOTHaK Photo Studio')

@section('content')
<div class="space-y-6 max-w-[1500px] w-full mx-auto px-2 sm:px-4">

  <!-- CASHIER HEADER: COMPACT & CLEAN -->
  <div class="bg-white rounded-2xl sm:rounded-3xl border border-[#E7E2D8] p-5 sm:p-6 shadow-[0_4px_20px_-4px_rgba(26,26,46,0.05),0_1px_3px_rgba(26,26,46,0.02)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-xl sm:text-2xl font-heading font-black text-[#1A1A2E]">
        Dasbor Kasir Photobox
      </h1>
      <p class="text-xs text-slate-500 font-sans mt-0.5">
        Panggil antrean dan pantau sesi aktif bilik foto secara real-time.
      </p>
    </div>

    <div class="flex items-center gap-3">
      <div class="px-4 py-2 rounded-xl bg-[#F4F1EA] border border-[#E7E2D8] text-right">
        <span class="block text-[9px] font-heading font-black uppercase text-slate-500 tracking-wider">HARI INI</span>
        <span class="text-xs font-mono font-bold text-[#1E3A8A]">
          {{ \Carbon\Carbon::parse($today)->translatedFormat('d M Y') }}
        </span>
      </div>
    </div>
  </div>

  <!-- 1 LAYAR CARD BOOTH (LEBIH LEGA, BERSIH, MINIMALIS & TINGGI SEJAJAR) -->
  <div id="boothBoardContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 items-stretch">
    @foreach($booths as $booth)
      <div class="bg-white rounded-2xl sm:rounded-3xl border border-[#E7E2D8] shadow-[0_4px_20px_-4px_rgba(26,26,46,0.05),0_1px_3px_rgba(26,26,46,0.02)] overflow-hidden flex flex-col justify-between p-5 space-y-4 h-full">

        <!-- BOOTH HEADER: SIMPLE & AIRY -->
        <div class="flex items-center justify-between pb-3.5 border-b border-[#F0ECE4]">
          <div class="flex items-center gap-2.5 min-w-0 pr-2">
            <span class="w-8 h-8 rounded-xl bg-[#E8EEF9] text-[#1E3A8A] border border-[#1E3A8A]/20 flex items-center justify-center font-mono font-black text-sm shrink-0">
              {{ $booth['prefix'] }}
            </span>
            <h2 class="font-heading font-black text-base text-[#1A1A2E] leading-tight truncate">
              {{ $booth['name'] }}
            </h2>
          </div>

          <div class="flex items-center gap-1.5 shrink-0">
            <span class="px-2.5 py-1 rounded-full text-[11px] font-mono font-black {{ $booth['waiting_count'] > 0 ? 'bg-[#E8EEF9] text-[#1E3A8A] border border-[#1E3A8A]/20' : 'bg-slate-100 text-slate-400' }}">
              {{ $booth['waiting_count'] }} Antrean
            </span>
          </div>
        </div>

        <!-- SESI AKTIF (POIN A): 3 KONDISI (KONFIRMASI 30S, FOTO 15M, STANDBY) DENGAN TINGGI IDENTIK (h-[132px]) -->
        @if($booth['is_calling'])
          @php $callingBooking = $booth['active_booking']; @endphp
          <div class="h-[132px] p-3 rounded-2xl bg-amber-50/90 border border-amber-300 text-amber-950 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                <span class="text-[10px] font-heading font-black uppercase tracking-wider text-amber-800">
                  Konfirmasi Kehadiran (30 Dtk)
                </span>
              </div>
              <span class="text-[11px] font-mono font-bold text-amber-900 auto-countdown-timer bg-amber-100/90 border border-amber-300 px-2 py-0.5 rounded-lg" data-seconds="{{ $booth['confirmation_seconds'] }}" data-prefix="Sisa " data-expired-text="Auto Masuk...">
                Sisa 00:{{ sprintf('%02d', $booth['confirmation_seconds']) }}
              </span>
            </div>

            <div class="flex items-baseline justify-between gap-2">
              <div>
                <span class="text-2xl font-mono font-black text-amber-950 leading-none">
                  {{ $callingBooking->queue_number }}
                </span>
                <span class="text-xs font-bold text-slate-800 ml-2">
                  {{ $callingBooking->user->name }}
                </span>
              </div>
              <span class="text-[10px] font-mono text-amber-700 font-bold">
                Dipanggil
              </span>
            </div>

            <div class="flex items-center gap-2">
              <form action="{{ route('cashier.call-specific', $callingBooking->id) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-1.5 px-2 rounded-xl font-heading font-black uppercase tracking-wider text-[10px] flex items-center justify-center gap-1 shadow-xs transition-all cursor-pointer" title="Pelanggan hadir, langsung mulai sesi foto sekarang">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                  <span>Masuk Sekarang</span>
                </button>
              </form>

              <button type="button" onclick="openNoShowModal('{{ route('cashier.no-show', $callingBooking->id) }}', '{{ $callingBooking->queue_number }}', '{{ addslashes($callingBooking->user->name) }}', '{{ $booth['name'] }}')" class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 py-1.5 px-2.5 rounded-xl font-heading font-bold uppercase text-[10px] tracking-wider transition-colors cursor-pointer" title="Tandai tidak hadir dan lewati antrean">
                <span>Tidak Hadir</span>
              </button>
            </div>
          </div>
        @elseif($booth['in_studio'])
          @php $activeBooking = $booth['in_studio']; @endphp
          <div class="h-[132px] p-3.5 rounded-2xl bg-emerald-50/80 border border-emerald-300/80 text-emerald-950 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span class="text-[10px] font-heading font-black uppercase tracking-wider text-emerald-700">
                  Foto di Bilik ({{ $booth['duration_minutes'] }} Mnt)
                </span>
              </div>
              <span class="text-[11px] font-mono font-bold text-emerald-800 auto-countdown-timer" data-seconds="{{ $booth['remaining_seconds'] }}" data-prefix="Sisa " data-expired-text="Sesi Selesai (Memajukan...)">
                @php
                  $min = floor($booth['remaining_seconds'] / 60);
                  $sec = $booth['remaining_seconds'] % 60;
                @endphp
                Sisa {{ sprintf('%02d:%02d', $min, $sec) }}
              </span>
            </div>

            <div class="flex items-baseline justify-between gap-2">
              <div>
                <span class="text-2xl font-mono font-black text-emerald-900 leading-none">
                  {{ $activeBooking->queue_number }}
                </span>
                <span class="text-xs font-bold text-slate-800 ml-2">
                  {{ $activeBooking->user->name }}
                </span>
              </div>
              <span class="text-[10px] font-mono text-slate-500">
                Masuk {{ $activeBooking->called_at ? $activeBooking->called_at->format('H.i') : now()->format('H.i') }}
              </span>
            </div>

            <form action="{{ route('cashier.complete', $activeBooking->id) }}" method="POST">
              @csrf
              <button type="submit" title="Klik jika pelanggan selesai sebelum 15 menit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 px-3 rounded-xl font-heading font-black uppercase tracking-wider text-[11px] flex items-center justify-center gap-1.5 shadow-xs hover:shadow-md transition-all cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>Selesaikan Sesi (Manual)</span>
              </button>
            </form>
          </div>
        @else
          <!-- KONDISI BILIK KOSONG: TINGGI & SPACE SAMA PERSIS DENGAN KONDISI BERISI AGAR SEIMBANG -->
          <div class="h-[132px] p-3.5 rounded-2xl bg-[#F8F7F4] border border-dashed border-[#DDD7CC] text-slate-500 flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between">
              <span class="text-[10px] font-heading font-black uppercase tracking-wider text-slate-600">
                Bilik Siap
              </span>
              <span class="text-[11px] font-mono text-slate-400">
                Standby
              </span>
            </div>

            <div>
              <div class="text-base font-heading font-bold text-slate-700 leading-tight">
                Bilik Kosong
              </div>
              <p class="text-[11px] text-slate-400 font-sans mt-0.5">
                Bilik siap digunakan, silakan panggil antrean di bawah.
              </p>
            </div>

            <div class="w-full py-1.5 px-3 rounded-xl bg-[#EFECE5] border border-[#E2DDD3] text-center text-[10px] font-heading font-bold uppercase tracking-wider text-slate-500">
              Menunggu Panggilan Masuk
            </div>
          </div>
        @endif

        <!-- DAFTAR ANTREAN MENUNGGU (POIN B): TINGGI KONSISTEN & BISA DISCROLL -->
        <div class="space-y-2 pt-1 flex-1 flex flex-col">
          <div class="flex items-center justify-between text-[11px]">
            <span class="font-heading font-black uppercase tracking-wider text-slate-600">
              Antrean Menunggu
            </span>
            <span class="font-mono text-slate-400 text-[10px]">
              {{ $booth['waiting']->count() }} orang
            </span>
          </div>

          @if($booth['waiting']->isEmpty())
            <div class="h-[340px] flex flex-col items-center justify-center text-center rounded-xl bg-[#F8F7F4] border border-dashed border-[#DDD7CC] text-slate-400 p-4 space-y-1">
              <p class="text-xs font-heading font-bold text-slate-500">Belum ada antrean</p>
              <p class="text-[10px] text-slate-400">Pemesanan baru akan otomatis muncul di sini</p>
            </div>
          @else
            <div class="h-[340px] space-y-2.5 overflow-y-auto pr-1 booth-scroll-list">
              @foreach($booth['waiting'] as $q)
                <div class="p-3 rounded-xl border {{ $loop->first ? 'border-[#1E3A8A]/30 bg-[#E8EEF9]/25' : 'border-[#EBE7E0] bg-[#FAFAF8] hover:border-[#1E3A8A]/30 hover:bg-white' }} shadow-2xs space-y-2 transition-all">
                  
                  <!-- Top Row: Kode Antrean di Kiri, Badge Berikutnya di Kanan -->
                  <div class="flex items-center justify-between pb-1.5 border-b border-[#F0ECE4]">
                    <span class="whitespace-nowrap shrink-0 inline-block font-mono font-black text-xs text-[#1E3A8A] bg-white border border-[#1E3A8A]/20 px-2 py-0.5 rounded-md shadow-2xs">
                      {{ $q->queue_number }}
                    </span>

                    @if($loop->first)
                      <span class="whitespace-nowrap shrink-0 text-[9px] font-heading font-black uppercase text-emerald-800 bg-emerald-100/90 border border-emerald-300/80 px-2 py-0.5 rounded-full">
                        Berikutnya
                      </span>
                    @else
                      <span class="text-[10px] font-mono text-slate-400 font-bold">
                        #{{ $loop->iteration }}
                      </span>
                    @endif
                  </div>

                  <!-- Bottom Row: Nama Pemesan & Tombol Aksi Kasir -->
                  <div class="flex items-center justify-between gap-2 pt-0.5">
                    <div class="min-w-0 flex-1">
                      <div class="font-bold text-slate-800 text-xs truncate">
                        {{ $q->user->name }}
                      </div>
                      <div class="text-[10px] font-mono text-slate-400 truncate">
                        {{ $q->created_at->format('H.i') }} WIB &bull; {{ $q->user->phone_whatsapp }}
                      </div>
                    </div>

                    <!-- Right: Action Buttons -->
                    <div class="flex items-center gap-1.5 shrink-0">
                      <!-- Tombol Masuk -->
                      <form action="{{ route('cashier.call-specific', $q->id) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="playChimeAudio()" class="bg-[#1E3A8A] hover:bg-[#2D2DB0] text-white px-3 py-1.5 rounded-lg font-heading font-black uppercase text-[10px] tracking-wider flex items-center gap-1 shadow-2xs transition-all" title="Izinkan masuk ke bilik">
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                          <span>Masuk</span>
                        </button>
                      </form>

                      <!-- Tombol Tidak Hadir (Membuka Card Modal Konfirmasi) -->
                      <button type="button" onclick="openNoShowModal('{{ route('cashier.no-show', $q->id) }}', '{{ $q->queue_number }}', '{{ addslashes($q->user->name) }}', '{{ $booth['name'] }}')" class="bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-700 border border-slate-200 hover:border-rose-200 px-2 py-1.5 rounded-lg font-heading font-bold uppercase text-[10px] tracking-wider transition-colors" title="Lewati jika tidak datang">
                        <span>Tidak Hadir</span>
                      </button>
                    </div>
                  </div>

                </div>
              @endforeach
            </div>
          @endif
        </div>

        <!-- RIWAYAT RINGKAS (COLLAPSIBLE SEDERHANA DI BAWAH) -->
        <details class="group pt-2 border-t border-slate-100 text-xs">
          <summary class="cursor-pointer text-[10px] font-heading font-bold uppercase text-slate-400 hover:text-slate-700 flex items-center justify-between select-none py-1">
            <span>Riwayat Selesai Hari Ini ({{ $booth['finished']->count() }})</span>
            <svg class="w-3 h-3 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
          </summary>

          <div class="mt-2 space-y-1.5 max-h-36 overflow-y-auto">
            @forelse($booth['finished'] as $fb)
              <div class="flex items-center justify-between px-2.5 py-1.5 rounded-lg bg-slate-50 text-[10px]">
                <div class="flex items-center gap-1.5">
                  <span class="font-mono font-bold text-slate-700">{{ $fb->queue_number }}</span>
                  <span class="text-slate-600 truncate max-w-[120px]">{{ $fb->user->name }}</span>
                </div>
                <span class="px-1.5 py-0.5 rounded text-[8px] font-heading font-bold uppercase {{ $fb->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                  {{ $fb->status === 'completed' ? 'Selesai' : 'Tidak Hadir' }}
                </span>
              </div>
            @empty
              <p class="text-[10px] text-slate-400 text-center py-1">
                Belum ada riwayat selesai.
              </p>
            @endforelse
          </div>
        </details>

      </div>
    @endforeach
  </div>

</div>

<!-- CUSTOM MODAL: CARD KONFIRMASI TIDAK HADIR -->
<div id="noShowModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#0F172A]/60 backdrop-blur-xs hidden transition-opacity">
  <div class="bg-white rounded-3xl max-w-sm w-full border border-[#E7E2D8] shadow-[0_25px_60px_-15px_rgba(26,26,46,0.35)] overflow-hidden animate-in fade-in zoom-in-95 duration-150">
    
    <!-- BRAND STUDIO HEADER BAR -->
    <div class="bg-[#1E3A8A] px-5 py-3.5 flex items-center justify-between text-white border-b border-[#1E3A8A]/30">
      <div class="flex items-center gap-2">
        <div class="w-5 h-5 rounded-md bg-white/15 border border-white/20 flex items-center justify-center font-mono font-black text-[9px] text-white">
          K
        </div>
        <div class="flex items-baseline gap-1">
          <span class="text-xs font-heading font-black tracking-widest uppercase text-white">KOTHaK</span>
          <span class="text-[10px] font-serif italic text-white/80 lowercase">kasir studio</span>
        </div>
      </div>

      <button
        type="button"
        onclick="closeNoShowModal()"
        class="w-6 h-6 rounded-lg bg-white/10 hover:bg-white/20 text-white/80 hover:text-white flex items-center justify-center transition-all cursor-pointer"
        title="Tutup (Esc)"
      >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <!-- BODY -->
    <div class="p-5 sm:p-6 space-y-4">
      <div class="space-y-1">
        <div class="flex items-center gap-2">
          <span class="text-[10px] font-heading font-black tracking-wider uppercase px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-200">
            ANTREAN TIDAK HADIR
          </span>
          <span class="text-[10px] font-mono text-slate-400">#KasirAction</span>
        </div>
        <h3 class="font-heading font-black text-lg text-[#1A1A2E] leading-tight">
          Konfirmasi Lewati Antrean
        </h3>
        <p class="text-xs text-slate-500 font-sans">
          Tandai antrean sebagai terlewat agar antrean berikutnya dapat dipanggil.
        </p>
      </div>

      <!-- Ticket Detail Summary Card -->
      <div class="p-3.5 rounded-2xl bg-[#FAF9F6] border border-[#E7E2D8] text-xs space-y-2">
        <div class="flex items-center justify-between">
          <span class="text-slate-500 font-sans">Nomor Antrean:</span>
          <span class="font-mono font-black text-base text-[#1E3A8A] bg-[#E8EEF9] px-2.5 py-0.5 rounded-lg border border-[#1E3A8A]/20" id="modalQueueNumber">-</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-slate-500 font-sans">Nama Pelanggan:</span>
          <span class="font-bold text-slate-800" id="modalCustomerName">-</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-slate-500 font-sans">Pilihan Booth:</span>
          <span class="font-medium text-slate-600" id="modalBoothName">-</span>
        </div>
      </div>
    </div>

    <!-- FOOTER -->
    <div class="bg-[#F8F7F4] px-5 py-3.5 border-t border-[#E7E2D8] flex items-center gap-2.5">
      <button type="button" onclick="closeNoShowModal()" class="flex-1 py-2.5 px-4 rounded-xl font-heading font-bold uppercase text-xs tracking-wider bg-white hover:bg-slate-100 text-slate-700 border border-[#DDD7CC] transition-all text-center cursor-pointer shadow-2xs">
        Batal
      </button>

      <form id="noShowForm" action="" method="POST" class="flex-1">
        @csrf
        <button type="submit" class="w-full py-2.5 px-4 rounded-xl font-heading font-black uppercase text-xs tracking-wider bg-rose-600 hover:bg-rose-700 text-white shadow-xs hover:shadow-md active:scale-95 transition-all text-center cursor-pointer">
          Ya, Lewati
        </button>
      </form>
    </div>

  </div>
</div>

<!-- SCRIPTS: AUDIO CHIME & MODAL LOGIC -->
<script>
function openNoShowModal(actionUrl, queueNum, customerName, boothName) {
  const modal = document.getElementById('noShowModal');
  const form = document.getElementById('noShowForm');
  const textQueue = document.getElementById('modalQueueNumber');
  const textCustomer = document.getElementById('modalCustomerName');
  const textBooth = document.getElementById('modalBoothName');

  form.action = actionUrl;
  textQueue.textContent = queueNum;
  textCustomer.textContent = customerName;
  textBooth.textContent = boothName;

  modal.classList.remove('hidden');
}

function closeNoShowModal() {
  const modal = document.getElementById('noShowModal');
  modal.classList.add('hidden');
}

// Close modal on Escape key or backdrop click
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeNoShowModal();
});
document.getElementById('noShowModal').addEventListener('click', function(e) {
  if (e.target === this) closeNoShowModal();
});

function playChimeAudio() {
  try {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.type = 'sine';
    osc.frequency.setValueAtTime(587.33, ctx.currentTime);
    osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15);
    gain.gain.setValueAtTime(0.3, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
    osc.connect(gain);
    gain.connect(ctx.destination);
    osc.start();
    osc.stop(ctx.currentTime + 0.45);
  } catch (e) {
    // Audio Context might be restricted before user gesture
  }
}

// LIVE AUTO-REFRESH (Setiap 3.5 detik, tanpa cache, tanpa flicker & tanpa reset scroll)
let isRefreshing = false;

// Kumpulkan nomor antrean yang saat ini tampil di layar
function getBoardQueueCodes(rootEl = document) {
  const codes = [];
  rootEl.querySelectorAll('.font-mono').forEach(el => {
    const text = el.textContent.trim();
    if (text.match(/^[A-Z]-[0-9]{3}$/)) {
      codes.push(text);
    }
  });
  return codes;
}

let knownQueueCodes = getBoardQueueCodes(document);

function fetchLiveBoothQueue() {
  if (isRefreshing) return;

  const modal = document.getElementById('noShowModal');
  // Jangan timpa jika kasir sedang membuka modal konfirmasi Tidak Hadir
  if (modal && !modal.classList.contains('hidden')) return;

  // Catat posisi scroll tiap bilik
  const scrollContainers = document.querySelectorAll('.booth-scroll-list');
  const scrollMap = [];
  scrollContainers.forEach((el, idx) => {
    scrollMap[idx] = el.scrollTop;
  });

  isRefreshing = true;

  // Menggunakan timestamp query & cache: 'no-store' agar browser tidak pernah menyimpan response usang di cache
  const url = window.location.pathname + '?_t=' + Date.now();

  fetch(url, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Cache-Control': 'no-cache, no-store, must-revalidate',
      'Pragma': 'no-cache'
    },
    cache: 'no-store'
  })
  .then(res => res.text())
  .then(html => {
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const newBoard = doc.getElementById('boothBoardContainer');
    const currentBoard = document.getElementById('boothBoardContainer');

    if (newBoard && currentBoard) {
      // Deteksi jika ada nomor antrean baru yang masuk untuk bunyikan lonceng bel kasir
      const newCodes = getBoardQueueCodes(newBoard);
      const hasIncomingQueue = newCodes.some(c => !knownQueueCodes.includes(c));
      if (hasIncomingQueue && knownQueueCodes.length > 0) {
        playChimeAudio();
      }
      knownQueueCodes = newCodes;

      // Perbarui tampilan antrean kasir secara real-time
      currentBoard.innerHTML = newBoard.innerHTML;

      // Pulihkan posisi scroll bilik foto
      const newScrollContainers = currentBoard.querySelectorAll('.booth-scroll-list');
      newScrollContainers.forEach((el, idx) => {
        if (scrollMap[idx] !== undefined) {
          el.scrollTop = scrollMap[idx];
        }
      });
    }
  })
  .catch(err => {
    // Silent fail on brief network blips
  })
  .finally(() => {
    isRefreshing = false;
  });
}

// Hitung mundur visual detik sesi foto bilik & konfirmasi 30 detik secara live
setInterval(() => {
  document.querySelectorAll('.auto-countdown-timer').forEach(el => {
    let sec = parseInt(el.getAttribute('data-seconds') || '0', 10);
    if (sec > 0) {
      sec--;
      el.setAttribute('data-seconds', sec);
      const m = Math.floor(sec / 60);
      const s = sec % 60;
      const prefix = el.getAttribute('data-prefix') || 'Sisa ';
      el.textContent = prefix + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    } else if (sec === 0) {
      el.textContent = el.getAttribute('data-expired-text') || 'Sesi Selesai (Memajukan...)';
      broadcastQueueBump();
      fetchLiveBoothQueue();
    }
  });
}, 1000);

// Broadcast update instan ke tab antrean pelanggan & display
function broadcastQueueBump(payload = {}) {
  const eventData = {
    type: 'queue_updated',
    time: Date.now(),
    ...payload
  };

  try {
    if ('BroadcastChannel' in window) {
      const bc = new BroadcastChannel('kothak_live_queue');
      bc.postMessage(eventData);
    }
    localStorage.setItem('kothak_queue_bump', JSON.stringify(eventData));
  } catch(e) {}
}

// Intersep form aksi kasir (Masuk, Selesai, Lewati) dengan AJAX agar eksekusi instan tanpa reload halaman
document.addEventListener('submit', async function(e) {
  const form = e.target.closest('form');
  if (!form || !form.action) return;

  const isCall = form.action.includes('/cashier/call/');
  const isComplete = form.action.includes('/cashier/complete/');
  const isNoShow = form.action.includes('/cashier/no-show');

  if (!isCall && !isComplete && !isNoShow) {
    broadcastQueueBump();
    return;
  }

  e.preventDefault();
  playChimeAudio();

  const submitBtn = form.querySelector('button[type="submit"]');
  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-75');
  }

  try {
    const res = await fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    });

    if (res.ok) {
      const data = await res.json();

      // Tutup modal tidak hadir jika sedang terbuka
      const modal = document.getElementById('noShowModal');
      if (modal) modal.classList.add('hidden');

      // Siarkan seketika (0 milidetik) ke tab tiket pelanggan dengan data spesifik
      broadcastQueueBump(data);

      // Perbarui tampilan bilik kasir langsung
      fetchLiveBoothQueue();
    } else {
      form.submit();
    }
  } catch(err) {
    form.submit();
  }
});

// Jalankan polling otomatis setiap 1 detik (Real-Time Ultra Responsif)
setInterval(fetchLiveBoothQueue, 1000);
</script>
@endsection
