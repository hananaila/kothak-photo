@extends('layouts.admin')

@section('page_title', 'Manajemen Pengguna & Akun Studio (FR-01)')

@section('content')
<div class="space-y-6 max-w-7xl">

  <!-- TOP ACTIONS & FILTERS -->
  <div class="neo-box bg-white p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3 text-xs">
      <div>
        <label for="role" class="block font-black uppercase text-slate-700 text-[10px]">Filter Peran:</label>
        <select id="role" name="role" onchange="this.form.submit()" class="border-2 border-slate-900 px-3 py-1.5 font-bold bg-[#F8FAFC]">
          <option value="">Semua Peran</option>
          <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Pelanggan</option>
          <option value="cashier" {{ request('role') === 'cashier' ? 'selected' : '' }}>Kasir</option>
          <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin Studio</option>
        </select>
      </div>

      <div>
        <label for="search" class="block font-black uppercase text-slate-700 text-[10px]">Cari Nama / Email:</label>
        <div class="flex gap-1">
          <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Cari pengguna..." class="border-2 border-slate-900 px-3 py-1.5 font-bold bg-[#F8FAFC]" />
          <button type="submit" class="neo-btn bg-slate-900 text-white px-3 py-1 font-black text-xs">Cari</button>
        </div>
      </div>
    </form>

    <button type="button" onclick="openAddUserModal()" class="neo-btn bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 text-xs font-black uppercase tracking-wider flex items-center gap-1.5 shrink-0">
      <span>➕</span> Tambah Pengguna Baru
    </button>
  </div>

  <!-- USERS TABLE -->
  <div class="neo-box bg-white overflow-hidden">
    <div class="bg-slate-900 text-white px-5 py-3.5 font-black uppercase text-xs tracking-wider flex items-center justify-between">
      <span>Daftar Pengguna Terdaftar</span>
      <span class="font-mono text-slate-400 text-xs">{{ $users->total() }} Pengguna</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-[#F8FAFC] border-b-2 border-slate-900 uppercase font-black text-slate-700">
            <th class="p-3.5">ID</th>
            <th class="p-3.5">Nama & Kontak</th>
            <th class="p-3.5">Email</th>
            <th class="p-3.5">Peran (Role)</th>
            <th class="p-3.5">Total Booking</th>
            <th class="p-3.5">Status Akun</th>
            <th class="p-3.5 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-slate-900">
          @forelse($users as $u)
          <tr class="hover:bg-slate-50 transition-colors {{ !$u->is_active ? 'bg-slate-100 opacity-70' : '' }}">
            <td class="p-3.5 font-mono text-slate-500 font-bold">#{{ $u->id }}</td>
            <td class="p-3.5">
              <div class="font-black text-slate-900 text-sm">{{ $u->name }}</div>
              <div class="text-[11px] font-mono text-slate-500">{{ $u->phone_whatsapp ?? '-' }}</div>
            </td>
            <td class="p-3.5 font-mono text-slate-700">{{ $u->email }}</td>
            <td class="p-3.5">
              @if($u->role === 'admin')
                <span class="bg-amber-100 text-amber-900 border border-amber-400 px-2.5 py-0.5 text-[10px] font-black uppercase">Admin Studio</span>
              @elseif($u->role === 'cashier')
                <span class="bg-emerald-100 text-emerald-900 border border-emerald-400 px-2.5 py-0.5 text-[10px] font-black uppercase">Kasir / Operator</span>
              @else
                <span class="bg-blue-100 text-blue-900 border border-blue-400 px-2.5 py-0.5 text-[10px] font-black uppercase">Pelanggan</span>
              @endif
            </td>
            <td class="p-3.5 font-mono font-bold">{{ $u->bookings_count }} Reservasi</td>
            <td class="p-3.5">
              @if($u->is_active)
                <span class="bg-emerald-50 text-emerald-700 border border-emerald-300 px-2 py-0.5 text-[10px] font-black uppercase">Aktif</span>
              @else
                <span class="bg-rose-50 text-rose-700 border border-rose-300 px-2 py-0.5 text-[10px] font-black uppercase">Non-Aktif</span>
              @endif
            </td>
            <td class="p-3.5 text-right space-x-1">
              <!-- Edit Button -->
              <button
                type="button"
                onclick='openEditUserModal(@json($u))'
                class="neo-btn bg-white hover:bg-slate-100 text-slate-900 px-2.5 py-1 text-[11px] font-bold border border-slate-900 inline-block"
              >
                Ubah
              </button>

              <!-- Toggle Active Button (FR-01) -->
              <form action="{{ route('admin.users.toggle', $u->id) }}" method="POST" class="inline">
                @csrf
                <button
                  type="submit"
                  class="neo-btn {{ $u->is_active ? 'bg-amber-400 hover:bg-amber-500 text-slate-900' : 'bg-emerald-500 hover:bg-emerald-600 text-slate-900' }} px-2.5 py-1 text-[11px] font-bold border border-slate-900 inline-block"
                  title="{{ $u->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                >
                  {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
              </form>

              <!-- Delete Button -->
              <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna ini?')">
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
            <td colspan="7" class="p-8 text-center text-slate-500 font-bold">
              Tidak ada pengguna yang cocok dengan pencarian.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="p-4 border-t-2 border-slate-900 bg-[#F8FAFC]">
      {{ $users->links() }}
    </div>
  </div>

</div>

<!-- MODAL: TAMBAH PENGGUNA BARU -->
<div id="addUserModal" class="fixed inset-0 bg-slate-900/80 z-50 hidden flex items-center justify-center p-4">
  <div class="neo-box bg-white max-w-md w-full p-6 space-y-4">
    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3">
      <h3 class="text-lg font-black text-slate-900">Tambah Akun Pengguna Baru</h3>
      <button onclick="closeAddUserModal()" class="text-xl font-black text-slate-500 hover:text-black">✕</button>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-3 text-xs">
      @csrf

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Nama Lengkap *</label>
        <input type="text" name="name" required class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none" />
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Alamat Email *</label>
        <input type="email" name="email" required class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none" />
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Nomor WhatsApp *</label>
        <input type="text" name="phone_whatsapp" required placeholder="08xxxxxxxxxx" class="w-full border-2 border-slate-900 p-2 font-mono font-bold focus:outline-none" />
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Peran (Role) *</label>
        <select name="role" required class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none bg-white">
          <option value="customer">Pelanggan (Customer)</option>
          <option value="cashier">Kasir / Operator Studio</option>
          <option value="admin">Admin Studio (Full Akses)</option>
        </select>
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Kata Sandi Awal *</label>
        <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none" />
      </div>

      <div class="pt-3 border-t-2 border-slate-900 flex gap-2">
        <button type="submit" class="flex-1 neo-btn bg-blue-700 hover:bg-blue-800 text-white py-2.5 font-black uppercase text-xs">
          Simpan Akun
        </button>
        <button type="button" onclick="closeAddUserModal()" class="neo-btn bg-white hover:bg-slate-100 px-4 py-2.5 font-bold uppercase text-xs">
          Batal
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: UBAH DATA PENGGUNA -->
<div id="editUserModal" class="fixed inset-0 bg-slate-900/80 z-50 hidden flex items-center justify-center p-4">
  <div class="neo-box bg-white max-w-md w-full p-6 space-y-4">
    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3">
      <h3 class="text-lg font-black text-slate-900">Ubah Data Pengguna</h3>
      <button onclick="closeEditUserModal()" class="text-xl font-black text-slate-500 hover:text-black">✕</button>
    </div>

    <form id="editUserForm" method="POST" class="space-y-3 text-xs">
      @csrf
      @method('PUT')

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Nama Lengkap *</label>
        <input type="text" name="name" id="editName" required class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none" />
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Alamat Email *</label>
        <input type="email" name="email" id="editEmail" required class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none" />
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Nomor WhatsApp *</label>
        <input type="text" name="phone_whatsapp" id="editPhone" required class="w-full border-2 border-slate-900 p-2 font-mono font-bold focus:outline-none" />
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Peran (Role) *</label>
        <select name="role" id="editRole" required class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none bg-white">
          <option value="customer">Pelanggan (Customer)</option>
          <option value="cashier">Kasir / Operator Studio</option>
          <option value="admin">Admin Studio (Full Akses)</option>
        </select>
      </div>

      <div>
        <label class="block font-black uppercase text-slate-900 mb-1">Ganti Kata Sandi (Opsional)</label>
        <input type="password" name="password" minlength="6" placeholder="Biarkan kosong jika tidak diubah" class="w-full border-2 border-slate-900 p-2 font-bold focus:outline-none" />
      </div>

      <div class="pt-3 border-t-2 border-slate-900 flex gap-2">
        <button type="submit" class="flex-1 neo-btn bg-blue-700 hover:bg-blue-800 text-white py-2.5 font-black uppercase text-xs">
          Simpan Perubahan
        </button>
        <button type="button" onclick="closeEditUserModal()" class="neo-btn bg-white hover:bg-slate-100 px-4 py-2.5 font-bold uppercase text-xs">
          Batal
        </button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  function openAddUserModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
  }
  function closeAddUserModal() {
    document.getElementById('addUserModal').classList.add('hidden');
  }

  function openEditUserModal(user) {
    const form = document.getElementById('editUserForm');
    form.action = '/admin/users/' + user.id;

    document.getElementById('editName').value = user.name;
    document.getElementById('editEmail').value = user.email;
    document.getElementById('editPhone').value = user.phone_whatsapp || '';
    document.getElementById('editRole').value = user.role;

    document.getElementById('editUserModal').classList.remove('hidden');
  }
  function closeEditUserModal() {
    document.getElementById('editUserModal').classList.add('hidden');
  }
</script>
@endpush
@endsection
