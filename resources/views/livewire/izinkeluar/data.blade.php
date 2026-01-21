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
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Pengajuan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('izinkeluar.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Izin Keluar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Ajuan Izin Keluar
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                
                <div class="p-6 text-base-content space-y-4">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-4">
                        <div class="w-full md:w-auto grid grid-cols-2 gap-[2px]">
                            @can('akses', 'Pengajuan Izin Keluar Tambah')
                            <button onclick="document.getElementById('storeModalIzinKeluar').showModal()" class="btn btn-success w-full">
                                <i class="fa-solid fa-plus"></i> Tambah
                            </button>
                            @endcan
                        </div>
                    </div>
                    <div class="space-y-8">
                        @can('akses', 'Pengajuan Izin Keluar')
                        {{-- IZIN ON PROCESS (STATUS === DISETUJUI) --}}
                        <div class="card bg-base-100 shadow">
                            <div class="card-body">
                                <h2 class="text-lg font-semibold text-primary flex items-center gap-2">
                                    <i class="fa-solid fa-circle-check"></i> Staff Sedang Izin Keluar
                                </h2>
                                <div class="divider my-2"></div>
                                <livewire:Izinkeluar.Izinkeluardisetujui-Table />
                            </div>
                        </div>
                        @endcan
                        @can('akses', 'Pengajuan Riwayat Izin Keluar')
                        {{-- IZIN SELESAI (STATUS === SELESAI) --}}
                        <div class="card bg-base-100 shadow">
                            <div class="card-body">
                                <h2 class="text-lg font-semibold text-success flex items-center gap-2">
                                    <i class="fa-solid fa-circle-check"></i> Riwayat Staff Izin Keluar
                                </h2>
                                <div class="divider my-2"></div>
                                <livewire:Izinkeluar.Izinkeluarselesai-Table />
                            </div>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>