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
                        <a href="{{ route('cuti.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Cuti
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Pengajuan Cuti
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                <div class="p-6 text-base-content space-y-4">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-4">
                        <div class="w-full md:w-auto grid grid-cols-2 gap-[2px]">
                            @can('akses', 'Pengajuan Cuti Tambah')
                            <button onclick="document.getElementById('storeModalCuti').showModal()" class="btn btn-success w-full">
                                <i class="fa-solid fa-plus"></i> Ajukan Cuti
                            </button>
                            @endcan
                        </div>
                    </div>
                    <div class="space-y-8">
                        <div class="tabs tabs-border">
                            @can('akses', 'Pengajuan Cuti Tambah')
                            <input type="radio" name="tabs_cuti" class="tab bg-transparent text-base-content" aria-label="Cuti Anda" style="background-image: none;" @checked(!auth()->user()->can('akses', 'Persetujuan Pengajuan Cuti'))/>
                            <div class="tab-content p-1">
                                <div class="card bg-base-100 shadow">
                                    <div class="card-body">
                                        <h2 class="text-lg font-semibold text-primary flex items-center gap-2">
                                            <i class="fa-solid fa-hourglass-half"></i> Pengajuan Cuti Anda
                                        </h2>
                                        <div class="divider my-2"></div>
                                        <livewire:cuti.pengajuan-saya-table />
                                    </div>
                                </div>
                                @cannot('akses', 'Riwayat Pengajuan Cuti')
                                <div class="card bg-base-100 shadow">
                                    <div class="card-body">
                                        <h2 class="text-lg font-semibold text-warning flex items-center gap-2">
                                            <i class="fa-solid fa-clock-rotate-left"></i>Riwayat Cuti
                                        </h2>
                                        <div class="divider my-2"></div>
                                        <livewire:cuti.riwayat-pengajuan-table />
                                    </div>
                                </div>
                                @endcannot
                            </div>
                            @endcan
                            @can('akses', 'Persetujuan Pengajuan Cuti')
                            <input type="radio" name="tabs_cuti" class="tab bg-transparent text-base-content" aria-label="Daftar Cuti Karyawan" style="background-image: none;" @checked(auth()->user()->can('akses', 'Persetujuan Pengajuan Cuti'))/>
                            <div class="tab-content p-1">
                                <div class="card bg-base-100 shadow">
                                    <div class="card-body">
                                        <h2 class="text-lg font-semibold text-success flex items-center gap-2">
                                            <i class="fa-solid fa-circle-check"></i> Daftar Pengajuan Cuti
                                        </h2>
                                        <div class="divider my-2"></div>
                                        <livewire:Cuti.daftar-pengajuan-table />
                                    </div>
                                </div>
                            </div>
                            @endcan
                            @can('akses', 'Riwayat Pengajuan Cuti')
                            <input type="radio" name="tabs_cuti" class="tab bg-transparent text-base-content" aria-label="Riwayat Pengajuan Cuti" style="background-image: none;"/>
                            <div class="tab-content p-1">
                                <div class="card bg-base-100 shadow">
                                    <div class="card-body">
                                        <h2 class="text-lg font-semibold text-warning flex items-center gap-2">
                                            <i class="fa-solid fa-clock-rotate-left"></i>Riwayat Cuti
                                        </h2>
                                        <div class="divider my-2"></div>
                                        <livewire:cuti.riwayat-pengajuan-table />
                                    </div>
                                </div>
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>