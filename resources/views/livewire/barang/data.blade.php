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
                        <div class="w-full md:w-auto grid grid-cols-2 gap-[2px]">
                            @can('akses', 'Persediaan Barang Tambah')
                            <button onclick="document.getElementById('storeModalBarang').showModal()" class="btn btn-success w-full">
                                <i class="fa-solid fa-box-open"></i> Tambah
                            </button>
                            @endcan
                            @can('akses', 'Persediaan Riwayat Barang')
                            <a href="{{ route('barang.riwayat') }}" class="btn btn-warning w-full">
                                <i class="fa-solid fa-clipboard"></i> Riwayat
                            </a>
                            @endcan
                        </div>

                        <!-- KANAN: Stok Keluar & Masuk -->
                        <div class="w-full md:w-auto grid grid-cols-2 gap-[2px] mt-2 md:mt-0">
                            @can('akses', 'Persediaan Barang Keluar')
                            <button onclick="document.getElementById('takeModalBarang').showModal()" class="btn btn-secondary w-full">
                                <i class="fa-solid fa-circle-minus"></i> Keluar
                            </button>
                            @endcan
                            @can('akses', 'Persediaan Barang Masuk')
                            <button onclick="document.getElementById('restockModalBarang').showModal()" class="btn btn-primary w-full">
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
</div>