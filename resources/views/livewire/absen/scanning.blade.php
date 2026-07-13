<div x-data="{ now: new Date() }" x-init="setInterval(() => now = new Date(), 1000)">
    {{-- Tanggal & Waktu (full width) --}}
    <div class="flex justify-between items-center bg-base-100 shadow-md rounded-box border-t-2 border-top border-warning px-4 py-3 mb-2">
        <span class="font-medium texl-xl" x-text="now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })"></span>
        <span class="font-mono text-xl" x-text="now.toLocaleTimeString('id-ID')"></span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Card 1: Pindai --}}
        <div class="bg-base-100 rounded-box p-4 flex flex-col items-center gap-2 text-center shadow-md border-t-2 border-primary">
            <p class="text-lg font-semibold text-primary">Pindai Kehadiran</p>

            <button onclick="document.getElementById('modalScanning').showModal(); startScanner()" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-expand"></i> Pindai
            </button>

            <div class="text-sm mt-1 space-y-1">
                <p class="flex items-center gap-2">
                    <i class="fa-solid fa-right-to-bracket text-success"></i>
                    Masuk: <span class="font-medium">{{ $jamMasuk ?? 'Belum Absen' }}</span>
                </p>
                <p class="flex items-center gap-2">
                    <i class="fa-solid fa-right-from-bracket text-error"></i>
                    Pulang: <span class="font-medium">{{ $jamPulang ?? 'Belum Absen' }}</span>
                </p>
            </div>
        </div>

        {{-- Card 2: Absen Manual --}}
        <div class="bg-base-100 rounded-box p-4 flex flex-col items-center gap-2 text-center shadow-md border-t-2 border-info">
            <p class="text-lg font-semibold text-info">Absen Manual</p>

            <p class="text-sm">Lokasi Anda : <span class="font-semibold text-info">{{ $lokasiTerdeteksi ?? 'Tidak Terdeteksi' }}</span></p>

            <div class="flex gap-2">
                <button wire:click="absenMasuk" wire:loading.attr="disabled" wire:target="absenMasuk" class="btn btn-success btn-sm">
                    <span wire:loading.remove wire:target="absenMasuk">Absen Masuk</span>
                    <span wire:loading wire:target="absenMasuk">
                        <span class="loading loading-spinner loading-xs"></span> Memproses...
                    </span>
                </button>
                <button wire:click="absenPulang" wire:loading.attr="disabled" wire:target="absenPulang" class="btn btn-error btn-sm">
                    <span wire:loading.remove wire:target="absenPulang">Absen Pulang</span>
                    <span wire:loading wire:target="absenPulang">
                        <span class="loading loading-spinner loading-xs"></span> Memproses...
                    </span>
                </button>
            </div>

            <div class="text-sm mt-1 space-y-1">
                <p class="flex items-center gap-2">
                    <i class="fa-solid fa-right-to-bracket text-success"></i>
                    Masuk: <span class="font-medium">{{ $jamMasuk ?? 'Belum Absen' }}</span>
                </p>
                <p class="flex items-center gap-2">
                    <i class="fa-solid fa-right-from-bracket text-error"></i>
                    Pulang: <span class="font-medium">{{ $jamPulang ?? 'Belum Absen' }}</span>
                </p>
            </div>
        </div>

        {{-- Card 3: Statistik --}}
        <div class="bg-base-100 rounded-box p-4 flex flex-col gap-2 shadow-md border-t-2 border-error">
            <p class="text-lg font-semibold text-center text-error">Data Absensi Anda</p>

            <div class="text-sm space-y-1.5 mt-1">
                <p class="flex justify-between gap-2">
                    <span class="text-base-content/70">Kuota Libur</span>
                    <span class="font-semibold text-error">{{ $kuotaLibur ?? 0 }} Hari</span>
                </p>
                <p class="flex justify-between gap-2">
                    <span class="text-base-content/70">Masuk Terlambat</span>
                    <span class="font-semibold text-warning">{{ $jumlahTerlambat ?? 0 }} Hari</span>
                </p>
                <p class="flex justify-between gap-2">
                    <span class="text-base-content/70">Masuk Tepat Waktu</span>
                    <span class="font-semibold text-success">{{ $jumlahTepatWaktu ?? 0 }} Hari</span>
                </p>
                <p class="flex justify-between gap-2">
                    <span class="text-base-content/70">Tidak Ada Absensi</span>
                    <span class="font-semibold">{{ $jumlahAlpha ?? 0 }} Hari</span>
                </p>
            </div>
        </div>

    </div>
    <dialog id="modalScanning" class="modal" wire:ignore.self x-data x-init="
        Livewire.on('closemodalScanning', () => {
            stopScanner();
            document.getElementById('modalScanning')?.close();
        });
        ">
        <div class="modal-box w-full max-w-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold">Scan QR</h3>
                @if(!$scannedData)
                    <button onclick="flipKamera()" class="btn btn-sm btn-soft btn-primary gap-2">
                        <i class="fa-solid fa-arrows-rotate"></i>
                        Balik Kamera
                    </button>
                @endif
            </div>

            @if(!$scannedData)
                <div wire:ignore>
                    <div id="qr-reader" class="w-full rounded-lg overflow-hidden"></div>
                </div>
            @endif

            @if($scannedData)
                @if ($booleanScan === true)
                    <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                        <p class="font-semibold mb-1">Berhasil Melakukan Absensi!</p>
                        <p>Halo, <span class="font-mono">{{ $scannedData }}</span></p>
                    </div>
                @endif
                @if ($booleanScan === false)
                    <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                        <p class="font-semibold mb-1">Gagal Melakukan Absensi!</p>
                        <p>Mohon Maaf, <span class="font-mono">{{ $scannedData }}</span></p>
                    </div>
                @endif
                <div class="mt-3">
                    <button class="btn btn-soft btn-sm btn-secondary" wire:click="resetScan">
                        Scan Ulang
                    </button>
                </div>
            @endif

            <div class="modal-action">
                <button class="btn btn-soft btn-error" onclick="stopScanner(); document.getElementById('modalScanning').close()">
                    Tutup
                </button>
            </div>
        </div>
    </dialog>
</div>
<script>
    let html5QrCode = null;
    let currentCamera = 'environment';

    function startScanner() {
        // Tunggu DOM selesai di-render Livewire dulu
        setTimeout(() => {
            const readerEl = document.getElementById('qr-reader');
            if (!readerEl) return;

            readerEl.innerHTML = '';
            html5QrCode = new Html5Qrcode("qr-reader");

            html5QrCode.start(
                { facingMode: currentCamera },
                { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
                (decodedText) => {
                    if (!html5QrCode || !html5QrCode.isScanning) return;

                    html5QrCode.stop().then(() => {
                        html5QrCode.clear();
                        html5QrCode = null;
                        @this.dispatch('qrScanned', { result: decodedText });
                    });
                },
                () => {}
            ).catch(err => console.error("Gagal akses kamera:", err));
        }, 300); // tunggu Livewire selesai render DOM
    }

    function stopScanner() {
        if (html5QrCode) {
            if (html5QrCode.isScanning) {
                html5QrCode.stop().then(() => { html5QrCode.clear(); html5QrCode = null; });
            } else {
                html5QrCode.clear();
                html5QrCode = null;
            }
        }
    }

    function flipKamera() {
        currentCamera = currentCamera === 'environment' ? 'user' : 'environment';
        stopScanner();
        setTimeout(() => startScanner(), 300);
    }

    // Listen event dari Livewire
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('startScanner', () => startScanner());

        Livewire.on('closemodalScanning', () => {
            stopScanner();
            document.getElementById('modalScanning')?.close();
        });
    });
</script>