<!-- Main Content -->
<div class="max-w-full mx-auto sm:px-6 lg:px-8 mt-2">
    <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
        <button onclick="document.getElementById('modalScanning').showModal(); startScanner()" class="btn btn-neutral btn-sm">
            <i class="fa-solid fa-expand"></i> Pindai
        </button>
        <div class="p-6 text-base-content space-y-4">
            <dialog id="modalScanning" class="modal" wire:ignore.self x-data x-init="
                Livewire.on('closemodalScanning', () => {
                    stopScanner();
                    document.getElementById('modalScanning')?.close();
                });
    
                Livewire.on('openTakeModal', () => {
                    stopScanner();
                    document.getElementById('modalScanning')?.close();
                    setTimeout(() => {
                        document.getElementById('takeModalBarang')?.showModal();
                    }, 300);
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
                                <p>Hasil: <span class="font-mono">{{ $scannedData }}</span></p>
                            </div>
                        @endif
                        @if ($booleanScan === false)
                            <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                                <p class="font-semibold mb-1">Gagal Melakukan Absensi!</p>
                                <p>Hasil: <span class="font-mono">{{ $scannedData }}</span></p>
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
    </div>
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