<div class="pt-1 pb-12">
    <style>
        .responsive-row-expand-container {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        }

        .responsive-row-expand-item-container {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .responsive-row-expand-item-name {
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            opacity: 0.6;
        }

        .responsive-row-expand-item-value {
            font-size: 0.875rem;
            line-height: 1.4;
        }
    </style>
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
                        <a href="{{ route('resep.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Reservasi
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Daftar Reservasi Pasien
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                <div class="p-6 text-base-content space-y-4">
                    <div class="flex justify-between items-center mb-4">
                        <button onclick="document.getElementById('storeModalReservasi').showModal()" class="btn btn-success"><i class="fa-solid fa-plus"></i> Reservasi</button>
                    </div>
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Tabel Antrian Masuk -->
                        <div class="w-full md:w-1/2">
                            <div class="flex items-center justify-between mb-4">
                                <h1 class="text-lg font-bold text-base-content">Permintaan Reservasi</h1>
                            </div>
                            <livewire:Reservasi.Permintaan-Table />
                        </div>

                        <!-- Tabel Antrian Dipanggil -->
                        <div class="w-full md:w-1/2">
                            <div class="flex items-center justify-between mb-4">
                                <h1 class="text-lg font-bold text-base-content">
                                    Reservasi Terkonfirmasi
                                </h1>
                            </div>
                            <livewire:Reservasi.Reservasi-Table />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>