@extends('layouts.app')

@section('title', 'Dasbor Kasir & Verifikasi — KOTHaK Photo Studio')

@section('content')
<div class="space-y-6 sm:space-y-8 max-w-7xl mx-auto">

  <!-- CASHIER HEADER -->
  <div class="border-2 border-slate-900 bg-white p-5 sm:p-6 shadow-[6px_6px_0px_#1D4ED8]">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-2 bg-emerald-100 border border-emerald-400 px-3 py-1 text-xs font-bold text-emerald-900 uppercase tracking-widest mb-2">
          <span>OPERATOR & KASIR STUDIO PHOTOBOX (FR-08)</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900">
          Dasbor Verifikasi QRIS & Antrean Pintar
        </h1>
        <p class="text-xs text-slate-600 mt-1">
          Verifikasi bukti pembayaran QRIS statis, check-in kedatangan tamu di studio, panggil giliran antrean, dan input tamu walk-in.
        </p>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-wrap items-center gap-2.5">
        <!-- Call Next -->
        <form action="{{ route('cashier.call-next') }}" method="POST">
          @csrf
          <button type="submit" onclick="playChimeAudio()" class="neo-btn bg-emerald-500 hover:bg-emerald-600 text-slate-900 py-2.5 sm:py-3 px-4 font-black uppercase tracking-wider text-xs flex items-center gap-2">
            <span>📢</span>
            <span>Panggil Berikutnya</span>
          </button>
        </form>

        <!-- Walk-in Modal Trigger -->
        <button type="button" onclick="openWalkInModal()" class="neo-btn bg-blue-700 hover:bg-blue-800 text-white py-2.5 sm:py-3 px-4 font-black uppercase tracking-wider text-xs flex items-center gap-2">
          <span>➕</span>
          <span>Tamu Walk-In</span>
        </button>

        <!-- TV Display Shortcut -->
        <a href="{{ route('queue.display') }}" target="_blank" class="neo-btn bg-white hover:bg-slate-50 text-slate-900 py-2.5 sm:py-3 px-3 font-bold uppercase tracking-wider text-xs flex items-center gap-1.5" title="Buka Layar Monitor TV">
          <span>📺</span>
          <span>Layar TV ↗</span>
        </a>
      </div>
    </div>

    <!-- CURRENT STUDIO STATUS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mt-6 pt-6 border-t-2 border-slate-900">
      <div class="border-2 border-slate-900 bg-[#F5F4EE] p-4 text-center shadow-[3px_3px_0px_#0F172A]">
        <div class="text-[10px] font-black uppercase text-slate-500 tracking-wider">SEDANG DI STUDIO</div>
        <div class="text-2xl sm:text-3xl font-black font-mono text-emerald-600 mt-1">
          {{ $inStudio?->queue_number ?? 'KOSONG' }}
        </div>
        <div class="text-xs font-bold text-slate-800 truncate mt-0.5">
          {{ $inStudio ? $inStudio->user->name . ' (' . $inStudio->package->name . ')' : 'Studio Siap / Kosong' }}
        </div>
        @if($inStudio)
          <form action="{{ route('cashier.complete', $inStudio->id) }}" method="POST" class="mt-2">
            @csrf
            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-[10px] font-black uppercase px-2.5 py-1 border border-slate-900">
              Selesaikan Sesi ✓
            </button>
          </form>
        @endif
      </div>

      <div class="border-2 border-slate-900 bg-blue-50 p-4 text-center shadow-[3px_3px_0px_#1D4ED8]">
        <div class="text-[10px] font-black uppercase text-blue-800 tracking-wider">BERIKUTNYA (STANDBY)</div>
        <div class="text-2xl sm:text-3xl font-black font-mono text-blue-700 mt-1">
          {{ $nextQueue?->queue_number ?? 'TIDAK ADA' }}
        </div>
        <div class="text-xs font-bold text-slate-800 truncate mt-0.5">
          {{ $nextQueue ? $nextQueue->user->name . ' (' . $nextQueue->package->name . ')' : 'Belum ada antrean standby' }}
        </div>
        @if($nextQueue)
          <form action="{{ route('cashier.call-specific', $nextQueue->id) }}" method="POST" class="mt-2">
            @csrf
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white text-[10px] font-black uppercase px-2.5 py-1 border border-slate-900">
              Panggil Masuk 📢
            </button>
          </form>
        @endif
      </div>

      <div class="border-2 border-slate-900 bg-amber-50 p-4 text-center shadow-[3px_3px_0px_#D97706]">
        <div class="text-[10px] font-black uppercase text-amber-800 tracking-wider">TOTAL ANTREAN HARI INI</div>
        <div class="text-2xl sm:text-3xl font-black font-mono text-amber-700 mt-1">
          {{ $todayBookings->count() }}
        </div>
        <div class="text-xs font-bold text-slate-700 mt-0.5">
          {{ \Carbon\Carbon::parse($today)->translatedFormat('d F Y') }}
        </div>
      </div>
    </div>
  </div>

  <!-- SECTION 1: VERIFIKASI PEMBAYARAN QRIS STATIS (FR-05) -->
  <section class="border-2 border-slate-900 bg-white shadow-[6px_6px_0px_#1D4ED8]">
    <div class="bg-slate-900 text-white px-5 py-3.5 font-black uppercase text-xs tracking-wider flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span>1. Verifikasi Pembayaran QRIS Statis (FR-05)</span>
        @if($pendingPayments->count() > 0)
          <span class="bg-rose-500 text-white text-[10px] px-2 py-0.5 font-mono animate-pulse">
            {{ $pendingPayments->count() }} Menunggu
          </span>
        @endif
      </div>
      <span class="font-mono text-slate-400 text-xs">{{ $allPayments->count() }} Transaksi Terbaru</span>
    </div>

    <!-- Table of Payments -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-[#F5F4EE] border-b-2 border-slate-900 uppercase font-black text-slate-700">
            <th class="p-3.5">Antrean</th>
            <th class="p-3.5">Pelanggan</th>
            <th class="p-3.5">Paket & Total</th>
            <th class="p-3.5">Bukti Transfer</th>
            <th class="p-3.5">Status</th>
            <th class="p-3.5 text-right">Keputusan Kasir</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-slate-900">
          @forelse($allPayments as $pay)
          @php $b = $pay->booking; @endphp
          <tr class="hover:bg-slate-50 transition-colors {{ $pay->payment_status === 'pending' ? 'bg-amber-50/60' : '' }}">
            <td class="p-3.5 font-mono font-black text-blue-700 text-sm">
              {{ $b?->queue_number ?? '-' }}
              <div class="text-[10px] font-mono text-slate-400">#{{ $pay->id }}</div>
            </td>
            <td class="p-3.5">
              <div class="font-bold text-slate-900">{{ $b?->user?->name ?? 'Guest' }}</div>
              <div class="text-[11px] font-mono text-slate-500">{{ $b?->user?->phone_whatsapp ?? '-' }}</div>
            </td>
            <td class="p-3.5">
              <div class="font-bold text-slate-900">{{ $b?->package?->name ?? '-' }}</div>
              <div class="font-mono font-black text-blue-700">Rp {{ number_format($pay->amount ?: ($b?->total_price ?? 0), 0, ',', '.') }}</div>
            </td>
            <td class="p-3.5">
              @if($pay->proof_image_path)
                <button type="button" onclick="openProofModal('{{ asset($pay->proof_image_path) }}', '{{ $b?->queue_number }}', '{{ $b?->user?->name }}', 'Rp {{ number_format($pay->amount ?: ($b?->total_price ?? 0), 0, ',', '.') }}')" class="neo-btn bg-white hover:bg-blue-50 text-blue-700 border border-slate-900 px-2.5 py-1 text-[11px] font-bold flex items-center gap-1">
                  <span>📷</span> Pratinjau Bukti
                </button>
              @else
                <span class="text-slate-400">-</span>
              @endif
            </td>
            <td class="p-3.5">
              @if($pay->payment_status === 'verified')
                <span class="bg-emerald-100 text-emerald-800 border border-emerald-400 px-2 py-0.5 text-[10px] font-black uppercase">Lunas (Terverifikasi)</span>
              @elseif($pay->payment_status === 'pending')
                <span class="bg-amber-100 text-amber-900 border border-amber-400 px-2 py-0.5 text-[10px] font-black uppercase">Menunggu Kasir</span>
              @else
                <span class="bg-rose-100 text-rose-800 border border-rose-400 px-2 py-0.5 text-[10px] font-black uppercase">Ditolak</span>
                @if($pay->rejection_note)
                  <div class="text-[10px] text-rose-600 mt-0.5">{{ $pay->rejection_note }}</div>
                @endif
              @endif
            </td>
            <td class="p-3.5 text-right">
              @if($pay->payment_status === 'pending' && $b)
                <div class="inline-flex items-center gap-1.5">
                  <!-- Accept Form -->
                  <form action="{{ route('cashier.verify', $b->id) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="decision" value="accept" />
                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 border border-slate-900 px-3 py-1.5 text-[11px] font-black uppercase text-slate-900 shadow-[2px_2px_0px_#0F172A]">
                      Terima ✓
                    </button>
                  </form>

                  <!-- Reject Button (Opens mini prompt) -->
                  <button type="button" onclick="rejectPaymentWithPrompt('{{ $b->id }}', '{{ $b->queue_number }}')" class="bg-rose-600 hover:bg-rose-700 text-white border border-slate-900 px-3 py-1.5 text-[11px] font-black uppercase shadow-[2px_2px_0px_#0F172A]">
                    Tolak ✕
                  </button>
                </div>
              @else
                <span class="text-slate-400 text-[11px] font-bold">
                  {{ $pay->verified_at ? 'Diproses: ' . \Carbon\Carbon::parse($pay->verified_at)->format('H:i') : 'Selesai' }}
                </span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="p-8 text-center text-slate-500 font-bold">Belum ada data pembayaran QRIS.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <!-- SECTION 2: CHECK-IN & ANTREAN HARI INI (FR-08) -->
  <section class="border-2 border-slate-900 bg-white shadow-[6px_6px_0px_#0F172A]">
    <div class="bg-blue-700 text-white px-5 py-3.5 font-black uppercase text-xs tracking-wider flex items-center justify-between">
      <span>2. Check-In &amp; Antrean Studio Hari Ini</span>
      <span class="font-mono text-blue-200 text-xs">{{ $todayBookings->count() }} Tamu Terjadwal</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-[#F5F4EE] border-b-2 border-slate-900 uppercase font-black text-slate-700">
            <th class="p-3.5">Antrean</th>
            <th class="p-3.5">Pelanggan</th>
            <th class="p-3.5">Slot Waktu</th>
            <th class="p-3.5">Tema Backdrop</th>
            <th class="p-3.5">Status Booking</th>
            <th class="p-3.5 text-right">Aksi Studio</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-slate-900">
          @forelse($todayBookings as $tb)
          <tr class="hover:bg-slate-50 transition-colors {{ $tb->status === 'in_studio' ? 'bg-amber-50 font-bold' : '' }}">
            <td class="p-3.5 font-mono font-black text-blue-700 text-sm">
              {{ $tb->queue_number }}
              <span class="text-[10px] font-mono {{ $tb->booking_type === 'walk_in' ? 'text-amber-700 font-bold' : 'text-slate-400' }}">
                [{{ strtoupper($tb->booking_type ?? 'online') }}]
              </span>
            </td>
            <td class="p-3.5">
              <div class="font-bold text-slate-900">{{ $tb->user->name }}</div>
              <div class="text-[11px] font-mono text-slate-500">{{ $tb->user->phone_whatsapp }}</div>
            </td>
            <td class="p-3.5 font-mono text-slate-700">
              {{ $tb->schedule?->formatted_time ?? '-' }}
            </td>
            <td class="p-3.5">
              {{ $tb->studio_backdrop }}
            </td>
            <td class="p-3.5">
              <span class="border px-2.5 py-1 text-[10px] font-black uppercase inline-block {{ $tb->status_badge_class }}">
                {{ $tb->status_label }}
              </span>
            </td>
            <td class="p-3.5 text-right space-x-1">
              @if(in_array($tb->status, ['confirmed', 'waiting_verification']))
                <form action="{{ route('cashier.checkin', $tb->id) }}" method="POST" class="inline">
                  @csrf
                  <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white border border-slate-900 px-3 py-1.5 font-black uppercase text-[11px] shadow-[2px_2px_0px_#0F172A]">
                    Check-In Tamu ➔
                  </button>
                </form>
              @endif

              @if($tb->status === 'checked_in')
                <form action="{{ route('cashier.call-specific', $tb->id) }}" method="POST" class="inline">
                  @csrf
                  <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-slate-900 border border-slate-900 px-3 py-1.5 font-black uppercase text-[11px] shadow-[2px_2px_0px_#0F172A]">
                    Panggil ke Studio 📢
                  </button>
                </form>
              @endif

              @if($tb->status === 'in_studio')
                <form action="{{ route('cashier.complete', $tb->id) }}" method="POST" class="inline">
                  @csrf
                  <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white border border-slate-900 px-3 py-1.5 font-black uppercase text-[11px] shadow-[2px_2px_0px_#0F172A]">
                    Selesai ✓
                  </button>
                </form>
              @endif

              <!-- WhatsApp Direct Reminder Link -->
              <a href="https://api.whatsapp.com/send?phone=628132674377&text=Halo%20kak%20{{ urlencode($tb->user->name) }},%20antrean%20Kothak%20Photo%20nomor%20{{ $tb->queue_number }}%20siap%20dipanggil%20masuk%20ke%20studio!" target="_blank" class="neo-btn bg-white hover:bg-slate-100 text-slate-900 px-2 py-1 text-[11px] font-bold border border-slate-900 inline-block" title="Hubungi Pelanggan via WhatsApp">
                💬 WA
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="p-8 text-center text-slate-500 font-bold">
              Tidak ada antrean studio untuk hari ini.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

</div>

<!-- MODAL 1: PREVIEW BUKTI TRANSFER QRIS -->
<div id="proofModal" class="fixed inset-0 bg-slate-900/80 z-50 hidden flex items-center justify-center p-4">
  <div class="neo-box bg-white max-w-lg w-full p-6 space-y-4">
    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3">
      <div>
        <span class="text-[10px] font-black uppercase text-blue-700">VERIFIKASI BUKTI QRIS</span>
        <h3 id="modalProofTitle" class="text-lg font-black text-slate-900">Antrean</h3>
      </div>
      <button onclick="closeProofModal()" class="text-xl font-black text-slate-500 hover:text-black">✕</button>
    </div>

    <div class="text-center bg-[#F5F4EE] border-2 border-slate-900 p-2">
      <img id="modalProofImage" src="#" alt="Bukti Transfer" class="max-h-80 mx-auto object-contain border" />
    </div>

    <div class="flex justify-between items-center text-xs">
      <div>Pelanggan: <strong id="modalProofCustomer">-</strong></div>
      <div>Nominal: <strong id="modalProofAmount" class="font-mono text-blue-700 font-black">-</strong></div>
    </div>

    <button onclick="closeProofModal()" class="w-full neo-btn bg-slate-900 text-white py-2 text-xs font-black uppercase">
      Tutup Pratinjau
    </button>
  </div>
</div>

<!-- MODAL 2: INPUT TAMU WALK-IN (FR-04, FR-08) -->
<div id="walkInModal" class="fixed inset-0 bg-slate-900/80 z-50 hidden flex items-center justify-center p-4">
  <div class="neo-box bg-white max-w-lg w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3">
      <div>
        <span class="text-[10px] font-black uppercase text-emerald-700">KASIR / OPERATOR STUDIO</span>
        <h3 class="text-xl font-black text-slate-900">Registrasi Tamu Walk-In (Di Lokasi)</h3>
      </div>
      <button onclick="closeWalkInModal()" class="text-xl font-black text-slate-500 hover:text-black">✕</button>
    </div>

    <form action="{{ route('cashier.walkin.store') }}" method="POST" class="space-y-4 text-xs">
      @csrf

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Nama Pelanggan Walk-In *</label>
        <input type="text" name="customer_name" required placeholder="Contoh: Budi Santoso" class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none" />
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">No. WhatsApp *</label>
        <input type="text" name="customer_phone" required placeholder="081234567890" class="w-full border-2 border-slate-900 p-2 font-mono font-bold focus:outline-none" />
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Pilih Paket Foto *</label>
        <select name="package_id" required class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none bg-white">
          @foreach($packages as $p)
            <option value="{{ $p->id }}">{{ $p->name }} — Rp {{ number_format($p->price, 0, ',', '.') }} ({{ $p->duration }} Menit)</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Pilih Slot Waktu Sesi *</label>
        <select name="schedule_id" required class="w-full border-2 border-slate-900 p-2 font-mono font-bold focus:outline-none bg-white">
          @foreach($todaySchedules as $s)
            <option value="{{ $s->id }}">{{ $s->formatted_time }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Pilihan Background</label>
        <select name="studio_backdrop" class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none bg-white">
          <option value="Electric Blue Backdrop">Electric Blue Backdrop (Signature KOTHaK)</option>
          <option value="Warm Cream Minimalist">Warm Cream Minimalist</option>
          <option value="Classic Neutral Grey">Classic Neutral Grey Studio</option>
          <option value="Butter Yellow Pop">Butter Yellow Pop Vibes</option>
        </select>
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Metode Pembayaran di Kasir *</label>
        <div class="flex gap-4">
          <label class="flex items-center gap-2 font-bold cursor-pointer">
            <input type="radio" name="payment_method" value="cash" checked class="accent-blue-700" />
            <span>Tunai / Cash di Kasir</span>
          </label>
          <label class="flex items-center gap-2 font-bold cursor-pointer">
            <input type="radio" name="payment_method" value="qris_static" class="accent-blue-700" />
            <span>Scan QRIS di Meja Kasir</span>
          </label>
        </div>
      </div>

      <div class="pt-3 border-t-2 border-slate-900 flex gap-2">
        <button type="submit" class="flex-1 neo-btn bg-emerald-500 hover:bg-emerald-600 text-slate-900 py-3 font-black uppercase tracking-wider text-xs">
          Simpan &amp; Masukkan ke Antrean ✓
        </button>
        <button type="button" onclick="closeWalkInModal()" class="neo-btn bg-white hover:bg-slate-100 px-4 py-3 font-bold uppercase text-xs">
          Batal
        </button>
      </div>
    </form>
  </div>
</div>

<!-- HIDDEN REJECT PROMPT FORM -->
<form id="rejectForm" action="" method="POST" class="hidden">
  @csrf
  <input type="hidden" name="decision" value="reject" />
  <input type="hidden" name="rejection_note" id="rejectNoteInput" value="" />
</form>

@push('scripts')
<script>
  function openProofModal(imgUrl, queueNum, customerName, amount) {
    document.getElementById('modalProofImage').src = imgUrl;
    document.getElementById('modalProofTitle').innerText = 'Antrean: ' + queueNum;
    document.getElementById('modalProofCustomer').innerText = customerName;
    document.getElementById('modalProofAmount').innerText = amount;
    document.getElementById('proofModal').classList.remove('hidden');
  }

  function closeProofModal() {
    document.getElementById('proofModal').classList.add('hidden');
  }

  function openWalkInModal() {
    document.getElementById('walkInModal').classList.remove('hidden');
  }

  function closeWalkInModal() {
    document.getElementById('walkInModal').classList.add('hidden');
  }

  function rejectPaymentWithPrompt(bookingId, queueNum) {
    const reason = prompt('Masukkan alasan penolakan pembayaran antrean ' + queueNum + ':', 'Bukti transfer tidak terbaca / nominal tidak sesuai.');
    if (reason !== null) {
      const form = document.getElementById('rejectForm');
      form.action = '/cashier/verify/' + bookingId;
      document.getElementById('rejectNoteInput').value = reason;
      form.submit();
    }
  }

  // Web Audio Chime Trigger
  function playChimeAudio() {
    try {
      const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(587.33, audioCtx.currentTime);
      osc.frequency.setValueAtTime(880.00, audioCtx.currentTime + 0.25);
      gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 1.0);
      osc.connect(gain);
      gain.connect(audioCtx.destination);
      osc.start();
      osc.stop(audioCtx.currentTime + 1.0);
    } catch(e) {}
  }
</script>
@endpush
@endsection
