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
                        <a href="{{ route('lembur.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Lembur
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Pengajuan Lembur
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                
                <div class="p-6 text-base-content space-y-4">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-4">
                        <div class="w-full md:w-auto grid grid-cols-2 gap-[2px]">
                            @can('akses', 'Pengajuan Izin Keluar Tambah')
                            <button onclick="document.getElementById('storeModalLembur').showModal()" class="btn btn-success w-full">
                                <i class="fa-solid fa-plus"></i> Tambah
                            </button>
                            @endcan
                        </div>
                    </div>
                    <div class="space-y-8">
                        @can('akses', 'Pengajuan Izin Keluar')
                        {{-- TABLE PENGAJUAN LEMBUR --}}
                        <div class="card bg-base-100 shadow">
                            <div class="card-body">
                                <h2 class="text-lg font-semibold text-primary flex items-center gap-2">
                                    <i class="fa-solid fa-hourglass-half"></i> Pengajuan Lembur Karyawan
                                </h2>
                                <div class="divider my-2"></div>
                                <livewire:Lembur.Pending-Table />
                            </div>
                        </div>
                        @endcan

                        @can('akses', 'Pengajuan Riwayat Izin Keluar')
                        {{-- TABLE KARYAWAN YANG DISETUJUI UNTUK LEMBUR --}}
                        <div class="card bg-base-100 shadow">
                            <div class="card-body">
                                <h2 class="text-lg font-semibold text-success flex items-center gap-2">
                                    <i class="fa-solid fa-circle-check"></i> Karyawan Disetujui Untuk Lembur
                                </h2>
                                <div class="divider my-2"></div>
                                <livewire:Lembur.Approve-Table />
                            </div>
                        </div>
                        @endcan

                        @can('akses', 'Pengajuan Riwayat Izin Keluar')
                        {{-- RIWAYAT PENGAJUAN --}}
                        <div class="card bg-base-100 shadow">
                            <div class="card-body">
                                <h2 class="text-lg font-semibold text-warning flex items-center gap-2">
                                    <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Pengajuan Karyawan Untuk Lembur
                                </h2>
                                <div class="divider my-2"></div>
                                <livewire:Lembur.History-Table />
                            </div>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>