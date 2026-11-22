<!-- MODAL DIALOG KONFIRMASI (CLEAN, MINIMAL & RESPONSIF) -->
<div id="kothakModalOverlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-[9999] hidden flex items-center justify-center p-4 transition-all">
  <div class="bg-white rounded-3xl border border-slate-200/80 max-w-lg w-full p-6 sm:p-7 space-y-6 shadow-2xl transform transition-all animate-in fade-in zoom-in-95 duration-150">
    
    <!-- TOP ROW: ICON & KONTEN PESAN -->
    <div class="flex items-start gap-4 sm:gap-5">
      <!-- Icon Box -->
      <div id="kothakModalIconBox" class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center shrink-0">
        <svg id="kothakModalIcon" class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
      </div>

      <!-- Text Container -->
      <div class="space-y-1.5 pt-1 min-w-0 flex-1">
        <h3 id="kothakModalTitle" class="text-lg sm:text-xl font-heading font-black text-slate-900 leading-snug">
          Hapus Akun Pengguna
        </h3>
        <p id="kothakModalMessage" class="text-xs sm:text-sm text-slate-600 font-sans leading-relaxed">
          Apakah Anda yakin ingin menghapus akun pengguna ini? Tindakan ini tidak dapat dibatalkan.
        </p>
      </div>
    </div>

    <!-- BOTTOM BUTTONS -->
    <div class="flex items-center justify-end gap-3 pt-2">
      <button
        type="button"
        id="kothakModalCancelBtn"
        onclick="kothakModalClose(false)"
        class="px-6 py-3 rounded-2xl text-xs font-heading font-black uppercase tracking-wider bg-[#F1F5F9] hover:bg-slate-200 text-slate-700 transition-all cursor-pointer"
      >
        BATAL
      </button>
      <button
        type="button"
        id="kothakModalConfirmBtn"
        onclick="kothakModalClose(true)"
        class="px-7 py-3 rounded-2xl text-xs font-heading font-black uppercase tracking-wider bg-[#E11D48] hover:bg-rose-700 text-white shadow-sm transition-all cursor-pointer"
      >
        <span id="kothakModalConfirmText">YA, HAPUS</span>
      </button>
    </div>

  </div>
</div>

<script>
  let kothakModalResolve = null;

  function showKothakConfirm({ title, message, confirmText = 'YA, HAPUS', cancelText = 'BATAL', type = 'danger' }) {
    return new Promise((resolve) => {
      kothakModalResolve = resolve;
      const overlay = document.getElementById('kothakModalOverlay');
      const titleEl = document.getElementById('kothakModalTitle');
      const msgEl = document.getElementById('kothakModalMessage');
      const confirmBtn = document.getElementById('kothakModalConfirmBtn');
      const confirmTextEl = document.getElementById('kothakModalConfirmText');
      const cancelBtn = document.getElementById('kothakModalCancelBtn');
      const iconBox = document.getElementById('kothakModalIconBox');
      const icon = document.getElementById('kothakModalIcon');

      if (titleEl) titleEl.innerText = title;
      if (msgEl) msgEl.innerText = message;

      if (cancelBtn) {
        if (cancelText) {
          cancelBtn.innerText = cancelText;
          cancelBtn.style.display = 'inline-block';
        } else {
          cancelBtn.style.display = 'none';
        }
      }

      if (confirmBtn && confirmTextEl) {
        confirmTextEl.innerText = confirmText;
        if (type === 'danger') {
          confirmBtn.className = 'px-7 py-3 rounded-2xl text-xs font-heading font-black uppercase tracking-wider bg-[#E11D48] hover:bg-rose-700 text-white shadow-sm transition-all cursor-pointer';
          if (iconBox) iconBox.className = 'w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center shrink-0';
          if (icon) {
            icon.className = 'w-7 h-7 text-rose-600';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>';
          }
        } else if (type === 'warning') {
          confirmBtn.className = 'px-7 py-3 rounded-2xl text-xs font-heading font-black uppercase tracking-wider bg-amber-600 hover:bg-amber-700 text-white shadow-sm transition-all cursor-pointer';
          if (iconBox) iconBox.className = 'w-14 h-14 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center shrink-0';
          if (icon) {
            icon.className = 'w-7 h-7 text-amber-600';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>';
          }
        } else {
          confirmBtn.className = 'px-7 py-3 rounded-2xl text-xs font-heading font-black uppercase tracking-wider bg-[#1E3A8A] hover:bg-[#2D2DB0] text-white shadow-sm transition-all cursor-pointer';
          if (iconBox) iconBox.className = 'w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0';
          if (icon) {
            icon.className = 'w-7 h-7 text-[#1E3A8A]';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>';
          }
        }
      }

      if (overlay) overlay.classList.remove('hidden');
    });
  }

  function showKothakAlert({ title = 'Informasi', message, type = 'info', buttonText = 'Tutup' }) {
    return showKothakConfirm({
      title,
      message,
      confirmText: buttonText,
      cancelText: null,
      type
    });
  }

  function kothakModalClose(confirmed) {
    const overlay = document.getElementById('kothakModalOverlay');
    if (overlay) overlay.classList.add('hidden');
    if (kothakModalResolve) {
      kothakModalResolve(confirmed);
      kothakModalResolve = null;
    }
  }

  // Helper untuk konfirmasi hapus form
  async function confirmFormSubmit(formEl, message, title = 'Hapus Akun Pengguna') {
    const confirmed = await showKothakConfirm({
      title,
      message,
      confirmText: 'YA, HAPUS',
      cancelText: 'BATAL',
      type: 'danger'
    });
    if (confirmed && formEl) {
      formEl.submit();
    }
  }

  // Intercept native browser alert
  window.alert = function(msg) {
    showKothakAlert({
      title: 'Pemberitahuan',
      message: msg,
      type: 'info',
      buttonText: 'Mengerti'
    });
  };

  // Close on Escape or click outside
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      const ov = document.getElementById('kothakModalOverlay');
      if (ov && !ov.classList.contains('hidden')) {
        kothakModalClose(false);
      }
    }
  });

  const overlayEl = document.getElementById('kothakModalOverlay');
  if (overlayEl) {
    overlayEl.addEventListener('click', function(e) {
      if (e.target === this) {
        kothakModalClose(false);
      }
    });
  }
</script>
