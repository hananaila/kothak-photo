@extends('layouts.admin')

@section('page_title', 'Manajemen Booth Studio')

@section('content')
<div class="space-y-6 sm:space-y-8 max-w-7xl">

  <!-- TOP BAR -->
  <div class="bg-white rounded-3xl border border-[#E7E2D8] p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-soft">
    <div>
      <h3 class="text-xl font-heading font-black text-[#1A1A2E]">Daftar Bilik Booth Photobox</h3>
      <p class="text-xs text-slate-500 font-sans mt-0.5">
        Kelola bilik booth foto studio. Setiap perubahan dan penambahan booth di sini langsung otomatis tampil di halaman pelanggan serta dasbor kasir.
      </p>
    </div>
    <button type="button" onclick="openAddPackageModal()" class="bg-[#1E3A8A] hover:bg-[#2D2DB0] text-white px-4 py-2 text-xs font-heading font-bold uppercase tracking-wider flex items-center gap-1.5 shrink-0 rounded-xl shadow-xs transition-all cursor-pointer">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
      <span>Tambah Booth Baru</span>
    </button>
  </div>

  <!-- MINIMALIST CARDS: MENAMPILKAN SEMUA BILIK/BOOTH AKTIF MAUPUN NONAKTIF -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="adminPackageCardsContainer">
    @forelse($packages as $pkg)
    <div class="bg-white rounded-3xl border border-[#E7E2D8] p-6 sm:p-7 shadow-soft flex flex-col justify-between hover:border-[#1E3A8A]/40 transition-all duration-200 group relative {{ !$pkg->is_active ? 'opacity-70 bg-slate-50/80' : '' }}">
      
      <div>
        <!-- Top: Status Indicator -->
        <div class="flex items-center justify-end gap-2 mb-3">
          <div class="flex items-center gap-1.5 text-xs font-bold">
            @if($pkg->is_active)
              <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
              <span class="text-emerald-700 text-[11px] font-heading uppercase">Aktif</span>
            @else
              <span class="w-2 h-2 rounded-full bg-slate-400"></span>
              <span class="text-slate-500 text-[11px] font-heading uppercase">Nonaktif</span>
            @endif
          </div>
        </div>

        <!-- Title & Operational Hours -->
        <h4 class="font-heading font-black text-xl text-[#1A1A2E] leading-snug group-hover:text-[#1E3A8A] transition-colors">
          {{ $pkg->name }}
        </h4>
        <div class="flex items-center gap-1.5 text-xs font-mono font-medium text-slate-500 mt-1">
          <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
          <span>{{ $pkg->operational_hours ?: 'Buka 09.00 — 23.00 WIB' }}</span>
        </div>

        <!-- Pricing (Minimalist typography) -->
        <div class="mt-4 mb-4 flex items-baseline gap-1.5">
          <span class="text-3xl font-black font-mono text-[#1E3A8A] tracking-tight">
            Rp {{ number_format($pkg->price, 0, ',', '.') }}
          </span>
          <span class="text-xs text-slate-400 font-sans font-medium">/ sesi ({{ $pkg->duration }} menit)</span>
        </div>

        <!-- Specifications (Termasuk Output Cetak yang tampil di pelanggan) -->
        <div class="py-3.5 border-t border-slate-100 space-y-2 text-xs text-slate-600 font-sans">
          <div class="flex items-center justify-between">
            <span class="text-slate-400">Output Cetak:</span>
            <span class="font-bold text-[#1E3A8A] bg-[#E8EEF9] px-2.5 py-0.5 rounded-lg border border-[#1E3A8A]/15 text-[11px]">
              {{ $pkg->prints_included ?: '2 Strip + Soft File' }}
            </span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-slate-400">Total Reservasi:</span>
            <span class="font-mono font-bold text-[#1E3A8A]">{{ $pkg->bookings_count }} Antrean</span>
          </div>
        </div>
      </div>

      <!-- Action Buttons Row (Unified & Harmonized) -->
      <div class="pt-4 border-t border-slate-100 flex items-center gap-2">
        <button
          type="button"
          onclick="editPackageById({{ $pkg->id }})"
          class="flex-1 py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-heading font-bold transition-all text-center cursor-pointer"
        >
          Ubah
        </button>

        <form action="{{ route('admin.packages.toggle', $pkg->id) }}" method="POST" class="flex-1">
          @csrf
          <button
            type="submit"
            class="w-full py-2.5 px-3 rounded-xl text-xs font-heading font-bold transition-all border cursor-pointer {{ $pkg->is_active ? 'border-amber-200 bg-amber-50 text-amber-800 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}"
          >
            {{ $pkg->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
          </button>
        </form>

        <form action="{{ route('admin.packages.destroy', $pkg->id) }}" method="POST">
          @csrf
          @method('DELETE')
          <button
            type="button"
            onclick="confirmFormSubmit(this.form, 'Apakah Anda yakin ingin menghapus bilik booth {{ addslashes($pkg->name) }}? Tindakan ini tidak dapat dibatalkan.', 'Hapus Bilik Booth')"
            class="p-2.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 transition-all flex items-center justify-center shrink-0 cursor-pointer"
            title="Hapus Booth"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          </button>
        </form>
      </div>

    </div>
    @empty
    <div class="col-span-full bg-white rounded-3xl border border-dashed border-[#DDD7CC] p-12 text-center text-slate-500 font-medium">
      Belum ada data bilik booth foto. Silakan klik tombol "Tambah Booth Baru" di atas.
    </div>
    @endforelse
  </div>

</div>

<!-- MODAL: TAMBAH BOOTH BARU (LENGKAP SESUAI KEBUTUHAN TAMPILAN PELANGGAN) -->
<div id="addPackageModal" class="fixed inset-0 bg-[#1A1A2E]/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl border border-slate-200 max-w-lg w-full p-6 sm:p-7 space-y-4 max-h-[90vh] overflow-y-auto shadow-2xl">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <div>
        <h3 class="text-base font-heading font-black text-[#1A1A2E]">Tambah Booth Baru</h3>
        <p class="text-[11px] text-slate-400 font-sans">Semua isian akan langsung terhubung ke kartu pilihan pelanggan</p>
      </div>
      <button onclick="closeAddPackageModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold cursor-pointer">&times;</button>
    </div>

    <form action="{{ route('admin.packages.store') }}" method="POST" class="space-y-3.5 text-xs font-sans">
      @csrf

      <div>
        <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">
          Nama Bilik / Booth * <span class="text-slate-400 font-normal lowercase">(judul kartu pelanggan)</span>
        </label>
        <input type="text" name="name" required placeholder="Contoh: Hana Booth / Neon Box Booth" class="w-full rounded-xl border border-slate-300 p-2.5 font-bold bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]" />
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">
            Tarif Harga (IDR) *
          </label>
          <input type="number" name="price" required value="35000" min="0" step="1000" class="w-full rounded-xl border border-slate-300 p-2.5 font-mono font-bold bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]" />
        </div>
        <div>
          <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">
            Fasilitas Cetak &bull; Kanan Bawah *
          </label>
          <input type="text" name="prints_included" required value="2 Strip + Soft File" placeholder="Contoh: 2 Strip + Soft File" class="w-full rounded-xl border border-slate-300 p-2.5 font-bold text-[#1E3A8A] bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]" />
        </div>
      </div>

      <div>
        <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">
          Keterangan Jam Operasional *
        </label>
        <input type="text" name="operational_hours" required value="Buka 09.00 — 23.00 WIB" placeholder="Contoh: Buka 09.00 — 23.00 WIB" class="w-full rounded-xl border border-slate-300 p-2.5 font-medium bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]" />
      </div>

      <input type="hidden" name="max_participants" value="4" />
      <input type="hidden" name="duration" value="15" />
      <input type="hidden" name="category" value="BOOTH" />

      <div class="pt-3 border-t border-slate-100 flex items-center gap-2">
        <button type="button" onclick="closeAddPackageModal()" class="flex-1 py-2.5 rounded-xl font-heading font-bold uppercase text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">
          Batal
        </button>
        <button type="submit" class="flex-1 py-2.5 rounded-xl font-heading font-black uppercase text-xs bg-[#1E3A8A] hover:bg-[#2D2DB0] text-white shadow-xs cursor-pointer">
          Simpan
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: UBAH BOOTH -->
<div id="editPackageModal" class="fixed inset-0 bg-[#1A1A2E]/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl border border-slate-200 max-w-lg w-full p-6 sm:p-7 space-y-4 max-h-[90vh] overflow-y-auto shadow-2xl">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <div>
        <h3 class="text-base font-heading font-black text-[#1A1A2E]">Ubah Bilik Booth</h3>
        <p class="text-[11px] text-slate-400 font-sans">Perubahan akan langsung diperbarui di kartu pelanggan</p>
      </div>
      <button onclick="closeEditPackageModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold cursor-pointer">&times;</button>
    </div>

    <form id="editPackageForm" method="POST" class="space-y-3.5 text-xs font-sans">
      @csrf
      @method('PUT')

      <div>
        <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">Nama Bilik / Booth *</label>
        <input type="text" name="name" id="editPkgName" required class="w-full rounded-xl border border-slate-300 p-2.5 font-bold bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]" />
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">Tarif Harga (IDR) *</label>
          <input type="number" name="price" id="editPkgPrice" required min="0" step="1000" class="w-full rounded-xl border border-slate-300 p-2.5 font-mono font-bold bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]" />
        </div>
        <div>
          <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">Fasilitas Cetak &bull; Kanan Bawah *</label>
          <input type="text" name="prints_included" id="editPkgPrints" required class="w-full rounded-xl border border-slate-300 p-2.5 font-bold text-[#1E3A8A] bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]" />
        </div>
      </div>

      <div>
        <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">Keterangan Jam Operasional *</label>
        <input type="text" name="operational_hours" id="editPkgHours" required class="w-full rounded-xl border border-slate-300 p-2.5 font-medium bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]" />
      </div>

      <input type="hidden" name="max_participants" id="editPkgMax" value="4" />
      <input type="hidden" name="duration" id="editPkgDuration" value="15" />
      <input type="hidden" name="category" id="editPkgCat" value="BOOTH" />

      <div class="pt-3 border-t border-slate-100 flex items-center gap-2">
        <button type="button" onclick="closeEditPackageModal()" class="flex-1 py-2.5 rounded-xl font-heading font-bold uppercase text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">
          Batal
        </button>
        <button type="submit" class="flex-1 py-2.5 rounded-xl font-heading font-black uppercase text-xs bg-[#1E3A8A] hover:bg-[#2D2DB0] text-white shadow-xs cursor-pointer">
          Simpan
        </button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script id="packagesDataScript">
  let packagesMap = @json($packages->keyBy('id'));
</script>
<script>
  function openAddPackageModal() {
    const modal = document.getElementById('addPackageModal');
    if (modal) modal.classList.remove('hidden');
  }
  function closeAddPackageModal() {
    const modal = document.getElementById('addPackageModal');
    if (modal) modal.classList.add('hidden');
  }

  function editPackageById(id) {
    const pkg = packagesMap[id];
    if (!pkg) {
      console.error('Package not found for ID:', id);
      return;
    }
    openEditPackageModal(pkg);
  }

  function openEditPackageModal(pkg) {
    const form = document.getElementById('editPackageForm');
    form.action = '/admin/packages/' + pkg.id;

    if (document.getElementById('editPkgName')) document.getElementById('editPkgName').value = pkg.name || '';
    if (document.getElementById('editPkgDuration')) document.getElementById('editPkgDuration').value = pkg.duration || 15;
    if (document.getElementById('editPkgMax')) document.getElementById('editPkgMax').value = pkg.max_participants || 4;
    if (document.getElementById('editPkgPrice')) document.getElementById('editPkgPrice').value = Math.round(pkg.price || 0);
    if (document.getElementById('editPkgPrints')) document.getElementById('editPkgPrints').value = pkg.prints_included || '2 Strip + Soft File';
    if (document.getElementById('editPkgHours')) document.getElementById('editPkgHours').value = pkg.operational_hours || 'Buka 09.00 — 23.00 WIB';
    if (document.getElementById('editPkgCat')) document.getElementById('editPkgCat').value = pkg.category || 'BOOTH';

    const modal = document.getElementById('editPackageModal');
    if (modal) {
      modal.classList.remove('hidden');
    }
  }
  function closeEditPackageModal() {
    const modal = document.getElementById('editPackageModal');
    if (modal) {
      modal.classList.add('hidden');
    }
  }

  // REAL-TIME AUTO-UPDATE MANAJEMEN BOOTH ADMIN (Tanpa Reload Halaman)
  let isPkgRefreshing = false;

  function fetchLivePackages() {
    if (isPkgRefreshing) return;

    // Jangan refresh jika admin sedang membuka modal tambah atau ubah booth
    const addModal = document.getElementById('addPackageModal');
    const editModal = document.getElementById('editPackageModal');
    if ((addModal && !addModal.classList.contains('hidden')) || (editModal && !editModal.classList.contains('hidden'))) {
      return;
    }

    isPkgRefreshing = true;
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

      const newContainer = doc.getElementById('adminPackageCardsContainer');
      const curContainer = document.getElementById('adminPackageCardsContainer');
      if (newContainer && curContainer) {
        curContainer.innerHTML = newContainer.innerHTML;
      }

      // Update map data paket terbaru
      const newScript = doc.getElementById('packagesDataScript');
      if (newScript) {
        try {
          eval(newScript.textContent);
        } catch(e) {}
      }
    })
    .catch(() => {})
    .finally(() => {
      isPkgRefreshing = false;
    });
  }

  // Broadcast perubahan booth ke halaman pelanggan & dasbor kasir
  function broadcastBoothUpdate() {
    try {
      if ('BroadcastChannel' in window) {
        const bc = new BroadcastChannel('kothak_live_queue');
        bc.postMessage({ type: 'package_updated', time: Date.now() });
      }
      localStorage.setItem('kothak_queue_bump', JSON.stringify({ type: 'package_updated', time: Date.now() }));
    } catch(e) {}
  }

  // Pemicu saat admin simpan / nonaktifkan / hapus booth
  document.addEventListener('submit', function() {
    broadcastBoothUpdate();
  });

  // Cross-tab broadcast listener (0 ms respon saat ada perubahan)
  try {
    if ('BroadcastChannel' in window) {
      const liveQueueChannel = new BroadcastChannel('kothak_live_queue');
      liveQueueChannel.onmessage = fetchLivePackages;
    }
  } catch(e) {}
  window.addEventListener('storage', function(e) {
    if (e.key === 'kothak_queue_bump') fetchLivePackages();
  });

  // Polling data otomatis setiap 1.5 detik (Real-Time Ultra Responsif)
  setInterval(fetchLivePackages, 1500);
</script>
@endpush
@endsection
