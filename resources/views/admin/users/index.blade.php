@extends('layouts.admin')

@section('page_title', 'Manajemen Staf & Pengguna Studio')

@section('content')
<div class="space-y-6 sm:space-y-8 max-w-7xl">

  <!-- TOP ACTIONS & FILTERS -->
  <div class="bg-white rounded-3xl border border-slate-200/80 p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-soft">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3 text-xs">
      <div>
        <label for="role" class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">Filter Peran:</label>
        <select id="role" name="role" onchange="this.form.submit()" class="rounded-xl border border-slate-300 px-3.5 py-1.5 font-bold bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
          <option value="">Semua Peran</option>
          <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin Studio</option>
          <option value="cashier" {{ request('role') === 'cashier' ? 'selected' : '' }}>Kasir</option>
          <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Pelanggan</option>
        </select>
      </div>

      <div>
        <label for="search" class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">Cari Nama / No. WhatsApp:</label>
        <div class="flex gap-1.5">
          <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Cari nama atau WhatsApp..." class="rounded-xl border border-slate-300 px-3.5 py-1.5 font-bold bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]" />
          <button type="submit" class="bg-[#1E3A8A] hover:bg-[#2D2DB0] text-white px-4 py-1.5 font-heading font-bold text-xs rounded-xl shadow-sm transition-all">
            Cari
          </button>
        </div>
      </div>
    </form>

    <button type="button" onclick="openAddUserModal()" class="bg-[#1E3A8A] hover:bg-[#2D2DB0] text-white px-4 py-2 text-xs font-heading font-bold uppercase tracking-wider flex items-center gap-1.5 shrink-0 rounded-xl shadow-xs transition-all">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
      <span>Tambah Akun Staf</span>
    </button>
  </div>

  <!-- USERS TABLE -->
  <div class="bg-white rounded-3xl border border-slate-200/80 shadow-soft overflow-hidden">
    <div class="bg-[#1E3A8A] text-white px-6 py-4 font-heading font-bold uppercase text-xs tracking-wider flex items-center justify-between">
      <span>Daftar Akun Staf &amp; Pengguna</span>
      <span class="font-mono text-white/80 text-xs">{{ $users->total() }} Akun</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 uppercase font-heading font-bold text-slate-700 text-[11px]">
            <th class="p-4 pl-6">ID</th>
            <th class="p-4">Pengguna &amp; Kontak</th>
            <th class="p-4">Peran (Role)</th>
            <th class="p-4">Total Antrean</th>
            <th class="p-4">Status Akun</th>
            <th class="p-4 pr-6 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody id="adminUserTableBody" class="divide-y divide-slate-100">
          @forelse($users as $u)
          <tr class="hover:bg-slate-50/80 transition-colors {{ !$u->is_active ? 'bg-slate-100/60 opacity-70' : '' }}">
            <td class="p-4 pl-6 font-mono text-slate-400 font-bold">#{{ $u->id }}</td>
            <td class="p-4">
              <div class="font-heading font-bold text-slate-900 text-sm">{{ $u->name }}</div>
              <div class="text-[11px] font-mono text-slate-500 flex items-center gap-1 mt-0.5">
                <span class="text-slate-400">WA:</span>
                <span>{{ $u->phone_whatsapp ?? '-' }}</span>
              </div>
            </td>
            <td class="p-4">
              @if($u->role === 'admin')
                <span class="bg-amber-100 text-amber-900 px-3 py-1 rounded-full text-[10px] font-heading font-black uppercase inline-block">Admin Studio</span>
              @elseif($u->role === 'cashier')
                <span class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-[10px] font-heading font-black uppercase inline-block">Kasir</span>
              @else
                <span class="bg-blue-100 text-blue-900 px-3 py-1 rounded-full text-[10px] font-heading font-black uppercase inline-block">Pelanggan</span>
              @endif
            </td>
            <td class="p-4 font-mono font-bold text-slate-700">
              <span class="bg-slate-100 text-slate-800 px-2.5 py-1 rounded-lg text-xs font-mono">
                {{ $u->bookings_count }} Antrean
              </span>
            </td>
            <td class="p-4">
              @if($u->is_active)
                <span class="px-2.5 py-1 rounded-full text-[10px] font-heading font-bold uppercase bg-emerald-100 text-emerald-800 inline-block">
                  Aktif
                </span>
              @else
                <span class="px-2.5 py-1 rounded-full text-[10px] font-heading font-bold uppercase bg-rose-100 text-rose-800 inline-block">
                  Nonaktif
                </span>
              @endif
            </td>
            <td class="p-4 pr-6 text-right whitespace-nowrap">
              <div class="inline-flex items-center justify-end gap-1.5">
                <!-- Edit Button (Icon) -->
                <button
                  type="button"
                  onclick='openEditUserModal(@json($u))'
                  class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 inline-flex items-center justify-center transition-all shadow-xs"
                  title="Ubah Akun"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>

                <!-- Toggle Active Button (Icon) -->
                <form action="{{ route('admin.users.toggle', $u->id) }}" method="POST" class="inline">
                  @csrf
                  <button
                    type="submit"
                    class="w-8 h-8 rounded-xl inline-flex items-center justify-center transition-all border shadow-xs {{ $u->is_active ? 'bg-amber-50 hover:bg-amber-100 text-amber-700 border-amber-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200' }}"
                    title="{{ $u->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                  >
                    @if($u->is_active)
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    @else
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @endif
                  </button>
                </form>

                <!-- Delete Button (Icon) -->
                @if($u->id !== auth()->id())
                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="inline">
                  @csrf
                  @method('DELETE')
                  <button
                    type="button"
                    onclick="confirmFormSubmit(this.form, 'Apakah Anda yakin ingin menghapus akun pengguna {{ addslashes($u->name) }}? Tindakan ini tidak dapat dibatalkan.', 'Hapus Akun Pengguna')"
                    class="w-8 h-8 rounded-xl inline-flex items-center justify-center transition-all border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 shadow-xs cursor-pointer"
                    title="Hapus Akun Pengguna"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="p-8 text-center text-slate-500 font-heading font-medium">
              Tidak ada akun yang cocok dengan pencarian.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="p-4 border-t border-slate-100 bg-white">
      {{ $users->links() }}
    </div>
  </div>

</div>

<!-- MODAL: TAMBAH PENGGUNA BARU -->
<div id="addUserModal" class="fixed inset-0 bg-[#1A1A2E]/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl border border-slate-200 max-w-md w-full p-6 space-y-4 shadow-2xl">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <h3 class="text-base font-heading font-black text-[#1A1A2E]">Tambah Akun Staf (Kasir / Admin)</h3>
      <button onclick="closeAddUserModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">✕</button>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-3.5 text-xs">
      @csrf

      <div>
        <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">Nama Lengkap *</label>
        <input type="text" name="name" required class="w-full rounded-xl border border-slate-300 p-2.5 font-bold bg-slate-50" />
      </div>

      <div>
        <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">Alamat Email (Login) *</label>
        <input type="email" name="email" required placeholder="staf@kothak.com" class="w-full rounded-xl border border-slate-300 p-2.5 font-bold bg-slate-50" />
      </div>

      <div>
        <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">Nomor WhatsApp *</label>
        <input type="text" name="phone_whatsapp" required placeholder="08xxxxxxxxxx" class="w-full rounded-xl border border-slate-300 p-2.5 font-mono font-bold bg-slate-50" />
      </div>

      <div>
        <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">Peran Staf (Role) *</label>
        <select name="role" required class="w-full rounded-xl border border-slate-300 p-2.5 font-bold bg-slate-50">
          <option value="cashier">Kasir</option>
          <option value="admin">Admin Studio (Full Akses)</option>
        </select>
        <span class="text-[10px] text-slate-400 mt-1 block">Data pelanggan dibuat otomatis dari nama dan nomor HP saat ambil antrean.</span>
      </div>

      <div>
        <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">Kata Sandi Awal *</label>
        <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full rounded-xl border border-slate-300 p-2.5 font-bold bg-slate-50" />
      </div>

      <div class="flex items-center gap-2 pt-2">
        <button type="button" onclick="closeAddUserModal()" class="flex-1 py-2.5 rounded-xl font-heading font-bold uppercase text-xs bg-slate-100 hover:bg-slate-200 text-slate-700">
          Batal
        </button>
        <button type="submit" class="flex-1 py-2.5 rounded-xl font-heading font-black uppercase text-xs bg-[#1E3A8A] hover:bg-[#2D2DB0] text-white shadow-sm">
          Simpan Akun Staf
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: UBAH DATA PENGGUNA -->
<div id="editUserModal" class="fixed inset-0 bg-[#1A1A2E]/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl border border-slate-200 max-w-md w-full p-6 space-y-4 shadow-2xl">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <h3 class="text-base font-heading font-black text-[#1A1A2E]">Ubah Data Akun</h3>
      <button onclick="closeEditUserModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">✕</button>
    </div>

    <form id="editUserForm" method="POST" class="space-y-3.5 text-xs">
      @csrf
      @method('PUT')

      <div>
        <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">Nama Lengkap *</label>
        <input type="text" name="name" id="editName" required class="w-full rounded-xl border border-slate-300 p-2.5 font-bold bg-slate-50" />
      </div>

      <div>
        <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">Alamat Email (Login) *</label>
        <input type="email" name="email" id="editEmail" required class="w-full rounded-xl border border-slate-300 p-2.5 font-bold bg-slate-50" />
      </div>

      <div>
        <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">Nomor WhatsApp *</label>
        <input type="text" name="phone_whatsapp" id="editPhone" required class="w-full rounded-xl border border-slate-300 p-2.5 font-mono font-bold bg-slate-50" />
      </div>

      <div>
        <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">Peran (Role) *</label>
        <select name="role" id="editRole" required class="w-full rounded-xl border border-slate-300 p-2.5 font-bold bg-slate-50">
          <option value="cashier">Kasir</option>
          <option value="admin">Admin Studio (Full Akses)</option>
          <option value="customer" disabled>Pelanggan (Terdeteksi Otomatis)</option>
        </select>
      </div>

      <div>
        <label class="block font-heading font-bold uppercase text-slate-600 text-[10px] mb-1">Ganti Kata Sandi (Opsional)</label>
        <input type="password" name="password" minlength="6" placeholder="Biarkan kosong jika tidak diubah" class="w-full rounded-xl border border-slate-300 p-2.5 font-bold bg-slate-50" />
      </div>

      <div class="flex items-center gap-2 pt-2">
        <button type="button" onclick="closeEditUserModal()" class="flex-1 py-2.5 rounded-xl font-heading font-bold uppercase text-xs bg-slate-100 hover:bg-slate-200 text-slate-700">
          Batal
        </button>
        <button type="submit" class="flex-1 py-2.5 rounded-xl font-heading font-black uppercase text-xs bg-[#1E3A8A] hover:bg-[#2D2DB0] text-white shadow-sm">
          Simpan Perubahan
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

  // REAL-TIME AUTO-UPDATE DAFTAR PENGGUNA (Tanpa Reload Halaman)
  let isUsersRefreshing = false;

  function fetchLiveUsers() {
    if (isUsersRefreshing) return;
    const addM = document.getElementById('addUserModal');
    const editM = document.getElementById('editUserModal');
    if ((addM && !addM.classList.contains('hidden')) || (editM && !editM.classList.contains('hidden'))) {
      return;
    }

    isUsersRefreshing = true;
    const url = window.location.href + (window.location.href.includes('?') ? '&' : '?') + '_t=' + Date.now();

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

      const newBody = doc.getElementById('adminUserTableBody');
      const curBody = document.getElementById('adminUserTableBody');
      if (newBody && curBody) {
        curBody.innerHTML = newBody.innerHTML;
      }
    })
    .catch(() => {})
    .finally(() => {
      isUsersRefreshing = false;
    });
  }

  // Cross-tab broadcast listener untuk respon seketika (0 ms)
  try {
    if ('BroadcastChannel' in window) {
      const liveQueueChannel = new BroadcastChannel('kothak_live_queue');
      liveQueueChannel.onmessage = fetchLiveUsers;
    }
  } catch(e) {}
  window.addEventListener('storage', function(e) {
    if (e.key === 'kothak_queue_bump') fetchLiveUsers();
  });

  // Polling data otomatis setiap 2 detik
  setInterval(fetchLiveUsers, 2000);
</script>
@endpush
@endsection
