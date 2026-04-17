<div class="pt-1 pb-12">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Breadcrumbs (hanya muncul di layar lg ke atas) -->
        <div class="hidden lg:flex justify-end px-4">
            <div class="breadcrumbs text-sm">
                <ul>
                    <li>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('barang.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Persediaan Barang
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Persediaan Barang
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                
                <div class="p-6 text-base-content space-y-4">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-4">
                        <!-- KIRI: Tambah Barang & Riwayat -->
                        <div class="flex flex-wrap gap-[2px]">
                            @can('akses', 'Persediaan Barang Tambah')
                            <button onclick="document.getElementById('storeModalBarang').showModal()" class="btn btn-success btn-sm">
                                <i class="fa-solid fa-box-open"></i> Tambah
                            </button>
                            @endcan
                            @can('akses', 'Persediaan Riwayat Barang')
                            <a href="{{ route('barang.riwayat') }}" class="btn btn-warning btn-sm">
                                <i class="fa-solid fa-clipboard"></i> Riwayat
                            </a>
                            @endcan
                            @can('akses', 'Persediaan Barang QR')
                            <button onclick="document.getElementById('showQrCode').showModal()" class="btn btn-info btn-sm">
                                <i class="fa-solid fa-qrcode"></i> QRCODE
                            </button>
                            @endcan
                        </div>

                        <!-- KANAN: Stok Keluar & Masuk -->
                        <div class="flex flex-wrap justify-end gap-[2px]">
                            @can('akses', 'Persediaan Barang Scan')
                            <button onclick="document.getElementById('modalScanning').showModal(); startScanner()" class="btn btn-neutral btn-sm">
                                <i class="fa-solid fa-expand"></i> Pindai
                            </button>
                            @endcan
                            @can('akses', 'Persediaan Barang Keluar')
                            <button onclick="document.getElementById('takeModalBarang').showModal()" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-circle-minus"></i> Keluar
                            </button>
                            @endcan
                            @can('akses', 'Persediaan Barang Masuk')
                            <button onclick="document.getElementById('restockModalBarang').showModal()" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-circle-plus"></i> Masuk
                            </button>
                            @endcan
                        </div>
                    </div>
                    <livewire:barang-table />
                    <script>
                        window.addEventListener('show-delete-confirmation', event => {
                                if (confirm('Yakin ingin menghapus user ini?')) {
                                    Livewire.call('confirmDelete', event.detail.rowId);
                                }
                            });
                    </script>
                </div>
            </div>
        </div>
    </div>
    <dialog id="showQrCode" class="modal" wire:ignore.self x-data x-init="
        Livewire.on('closeshowQrCode', () => { document.getElementById('showQrCode')?.close()})">
        
        <div class="modal-box w-full max-w-lg">
            
            <!-- Title -->
            <h3 class="text-xl font-semibold text-center mb-5">
                QR Code Barang
            </h3>

            <!-- QR Container -->
            <div class="flex flex-col items-center gap-4 p-5 border border-base-200 rounded-xl bg-base-100 mx-auto">

                <div class="w-48 h-48 flex items-center justify-center" id="qr-image">
                    {!! $qrImage !!}
                </div>
                <p class="text-base font-mono font-semibold tracking-widest bg-base-200 px-3 py-1 rounded-lg">{{ $qrcode }}</p>

                <button onclick="downloadQR('{{ $qrcode }}')" class="btn btn-sm btn-info gap-2 w-full">
                    <i class="fa-solid fa-download"></i>Download
                </button>
            </div>

            <!-- Footer -->
            <div class="flex justify-end mt-5">
                <button type="button" 
                    class="btn btn-error"
                    onclick="document.getElementById('showQrCode').close()">
                    Tutup
                </button>
            </div>

        </div>
    </dialog>
</div>
<script>
    function downloadQR(filename) {
        const svgEl = document.querySelector('#qr-image svg');
        const svgData = new XMLSerializer().serializeToString(svgEl);
        const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(svgBlob);

        const img = new Image();
        img.onload = function () {
            const canvas = document.createElement('canvas');
            canvas.width = 400;  // resolusi PNG, makin besar makin tajam
            canvas.height = 400;

            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff'; // background putih
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            const pngUrl = canvas.toDataURL('image/png');
            const a = document.createElement('a');
            a.href = pngUrl;
            a.download = `qrcode-${filename}.png`;
            a.click();

            URL.revokeObjectURL(url);
        };

        img.src = url;
    }
</script>