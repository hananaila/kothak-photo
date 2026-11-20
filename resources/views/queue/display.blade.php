<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Layar Antrean Studio (TV Display Mode) — KOTHaK Photo Purbalingga</title>

  <!-- Tailwind CSS & Google Fonts -->
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

  <style>
    body { background-color: #0F172A; color: #F8FAFC; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
    .font-mono { font-family: 'JetBrains Mono', monospace; }
    .neo-box-white { border: 3px solid #FFFFFF; box-shadow: 6px 6px 0px #1D4ED8; }
    .neo-box-blue { border: 3px solid #1D4ED8; box-shadow: 6px 6px 0px #FFFFFF; }
    .pulse-glow { animation: pulseGlow 2s infinite; }
    @keyframes pulseGlow {
      0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.6); }
      50% { box-shadow: 0 0 40px rgba(16, 185, 129, 0.8); }
    }
  </style>
</head>
<body class="min-h-screen flex flex-col justify-between p-4 sm:p-8">

  <!-- TOP HEADER BAR -->
  <header class="flex items-center justify-between border-b-2 border-slate-700 pb-4 mb-6">
    <div class="flex items-center gap-4">
      <img src="{{ asset('assets/img/logo kothak cafe.jpg') }}" alt="Logo" class="w-12 h-12 object-cover border-2 border-white shadow-[2px_2px_0px_#1D4ED8]" />
      <div>
        <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-none">
          KOTHaK <span class="text-blue-400">Photo Studio</span>
        </h1>
        <div class="text-xs font-mono text-slate-400 mt-1 uppercase tracking-wider">
          Smart Queue Display &bull; Purbalingga
        </div>
      </div>
    </div>

    <!-- Live Clock & Date -->
    <div class="text-right">
      <div id="liveClock" class="text-2xl sm:text-3xl font-mono font-black text-amber-400">--:--:-- WIB</div>
      <div id="liveDate" class="text-xs font-bold text-slate-400 uppercase tracking-wider">
        {{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}
      </div>
    </div>
  </header>

  <!-- MAIN MONITOR DISPLAY (TWO COLUMNS) -->
  <main class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

    <!-- LEFT COLUMN: CALLING NOW / IN STUDIO (BIG BOLD) -->
    <div class="lg:col-span-7 flex flex-col justify-between border-4 border-white bg-slate-900 p-6 sm:p-10 shadow-[8px_8px_0px_#1D4ED8] pulse-glow">
      <div>
        <div class="flex items-center justify-between border-b-2 border-slate-800 pb-4 mb-6">
          <div class="flex items-center gap-2.5">
            <span class="w-3.5 h-3.5 bg-emerald-400 rounded-full animate-ping"></span>
            <span class="font-black uppercase tracking-widest text-emerald-400 text-sm">SEDANG DI STUDIO FOTO</span>
          </div>
          <span class="bg-blue-600 text-white font-mono text-xs font-bold px-3 py-1 border border-white uppercase">
            BOOTH UTAMA
          </span>
        </div>

        <div class="text-center py-6 sm:py-12">
          <div class="text-xs sm:text-sm font-bold uppercase tracking-[0.3em] text-slate-400">NOMOR ANTREAN</div>
          <div id="displayInStudioNumber" class="text-6xl sm:text-8xl lg:text-9xl font-black font-mono tracking-tight text-white mt-2 mb-4">
            {{ $inStudio?->queue_number ?? 'KOSONG' }}
          </div>
          <div id="displayInStudioName" class="text-xl sm:text-2xl font-black text-amber-300">
            {{ $inStudio?->user?->name ?? 'Studio Siap Digunakan' }}
          </div>
          <div id="displayInStudioPackage" class="text-sm sm:text-base text-blue-200 font-medium mt-1">
            {{ $inStudio?->package?->name ?? 'Silakan Kasir Memanggil Antrean' }}
          </div>
          <div id="displayInStudioBackdrop" class="text-xs text-slate-400 mt-2 font-mono">
            {{ $inStudio ? '🎨 ' . $bBackdrop = $inStudio->studio_backdrop : '' }}
          </div>
        </div>
      </div>

      <!-- Instruction footer -->
      <div class="bg-slate-800 border-2 border-slate-700 p-4 text-center text-xs sm:text-sm font-bold text-slate-300 flex items-center justify-center gap-2">
        <span>📢</span>
        <span>Silakan masuk ke studio saat nomor antrean Anda dipanggil.</span>
      </div>
    </div>

    <!-- RIGHT COLUMN: NEXT IN LINE & UPCOMING LIST -->
    <div class="lg:col-span-5 flex flex-col justify-between space-y-6">

      <!-- NEXT IN LINE CARD -->
      <div class="border-4 border-blue-500 bg-blue-950 p-6 shadow-[6px_6px_0px_#FFFFFF]">
        <div class="flex items-center justify-between border-b-2 border-blue-800 pb-3 mb-4">
          <span class="text-xs font-black uppercase tracking-widest text-blue-300">GILIRAN BERIKUTNYA (STANDBY)</span>
          <span class="text-[10px] font-mono text-amber-400 font-bold bg-amber-950 px-2 py-0.5 border border-amber-600 uppercase">SIAP-SIAP</span>
        </div>
        <div class="flex items-center justify-between">
          <div>
            <div id="displayNextNumber" class="text-4xl sm:text-5xl font-black font-mono text-white">
              {{ $nextQueue?->queue_number ?? 'TIDAK ADA' }}
            </div>
            <div id="displayNextName" class="text-sm sm:text-base font-bold text-blue-200 mt-1">
              {{ $nextQueue?->user?->name ?? 'Belum ada antrean standby' }}
            </div>
          </div>
          <div class="text-right text-xs font-mono text-blue-300">
            <div>{{ $nextQueue?->package?->name ?? '' }}</div>
            <div class="text-amber-400 mt-1 font-bold">~ Persiapkan Diri</div>
          </div>
        </div>
      </div>

      <!-- UPCOMING QUEUE LIST -->
      <div class="flex-1 border-2 border-slate-700 bg-slate-900 p-5 shadow-[4px_4px_0px_#0F172A] flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between border-b border-slate-800 pb-2 mb-3">
            <span class="text-xs font-black uppercase tracking-wider text-slate-400">DAFTAR ANTREAN MENUNGGU</span>
            <span class="font-mono text-xs font-bold text-blue-400" id="displayWaitingCount">
              {{ $waitingList->count() }} Antrean
            </span>
          </div>

          <div class="space-y-2 overflow-y-auto max-h-56 sm:max-h-72" id="displayWaitingList">
            @forelse($waitingList->take(6) as $wl)
            <div class="flex items-center justify-between bg-slate-800 p-2.5 border border-slate-700 text-xs">
              <div class="flex items-center gap-3">
                <span class="font-mono font-black text-amber-400 text-sm">{{ $wl->queue_number }}</span>
                <span class="font-bold text-white truncate max-w-[140px] sm:max-w-[180px]">{{ $wl->user->name }}</span>
              </div>
              <span class="font-mono text-[10px] px-2 py-0.5 border {{ $wl->status === 'checked_in' ? 'bg-purple-900 text-purple-200 border-purple-600' : 'bg-slate-700 text-slate-300 border-slate-600' }} uppercase">
                {{ $wl->status_label }}
              </span>
            </div>
            @empty
            <div class="text-center py-8 text-slate-500 font-bold text-xs">
              Tidak ada antrean menunggu saat ini.
            </div>
            @endforelse
          </div>
        </div>

        <div class="pt-3 border-t border-slate-800 text-center text-[11px] text-slate-500 font-mono">
          Auto-Syncing with KOTHaK Photo Engine
        </div>
      </div>

    </div>

  </main>

  <!-- BOTTOM TICKER MARQUEE -->
  <footer class="mt-6 border-t-2 border-slate-800 pt-3 flex items-center justify-between text-xs text-slate-400">
    <div class="flex items-center gap-2">
      <span class="bg-blue-600 text-white font-mono px-2 py-0.5 font-bold uppercase text-[10px]">INFO</span>
      <span class="truncate">Selamat datang di KOTHaK Cafe & Photo Studio Purbalingga. Nikmati kopi dan abadikan momen terbaikmu!</span>
    </div>
    <div class="font-mono text-[11px] text-slate-500 shrink-0 hidden sm:block">
      Audio Chime: <span id="chimeStatus" class="text-emerald-400 font-bold">AKTIF</span>
    </div>
  </footer>

  <script>
    // Live Digital Clock
    function updateClock() {
      const now = new Date();
      const h = String(now.getHours()).padStart(2, '0');
      const m = String(now.getMinutes()).padStart(2, '0');
      const s = String(now.getSeconds()).padStart(2, '0');
      document.getElementById('liveClock').innerText = `${h}:${m}:${s} WIB`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Studio Chime using Web Audio API (Synthesized Ding-Dong Chime)
    let audioCtx = null;
    function playChime() {
      try {
        if (!audioCtx) {
          audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtx.state === 'suspended') {
          audioCtx.resume();
        }

        const osc1 = audioCtx.createOscillator();
        const osc2 = audioCtx.createOscillator();
        const gain = audioCtx.createGain();

        osc1.type = 'sine';
        osc2.type = 'sine';

        // High tone then lower tone (Airport / Studio chime)
        osc1.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
        osc1.frequency.setValueAtTime(880.00, audioCtx.currentTime + 0.3); // A5

        gain.gain.setValueAtTime(0.4, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 1.2);

        osc1.connect(gain);
        gain.connect(audioCtx.destination);

        osc1.start();
        osc1.stop(audioCtx.currentTime + 1.2);
      } catch (err) {
        console.log('Chime error:', err);
      }
    }

    // Unlock Web Audio on first user interaction
    document.addEventListener('click', () => {
      if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    }, { once: true });

    let lastCalledNumber = "{{ $inStudio?->queue_number ?? '' }}";

    // Live Polling every 5 seconds
    async function pollQueueDisplay() {
      try {
        const response = await fetch("{{ route('api.queue.status') }}");
        if (!response.ok) return;
        const data = await response.json();

        // Update In Studio
        const numEl = document.getElementById('displayInStudioNumber');
        const nameEl = document.getElementById('displayInStudioName');
        const pkgEl = document.getElementById('displayInStudioPackage');
        const backdropEl = document.getElementById('displayInStudioBackdrop');

        if (data.in_studio) {
          numEl.innerText = data.in_studio.queue_number;
          nameEl.innerText = data.in_studio.customer_name;
          pkgEl.innerText = data.in_studio.package_name;
          backdropEl.innerText = data.in_studio.backdrop ? '🎨 ' + data.in_studio.backdrop : '';

          // Play chime if newly called
          if (data.in_studio.queue_number !== lastCalledNumber) {
            lastCalledNumber = data.in_studio.queue_number;
            playChime();
          }
        } else {
          numEl.innerText = 'KOSONG';
          nameEl.innerText = 'Studio Siap Digunakan';
          pkgEl.innerText = 'Silakan Kasir Memanggil Antrean';
          backdropEl.innerText = '';
        }

        // Update Next Queue
        const nextNumEl = document.getElementById('displayNextNumber');
        const nextNameEl = document.getElementById('displayNextName');
        if (data.next_queue) {
          nextNumEl.innerText = data.next_queue.queue_number;
          nextNameEl.innerText = data.next_queue.customer_name;
        } else {
          nextNumEl.innerText = 'TIDAK ADA';
          nextNameEl.innerText = 'Belum ada antrean standby';
        }

        // Update Waiting count
        document.getElementById('displayWaitingCount').innerText = `${data.waiting_count} Antrean`;

      } catch (e) {
        console.log('Poll display error:', e);
      }
    }

    setInterval(pollQueueDisplay, 5000);
  </script>
</body>
</html>
