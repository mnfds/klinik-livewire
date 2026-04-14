<dialog id="modalScanning" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closemodalScanning', () => {
        stopScanner();
        document.getElementById('modalScanning')?.close();
    })
    ">
    <div class="modal-box w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold">Scan QR</h3>

            {{-- Tombol flip kamera --}}
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
            <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                <p class="font-semibold mb-1">Berhasil di-scan!</p>
                <p>Hasil: <span class="font-mono">{{ $scannedData }}</span></p>
            </div>
            <div class="mt-3">
                <button class="btn btn-outline btn-sm" wire:click="resetScan">
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