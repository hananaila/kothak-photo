@extends('layouts.app')

@section('title', 'Checkout & Pembayaran QRIS — KOTHaK Photo')

@section('content')
<div class="max-w-4xl mx-auto">
  <div class="border-2 border-slate-900 bg-white p-5 sm:p-7 shadow-[6px_6px_0px_#1D4ED8]">

    <!-- HEADER -->
    <div class="border-b-2 border-slate-900 pb-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div>
        <span class="text-xs font-black uppercase tracking-widest text-blue-700">LANGKAH 3 DARI 3 • PEMBAYARAN QRIS STATIS</span>
        <h1 class="text-2xl sm:text-3xl font-black mt-1 text-slate-900">Konfirmasi & Upload Bukti QRIS</h1>
      </div>
      <div class="bg-blue-700 text-white font-mono font-black text-xs sm:text-sm px-4 py-2 border-2 border-slate-900 shadow-[2px_2px_0px_#0F172A] shrink-0 text-center">
        NO. ANTREAN: {{ $booking->queue_number }}
      </div>
    </div>

    <!-- MAIN TWO-COLUMN LAYOUT -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">

      <!-- LEFT: ORDER DETAILS -->
      <div class="bg-[#F5F4EE] border-2 border-slate-900 p-5 space-y-3 text-xs shadow-[3px_3px_0px_#0F172A]">
        <div class="font-black uppercase tracking-wider text-blue-700 border-b-2 border-slate-900 pb-2 flex items-center justify-between">
          <span>RINCIAN PESANAN</span>
          <span class="font-mono text-[11px] text-slate-600">ID: #{{ $booking->id }}</span>
        </div>

        <div class="flex justify-between py-1 border-b border-slate-200">
          <span class="text-slate-600 font-bold">Pelanggan:</span>
          <strong class="text-slate-900 text-right">{{ $booking->user->name }}</strong>
        </div>

        <div class="flex justify-between py-1 border-b border-slate-200">
          <span class="text-slate-600 font-bold">WhatsApp:</span>
          <strong class="font-mono text-slate-900 text-right">{{ $booking->user->phone_whatsapp }}</strong>
        </div>

        <div class="flex justify-between py-1 border-b border-slate-200">
          <span class="text-slate-600 font-bold">Paket Foto:</span>
          <strong class="text-slate-900 text-right">{{ $booking->package->name }}</strong>
        </div>

        <div class="flex justify-between py-1 border-b border-slate-200">
          <span class="text-slate-600 font-bold">Tanggal:</span>
          <strong class="font-mono text-right">{{ \Carbon\Carbon::parse($booking->schedule->date)->translatedFormat('d F Y') }}</strong>
        </div>

        <div class="flex justify-between py-1 border-b border-slate-200">
          <span class="text-slate-600 font-bold">Slot Waktu:</span>
          <strong class="font-mono text-blue-700 text-right">{{ $booking->schedule->formatted_time }}</strong>
        </div>

        <div class="flex justify-between py-1 border-b border-slate-200">
          <span class="text-slate-600 font-bold">Studio Backdrop:</span>
          <strong class="text-right">{{ $booking->studio_backdrop }}</strong>
        </div>

        <!-- Add-ons list if any -->
        @if($booking->bookingAddOns->isNotEmpty())
        <div class="py-1 border-b border-slate-200 space-y-1">
          <div class="text-slate-600 font-bold">Layanan Tambahan:</div>
          @foreach($booking->bookingAddOns as $bao)
          <div class="flex justify-between text-[11px] pl-2 text-slate-700">
            <span>• {{ $bao->addOn->name }}</span>
            <span class="font-mono">+ Rp {{ number_format($bao->subtotal, 0, ',', '.') }}</span>
          </div>
          @endforeach
        </div>
        @endif

        <div class="border-t-2 border-slate-900 pt-3 flex justify-between items-baseline">
          <span class="font-black uppercase text-sm">Total Tagihan:</span>
          <span class="text-2xl font-black font-mono text-blue-700">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
        </div>

        <div class="bg-blue-50 border border-blue-200 p-2.5 text-[11px] text-blue-900 font-medium">
          💡 <strong>Petunjuk:</strong> Pindai QRIS Statis di samping dengan aplikasi pembayaran favoritmu (GoPay, OVO, ShopeePay, Dana, BCA, Livin Mandiri, dll), lalu unggah bukti transfer.
        </div>
      </div>

      <!-- RIGHT: QRIS CODE & PROOF UPLOAD (FR-05) -->
      <div class="space-y-5">
        <!-- QRIS DISPLAY BOX -->
        <div class="border-2 border-slate-900 p-5 text-center bg-white shadow-[3px_3px_0px_#0F172A]">
          <div class="text-xs font-black uppercase tracking-wider text-slate-900">KOTHaK CAFE &amp; PHOTO STUDIO</div>
          <div class="text-[11px] font-mono text-blue-700 font-bold mt-0.5">NMID: ID2026KOTHAKPBG01</div>
          <div class="text-[10px] text-slate-500 mb-2">Standar Pembayaran QRIS Bank Indonesia</div>

          <div class="my-3 flex justify-center">
            <img
              src="{{ asset('assets/img/qris-kothak.png') }}"
              alt="QRIS Statis KOTHaK Photo"
              class="w-44 h-44 sm:w-52 sm:h-52 object-contain border-2 border-slate-900 p-2 bg-white"
            />
          </div>

          <div class="text-xs text-slate-700 font-bold">
            Nominal Transfer Tepat: <strong class="text-blue-700 font-mono text-sm">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong>
          </div>
        </div>

        <!-- UPLOAD FORM -->
        <form action="{{ route('payment.upload', $booking->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3 bg-[#F5F4EE] border-2 border-slate-900 p-4 shadow-[3px_3px_0px_#0F172A]">
          @csrf
          <div>
            <label class="block text-xs font-black uppercase tracking-wider text-slate-900 mb-1.5">
              Upload Foto Bukti Transfer (JPG / PNG / WEBP) *
            </label>
            <input
              type="file"
              name="proof_image"
              id="proofImageInput"
              accept="image/jpeg,image/png,image/webp"
              required
              onchange="previewProofImage(event)"
              class="block w-full text-xs border-2 border-slate-900 bg-white p-2 file:mr-2 file:py-1 file:px-3 file:border-2 file:border-slate-900 file:font-black file:uppercase file:bg-blue-100 file:text-blue-900 file:text-xs cursor-pointer"
            />
            <div class="text-[10px] text-slate-500 mt-1">Ukuran maksimal file: 5 MB</div>
          </div>

          <!-- Image Preview -->
          <div id="imagePreviewContainer" class="hidden border-2 border-slate-900 bg-white p-2">
            <div class="text-[10px] font-bold text-slate-500 uppercase mb-1">Pratinjau Bukti:</div>
            <img id="imagePreview" src="#" alt="Pratinjau Bukti Transfer" class="max-h-40 mx-auto object-contain border" />
          </div>

          <button type="submit" class="w-full neo-btn bg-blue-700 hover:bg-blue-800 text-white border-2 border-slate-900 py-3 px-4 text-xs font-black uppercase tracking-widest shadow-[4px_4px_0px_#0F172A]">
            Kirim Bukti Pembayaran ➔
          </button>
        </form>
      </div>

    </div>
  </div>
</div>

@push('scripts')
<script>
  function previewProofImage(event) {
    const file = event.target.files[0];
    if (file) {
      const container = document.getElementById('imagePreviewContainer');
      const img = document.getElementById('imagePreview');
      img.src = URL.createObjectURL(file);
      container.classList.remove('hidden');
    }
  }
</script>
@endpush
@endsection
