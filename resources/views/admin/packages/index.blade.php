@extends('layouts.admin')

@section('page_title', 'Manajemen Paket Foto & Add-Ons (FR-07)')

@section('content')
<div class="space-y-8 max-w-7xl">

  <!-- SECTION 1: PAKET FOTO STUDIO -->
  <div class="space-y-4">
    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3">
      <div>
        <h3 class="text-xl font-black text-slate-900">Daftar Paket Foto Photobox Studio</h3>
        <p class="text-xs text-slate-600">Atur nama paket, deskripsi, durasi menit, harga IDR, dan kapasitas maksimal peserta.</p>
      </div>
      <button type="button" onclick="openAddPackageModal()" class="neo-btn bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 text-xs font-black uppercase tracking-wider flex items-center gap-1.5 shrink-0">
        <span>➕</span> Tambah Paket Foto
      </button>
    </div>

    <!-- Packages Table -->
    <div class="neo-box bg-white overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-[#F8FAFC] border-b-2 border-slate-900 uppercase font-black text-slate-700">
              <th class="p-3.5">Nama Paket</th>
              <th class="p-3.5">Durasi</th>
              <th class="p-3.5">Kapasitas</th>
              <th class="p-3.5">Harga</th>
              <th class="p-3.5">Kategori & Badge</th>
              <th class="p-3.5">Total Booking</th>
              <th class="p-3.5">Status</th>
              <th class="p-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y-2 divide-slate-900">
            @forelse($packages as $pkg)
            <tr class="hover:bg-slate-50 transition-colors {{ !$pkg->is_active ? 'bg-slate-100 opacity-70' : '' }}">
              <td class="p-3.5">
                <div class="font-black text-slate-900 text-sm">{{ $pkg->name }}</div>
                <div class="text-[11px] text-slate-500 max-w-xs truncate">{{ $pkg->description }}</div>
              </td>
              <td class="p-3.5 font-mono font-bold">{{ $pkg->duration }} Menit</td>
              <td class="p-3.5 font-bold">1 - {{ $pkg->max_participants }} Orang</td>
              <td class="p-3.5 font-mono font-black text-blue-700">Rp {{ number_format($pkg->price, 0, ',', '.') }}</td>
              <td class="p-3.5">
                <span class="bg-blue-100 text-blue-900 border border-blue-400 px-2 py-0.5 text-[10px] font-bold uppercase">{{ $pkg->category }}</span>
                @if($pkg->badge)
                  <span class="bg-amber-100 text-amber-900 border border-amber-400 px-2 py-0.5 text-[10px] font-bold uppercase ml-1">{{ $pkg->badge }}</span>
                @endif
              </td>
              <td class="p-3.5 font-mono font-bold">{{ $pkg->bookings_count }}x</td>
              <td class="p-3.5">
                @if($pkg->is_active)
                  <span class="bg-emerald-50 text-emerald-700 border border-emerald-300 px-2 py-0.5 text-[10px] font-black uppercase">Aktif</span>
                @else
                  <span class="bg-rose-50 text-rose-700 border border-rose-300 px-2 py-0.5 text-[10px] font-black uppercase">Non-Aktif</span>
                @endif
              </td>
              <td class="p-3.5 text-right space-x-1">
                <button
                  type="button"
                  onclick='openEditPackageModal(@json($pkg))'
                  class="neo-btn bg-white hover:bg-slate-100 text-slate-900 px-2.5 py-1 text-[11px] font-bold border border-slate-900 inline-block"
                >
                  Ubah
                </button>

                <form action="{{ route('admin.packages.toggle', $pkg->id) }}" method="POST" class="inline">
                  @csrf
                  <button type="submit" class="neo-btn {{ $pkg->is_active ? 'bg-amber-400' : 'bg-emerald-500' }} text-slate-900 px-2.5 py-1 text-[11px] font-bold border border-slate-900 inline-block">
                    {{ $pkg->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                  </button>
                </form>

                <form action="{{ route('admin.packages.destroy', $pkg->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket foto ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="neo-btn bg-rose-600 hover:bg-rose-700 text-white px-2 py-1 text-[11px] font-bold border border-slate-900 inline-block">
                    Hapus
                  </button>
                </form>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="p-8 text-center text-slate-500 font-bold">Belum ada paket foto.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- SECTION 2: LAYANAN TAMBAHAN (ADD-ONS) -->
  <div class="space-y-4">
    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3">
      <div>
        <h3 class="text-xl font-black text-slate-900">Layanan Tambahan (Add-Ons)</h3>
        <p class="text-xs text-slate-600">Cetak tambahan, drive all soft files, extra orang, sewa properti khusus.</p>
      </div>
      <button type="button" onclick="openAddOnModal()" class="neo-btn bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-xs font-black uppercase tracking-wider flex items-center gap-1.5 shrink-0">
        <span>➕</span> Tambah Add-On
      </button>
    </div>

    <div class="neo-box bg-white overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-[#F8FAFC] border-b-2 border-slate-900 uppercase font-black text-slate-700">
              <th class="p-3.5">Nama Layanan Tambahan</th>
              <th class="p-3.5">Deskripsi</th>
              <th class="p-3.5">Harga Tambahan</th>
              <th class="p-3.5">Total Digunakan</th>
              <th class="p-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y-2 divide-slate-900">
            @forelse($addOns as $ao)
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="p-3.5 font-black text-slate-900 text-sm">{{ $ao->name }}</td>
              <td class="p-3.5 text-slate-600">{{ $ao->description }}</td>
              <td class="p-3.5 font-mono font-black text-blue-700">Rp {{ number_format($ao->price, 0, ',', '.') }}</td>
              <td class="p-3.5 font-mono font-bold">{{ $ao->booking_add_ons_count }}x Reservasi</td>
              <td class="p-3.5 text-right">
                <form action="{{ route('admin.addons.destroy', $ao->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus add-on ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="neo-btn bg-rose-600 hover:bg-rose-700 text-white px-2 py-1 text-[11px] font-bold border border-slate-900">
                    Hapus
                  </button>
                </form>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="p-8 text-center text-slate-500 font-bold">Belum ada layanan tambahan.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- MODAL: TAMBAH PAKET FOTO -->
<div id="addPackageModal" class="fixed inset-0 bg-slate-900/80 z-50 hidden flex items-center justify-center p-4">
  <div class="neo-box bg-white max-w-lg w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3">
      <h3 class="text-lg font-black text-slate-900">Tambah Paket Foto Baru</h3>
      <button onclick="closeAddPackageModal()" class="text-xl font-black text-slate-500 hover:text-black">✕</button>
    </div>

    <form action="{{ route('admin.packages.store') }}" method="POST" class="space-y-3 text-xs">
      @csrf

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Nama Paket Foto *</label>
        <input type="text" name="name" required placeholder="Contoh: Kothak Signature Box" class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none" />
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Deskripsi Lengkap *</label>
        <textarea name="description" rows="3" required placeholder="Jelaskan fasilitas: cetak, soft file, properti..." class="w-full border-2 border-slate-900 p-2 font-medium focus:outline-none"></textarea>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-black uppercase text-slate-900 mb-1">Durasi (Menit) *</label>
          <input type="number" name="duration" required value="15" min="5" max="120" class="w-full border-2 border-slate-900 p-2 font-mono font-bold focus:outline-none" />
        </div>
        <div>
          <label class="block font-black uppercase text-slate-900 mb-1">Kapasitas Maksimal *</label>
          <input type="number" name="max_participants" required value="4" min="1" max="20" class="w-full border-2 border-slate-900 p-2 font-mono font-bold focus:outline-none" />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-black uppercase text-slate-900 mb-1">Harga (IDR) *</label>
          <input type="number" name="price" required placeholder="35000" min="0" step="1000" class="w-full border-2 border-slate-900 p-2 font-mono font-bold focus:outline-none" />
        </div>
        <div>
          <label class="block font-black uppercase text-slate-900 mb-1">Kategori *</label>
          <select name="category" required class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none bg-white">
            <option value="SOLO_DUO">Solo / Duo (1-2 Org)</option>
            <option value="GROUP">Grup & Bestie (3-6 Org)</option>
            <option value="BUNDLING">Bundling Cafe / Kopi</option>
          </select>
        </div>
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Badge Promosi (Opsional)</label>
        <input type="text" name="badge" placeholder="Contoh: BEST SELLER, POPULER, HEMAT" class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none" />
      </div>

      <div class="pt-3 border-t-2 border-slate-900 flex gap-2">
        <button type="submit" class="flex-1 neo-btn bg-blue-700 hover:bg-blue-800 text-white py-2.5 font-black uppercase text-xs">
          Simpan Paket
        </button>
        <button type="button" onclick="closeAddPackageModal()" class="neo-btn bg-white hover:bg-slate-100 px-4 py-2.5 font-bold uppercase text-xs">
          Batal
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: UBAH PAKET FOTO -->
<div id="editPackageModal" class="fixed inset-0 bg-slate-900/80 z-50 hidden flex items-center justify-center p-4">
  <div class="neo-box bg-white max-w-lg w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3">
      <h3 class="text-lg font-black text-slate-900">Ubah Paket Foto</h3>
      <button onclick="closeEditPackageModal()" class="text-xl font-black text-slate-500 hover:text-black">✕</button>
    </div>

    <form id="editPackageForm" method="POST" class="space-y-3 text-xs">
      @csrf
      @method('PUT')

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Nama Paket Foto *</label>
        <input type="text" name="name" id="editPkgName" required class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none" />
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Deskripsi Lengkap *</label>
        <textarea name="description" id="editPkgDesc" rows="3" required class="w-full border-2 border-slate-900 p-2 font-medium focus:outline-none"></textarea>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-black uppercase text-slate-900 mb-1">Durasi (Menit) *</label>
          <input type="number" name="duration" id="editPkgDuration" required min="5" max="120" class="w-full border-2 border-slate-900 p-2 font-mono font-bold focus:outline-none" />
        </div>
        <div>
          <label class="block font-black uppercase text-slate-900 mb-1">Kapasitas Maksimal *</label>
          <input type="number" name="max_participants" id="editPkgMax" required min="1" max="20" class="w-full border-2 border-slate-900 p-2 font-mono font-bold focus:outline-none" />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-black uppercase text-slate-900 mb-1">Harga (IDR) *</label>
          <input type="number" name="price" id="editPkgPrice" required min="0" step="1000" class="w-full border-2 border-slate-900 p-2 font-mono font-bold focus:outline-none" />
        </div>
        <div>
          <label class="block font-black uppercase text-slate-900 mb-1">Kategori *</label>
          <select name="category" id="editPkgCat" required class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none bg-white">
            <option value="SOLO_DUO">Solo / Duo (1-2 Org)</option>
            <option value="GROUP">Grup & Bestie (3-6 Org)</option>
            <option value="BUNDLING">Bundling Cafe / Kopi</option>
          </select>
        </div>
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Badge Promosi</label>
        <input type="text" name="badge" id="editPkgBadge" class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none" />
      </div>

      <div class="pt-3 border-t-2 border-slate-900 flex gap-2">
        <button type="submit" class="flex-1 neo-btn bg-blue-700 hover:bg-blue-800 text-white py-2.5 font-black uppercase text-xs">
          Simpan Perubahan
        </button>
        <button type="button" onclick="closeEditPackageModal()" class="neo-btn bg-white hover:bg-slate-100 px-4 py-2.5 font-bold uppercase text-xs">
          Batal
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: TAMBAH ADD-ON -->
<div id="addOnModal" class="fixed inset-0 bg-slate-900/80 z-50 hidden flex items-center justify-center p-4">
  <div class="neo-box bg-white max-w-md w-full p-6 space-y-4">
    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3">
      <h3 class="text-lg font-black text-slate-900">Tambah Layanan Ekstra (Add-On)</h3>
      <button onclick="closeAddOnModal()" class="text-xl font-black text-slate-500 hover:text-black">✕</button>
    </div>

    <form action="{{ route('admin.addons.store') }}" method="POST" class="space-y-3 text-xs">
      @csrf

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Nama Add-On *</label>
        <input type="text" name="name" required placeholder="Contoh: Cetak Strip 4R Tambahan" class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none" />
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Deskripsi Singkat</label>
        <input type="text" name="description" placeholder="Contoh: Tambahan 2 lembar cetak photo strip" class="w-full border-2 border-slate-900 p-2 font-medium focus:outline-none" />
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Harga Tambahan (IDR) *</label>
        <input type="number" name="price" required placeholder="10000" min="0" step="1000" class="w-full border-2 border-slate-900 p-2 font-mono font-bold focus:outline-none" />
      </div>

      <div class="pt-3 border-t-2 border-slate-900 flex gap-2">
        <button type="submit" class="flex-1 neo-btn bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 font-black uppercase text-xs">
          Simpan Add-On
        </button>
        <button type="button" onclick="closeAddOnModal()" class="neo-btn bg-white hover:bg-slate-100 px-4 py-2.5 font-bold uppercase text-xs">
          Batal
        </button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  function openAddPackageModal() { document.getElementById('addPackageModal').classList.remove('hidden'); }
  function closeAddPackageModal() { document.getElementById('addPackageModal').classList.add('hidden'); }

  function openEditPackageModal(pkg) {
    const form = document.getElementById('editPackageForm');
    form.action = '/admin/packages/' + pkg.id;

    document.getElementById('editPkgName').value = pkg.name;
    document.getElementById('editPkgDesc').value = pkg.description;
    document.getElementById('editPkgDuration').value = pkg.duration;
    document.getElementById('editPkgMax').value = pkg.max_participants;
    document.getElementById('editPkgPrice').value = pkg.price;
    document.getElementById('editPkgCat').value = pkg.category;
    document.getElementById('editPkgBadge').value = pkg.badge || '';

    document.getElementById('editPackageModal').classList.remove('hidden');
  }
  function closeEditPackageModal() { document.getElementById('editPackageModal').classList.add('hidden'); }

  function openAddOnModal() { document.getElementById('addOnModal').classList.remove('hidden'); }
  function closeAddOnModal() { document.getElementById('addOnModal').classList.add('hidden'); }
</script>
@endpush
@endsection
