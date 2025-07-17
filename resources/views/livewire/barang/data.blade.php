<div class="pt-1 pb-12">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Breadcrumbs (hanya muncul di layar lg ke atas) -->
        <div class="hidden lg:flex justify-end px-4">
            <div class="breadcrumbs text-sm">
                <ul>
                    <li>
                        <a class="inline-flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-current" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a class="inline-flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-current" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
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
                            <button onclick="document.getElementById('storeModalBarang').showModal()" class="btn btn-success w-full">
                                <i class="fa-solid fa-box-open"></i> Tambah
                            </button>
                            <button class="btn btn-warning w-full">
                                <i class="fa-solid fa-clipboard"></i> Riwayat
                            </button>
                        </div>

                        <!-- KANAN: Stok Keluar & Masuk -->
                        <div class="w-full md:w-auto grid grid-cols-2 gap-[2px] mt-2 md:mt-0">
                            <button onclick="document.getElementById('takeModalBarang').showModal()" class="btn btn-secondary w-full">
                                <i class="fa-solid fa-circle-minus"></i> Keluar
                            </button>
                            <button onclick="document.getElementById('restockModalBarang').showModal()" class="btn btn-primary w-full">
                                <i class="fa-solid fa-circle-plus"></i> Masuk
                            </button>
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