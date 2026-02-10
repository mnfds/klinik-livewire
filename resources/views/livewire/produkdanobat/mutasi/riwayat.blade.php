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
                        <a href="{{ route('produk-obat.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Produk Dan Obat
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('produk-obat.riwayat') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Riwayat Produk Dan Obat
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Riwayat Persediaan
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                <div class="p-6 text-base-content space-y-4">
                    <div class="flex justify-between md:justify-start items-center mb-4 gap-2">
                        @can('akses', 'Persediaan Produk & Obat Keluar')
                        <button onclick="document.getElementById('outstockModalProdukDanObat').showModal()" class="btn btn-secondary">
                            <i class="fa-solid fa-circle-minus"></i> Keluar
                        </button>
                        @endcan
                        @can('akses', 'Persediaan Produk & Obat Masuk')
                        <button onclick="document.getElementById('instockModalProdukDanObat').showModal()" class="btn btn-primary">
                            <i class="fa-solid fa-circle-plus"></i> Masuk
                        </button>
                        @endcan
                    </div>
                    <livewire:Produkdanobat.Mutasi.Riwayat-Table />
                    <livewire:Produkdanobat.Mutasi.Instock />
                    <livewire:Produkdanobat.Mutasi.Outstock />
                    <livewire:Produkdanobat.Mutasi.Updateriwayat />
                </div>
            </div>
        </div>
    </div>
</div>