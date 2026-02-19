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
                        <a href="{{ route('aruskas.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Laporan Kinerja Arus Kas
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Arus Kas
            </h1>
        </div>

        <!-- Main Content -->
        
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- TABS -->
            <div class="tabs tabs-border">
                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Klinik" style="background-image: none;"/>
                <div class="tab-content bg-base-200 p-1">
                    @can('akses', 'Arus Kas Klinik Card')
                    <livewire:Aruskas.uangklinikcard />
                    @endcan
                    @can('akses', 'Arus Kas Klinik Harian')
                    <livewire:Aruskas.Klinik.GrafikHarian />
                    @endcan
                    @can('akses', 'Arus Kas Klinik Bulanan')
                    <livewire:Aruskas.Klinik.GrafikBulanan />
                    @endcan
                    @can('akses', 'Arus Kas Klinik Tahunan')
                    <livewire:Aruskas.Klinik.GrafikTahunan />
                    @endcan
                    @if (
                        !Gate::allows('akses','Arus Kas Klinik Harian') &&
                        !Gate::allows('akses','Arus Kas Klinik Bulanan') &&
                        !Gate::allows('akses','Arus Kas Klinik Tahunan') &&
                        !Gate::allows('akses','Arus Kas Klinik Card')
                        )
                        <div class="flex items-center justify-center min-h-[300px]">
                            <div class="card bg-base-100 shadow-xl max-w-md w-full">
                                <div class="card-body items-center text-center">
                                    <div class="w-16 h-16 rounded-full bg-error/10 flex items-center justify-center">
                                        <i class="fa-solid fa-triangle-exclamation text-3xl text-error"></i>
                                    </div>
                                    <h2 class="card-title text-error mt-4">
                                        Akses Ditolak
                                    </h2>
                                    <p class="text-base-content/70 text-sm">
                                        Anda tidak memiliki izin untuk melihat laporan arus kas klinik.
                                        Silakan hubungi administrator untuk mendapatkan akses.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Apotik" style="background-image: none;"/>
                <div class="tab-content bg-base-200 p-1">
                    @can('akses', 'Arus Kas Apotik Card')
                    <livewire:Aruskas.uangapotikcard />
                    @endcan
                    @can('akses', 'Arus Kas Apotik Harian')
                    <livewire:Aruskas.Apotik.GrafikHarian />
                    @endcan
                    @can('akses', 'Arus Kas Apotik Bulanan')
                    <livewire:Aruskas.Apotik.GrafikBulanan />
                    @endcan
                    @can('akses', 'Arus Kas Apotik Tahunan')
                    <livewire:Aruskas.Apotik.GrafikTahunan />
                    @endcan
                    @if (
                        !Gate::allows('akses','Arus Kas Apotik Harian') &&
                        !Gate::allows('akses','Arus Kas Apotik Bulanan') &&
                        !Gate::allows('akses','Arus Kas Apotik Tahunan') &&
                        !Gate::allows('akses','Arus Kas Apotik Card')
                        )
                        <div class="flex items-center justify-center min-h-[300px]">
                            <div class="card bg-base-100 shadow-xl max-w-md w-full">
                                <div class="card-body items-center text-center">
                                    <div class="w-16 h-16 rounded-full bg-error/10 flex items-center justify-center">
                                        <i class="fa-solid fa-triangle-exclamation text-3xl text-error"></i>
                                    </div>
                                    <h2 class="card-title text-error mt-4">
                                        Akses Ditolak
                                    </h2>
                                    <p class="text-base-content/70 text-sm">
                                        Anda tidak memiliki izin untuk melihat laporan arus kas apotik.
                                        Silakan hubungi administrator untuk mendapatkan akses.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Pendapatan & Pengeluaran" style="background-image: none;"/>
                <div class="tab-content bg-base-200 p-1">
                    @can('akses', 'Pendapatan')
                    <livewire:Pendapatanlainnya.data />
                    <br>
                    @endcan
                    @can('akses', 'Pengeluaran')
                    <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                        <div class="p-6 text-base-content space-y-4">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-4">
                                <!-- Button -->
                                <div class="w-full md:w-auto grid grid-cols-2 gap-[2px]">
                                    @can('akses', 'Pengajuan Pengeluaran Tambah')
                                    <button onclick="document.getElementById('storeModalUangKeluar').showModal()" class="btn btn-success w-full">
                                        <i class="fa-solid fa-envelope"></i> Ajukan Permintaan
                                    </button>
                                    @endcan
                                    {{-- PENGELUARAN --}}
                                    @can('akses', 'Pengeluaran Tambah')
                                    <button onclick="document.getElementById('storeModalUangKeluarKasir').showModal()" class="btn btn-error w-full">
                                        <i class="fa-solid fa-plus"></i> Pengeluaran
                                    </button>
                                    @endcan
                                    @can('akses', 'Pengajuan Pengeluaran Disetujui Tambah')
                                    <button onclick="document.getElementById('storeModalUangKeluarKasir').showModal()" class="btn btn-success w-full">
                                        <i class="fa-solid fa-plus"></i> Pengeluaran
                                    </button>
                                    @endcan
                                </div>
                            </div>
                            <div class="space-y-8">
                                {{-- PENGAJUAN DITUNGGU --}}
                                @can('akses', 'Pengajuan Pengeluaran Pending')
                                <div class="card bg-base-100 shadow">
                                    <div class="card-body">
                                        <h2 class="text-lg font-semibold text-warning flex items-center gap-2">
                                            <i class="fa-solid fa-clock"></i> Pengajuan Ditunggu
                                        </h2>
                                        <div class="divider my-2"></div>
                                        <livewire:uangkeluar.Pending-table />
                                    </div>
                                </div>
                                @endcan
                                @can('akses', 'Pengeluaran')
                                {{-- PENGAJUAN DISETUJUI --}}
                                <div class="card bg-base-100 shadow">
                                    <div class="card-body">
                                        <h2 class="text-lg font-semibold text-error flex items-center gap-2">
                                            <i class="fa-solid fa-arrow-trend-down"></i> Pengeluaran
                                        </h2>
                                        <div class="divider my-2"></div>
                                        <livewire:uangkeluar.Diterima-table />
                                    </div>
                                </div>
                                @endcan
                                @can('akses', 'Pengajuan Pengeluaran Disetujui')
                                {{-- PENGAJUAN DISETUJUI --}}
                                <div class="card bg-base-100 shadow">
                                    <div class="card-body">
                                        <h2 class="text-lg font-semibold text-success flex items-center gap-2">
                                            <i class="fa-solid fa-circle-check"></i> Pengajuan Disetujui
                                        </h2>
                                        <div class="divider my-2"></div>
                                        <livewire:uangkeluar.Diterima-table />
                                    </div>
                                </div>
                                @endcan
                                @can('akses', 'Pengajuan Pengeluaran Ditolak')
                                {{-- PENGAJUAN DITOLAK --}}
                                <div class="card bg-base-100 shadow">
                                    <div class="card-body">
                                        <h2 class="text-lg font-semibold text-error flex items-center gap-2">
                                            <i class="fa-solid fa-circle-xmark"></i> Pengajuan Ditolak
                                        </h2>
                                        <div class="divider my-2"></div>
                                        <livewire:uangkeluar.Ditolak-table />
                                    </div>
                                </div>
                                @endcan
                                @if (!Gate::allows('akses','Pengeluaran'))
                                    <div class="flex items-center justify-center min-h-[300px]">
                                        <div class="card bg-base-100 shadow-xl max-w-md w-full">
                                            <div class="card-body items-center text-center">
                                                <div class="w-16 h-16 rounded-full bg-error/10 flex items-center justify-center">
                                                    <i class="fa-solid fa-triangle-exclamation text-3xl text-error"></i>
                                                </div>
                                                <h2 class="card-title text-error mt-4">
                                                    Akses Ditolak
                                                </h2>
                                                <p class="text-base-content/70 text-sm">
                                                    Anda tidak memiliki izin untuk akses table pengeluaran.
                                                    Silakan hubungi administrator untuk mendapatkan akses.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <livewire:Uangkeluar.Store />
                    <livewire:Uangkeluar.Storebykasir />
                    <livewire:Uangkeluar.Update />
                    @endcan
                    @if (!Gate::allows('akses','Pengeluaran') && !Gate::allows('akses','Pendapatan'))
                        <div class="flex items-center justify-center min-h-[300px]">
                            <div class="card bg-base-100 shadow-xl max-w-md w-full">
                                <div class="card-body items-center text-center">
                                    <div class="w-16 h-16 rounded-full bg-error/10 flex items-center justify-center">
                                        <i class="fa-solid fa-triangle-exclamation text-3xl text-error"></i>
                                    </div>
                                    <h2 class="card-title text-error mt-4">
                                        Akses Ditolak
                                    </h2>
                                    <p class="text-base-content/70 text-sm">
                                        Anda tidak memiliki izin untuk akses table pengeluaran.
                                        Silakan hubungi administrator untuk mendapatkan akses.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Pendapatan" style="background-image: none;"/>
                <div class="tab-content bg-base-200 p-1">
                    <livewire:Pendapatanlainnya.data />
                </div>
                
                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Pengeluaran" style="background-image: none;"/>
                <div class="tab-content bg-base-200 p-1">
                    <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                        <div class="p-6 text-base-content space-y-4">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-4">
                                <!-- Button -->
                                <div class="w-full md:w-auto grid grid-cols-2 gap-[2px]">
                                    @can('akses', 'Pengajuan Pengeluaran Tambah')
                                    <button onclick="document.getElementById('storeModalUangKeluar').showModal()" class="btn btn-success w-full">
                                        <i class="fa-solid fa-envelope"></i> Ajukan Permintaan
                                    </button>
                                    @endcan
                                    <!-- PENGELUARAN -->
                                    @can('akses', 'Pengeluaran Tambah')
                                    <button onclick="document.getElementById('storeModalUangKeluarKasir').showModal()" class="btn btn-error w-full">
                                        <i class="fa-solid fa-plus"></i> Pengeluaran
                                    </button>
                                    @endcan
                                    @can('akses', 'Pengajuan Pengeluaran Disetujui Tambah')
                                    <button onclick="document.getElementById('storeModalUangKeluarKasir').showModal()" class="btn btn-success w-full">
                                        <i class="fa-solid fa-plus"></i> Pengeluaran
                                    </button>
                                    @endcan
                                </div>
                            </div>
                            <div class="space-y-8">
                                <!-- PENGAJUAN DITUNGGU -->
                                @can('akses', 'Pengajuan Pengeluaran Pending')
                                <div class="card bg-base-100 shadow">
                                    <div class="card-body">
                                        <h2 class="text-lg font-semibold text-warning flex items-center gap-2">
                                            <i class="fa-solid fa-clock"></i> Pengajuan Ditunggu
                                        </h2>
                                        <div class="divider my-2"></div>
                                        <livewire:uangkeluar.Pending-table />
                                    </div>
                                </div>
                                @endcan
                                @can('akses', 'Pengeluaran')
                                <!-- PENGAJUAN DISETUJUI -->
                                <div class="card bg-base-100 shadow">
                                    <div class="card-body">
                                        <h2 class="text-lg font-semibold text-error flex items-center gap-2">
                                            <i class="fa-solid fa-arrow-trend-down"></i> Pengeluaran
                                        </h2>
                                        <div class="divider my-2"></div>
                                        <livewire:uangkeluar.Diterima-table />
                                    </div>
                                </div>
                                @endcan
                                @can('akses', 'Pengajuan Pengeluaran Disetujui')
                                <!-- PENGAJUAN DISETUJUI -->
                                <div class="card bg-base-100 shadow">
                                    <div class="card-body">
                                        <h2 class="text-lg font-semibold text-success flex items-center gap-2">
                                            <i class="fa-solid fa-circle-check"></i> Pengajuan Disetujui
                                        </h2>
                                        <div class="divider my-2"></div>
                                        <livewire:uangkeluar.Diterima-table />
                                    </div>
                                </div>
                                @endcan
                                @can('akses', 'Pengajuan Pengeluaran Ditolak')
                                <!-- PENGAJUAN DITOLAK -->
                                <div class="card bg-base-100 shadow">
                                    <div class="card-body">
                                        <h2 class="text-lg font-semibold text-error flex items-center gap-2">
                                            <i class="fa-solid fa-circle-xmark"></i> Pengajuan Ditolak
                                        </h2>
                                        <div class="divider my-2"></div>
                                        <livewire:uangkeluar.Ditolak-table />
                                    </div>
                                </div>
                                @endcan
                                @if (!Gate::allows('akses','Pengajuan Pengeluaran Pending') && !Gate::allows('akses','Pengajuan Pengeluaran Disetujui') && !Gate::allows('akses','Pengajuan Pengeluaran Ditolak') && !Gate::allows('akses','Pengeluaran'))   
                                <div class="alert alert-error">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    Anda tidak memiliki akses untuk melihat data ini.
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <livewire:Uangkeluar.Store />
                    <livewire:Uangkeluar.Storebykasir />
                    <livewire:Uangkeluar.Update />
                </div>  --}}

                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Rekapitulasi" style="background-image: none;" checked />
                <div class="tab-content bg-base-200 p-1">
                    @can('akses', 'Arus Kas Rekapitulasi Card')
                    <livewire:Aruskas.uangrekapitulasicard />
                    @endcan
                    @can('akses', 'Arus Kas Rekapitulasi Harian')
                    <livewire:Aruskas.Rekapitulasi.GrafikHarian />
                    @endcan
                    @can('akses', 'Arus Kas Rekapitulasi Bulanan')
                    <livewire:Aruskas.Rekapitulasi.GrafikBulanan />
                    @endcan
                    @can('akses', 'Arus Kas Rekapitulasi Tahunan')
                    <livewire:Aruskas.Rekapitulasi.GrafikTahunan />
                    @endcan
                    @if (
                        !Gate::allows('akses','Arus Kas Rekapitulasi Harian') &&
                        !Gate::allows('akses','Arus Kas Rekapitulasi Bulanan') &&
                        !Gate::allows('akses','Arus Kas Rekapitulasi Tahunan') &&
                        !Gate::allows('akses','Arus Kas Rekapitulasi Card')
                        )
                        <div class="flex items-center justify-center min-h-[300px]">
                            <div class="card bg-base-100 shadow-xl max-w-md w-full">
                                <div class="card-body items-center text-center">
                                    <div class="w-16 h-16 rounded-full bg-error/10 flex items-center justify-center">
                                        <i class="fa-solid fa-triangle-exclamation text-3xl text-error"></i>
                                    </div>
                                    <h2 class="card-title text-error mt-4">
                                        Akses Ditolak
                                    </h2>
                                    <p class="text-base-content/70 text-sm">
                                        Anda tidak memiliki izin untuk melihat laporan rekapitulasi arus kas.
                                        Silakan hubungi administrator untuk mendapatkan akses.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Table" style="background-image: none;" />
                <div class="tab-content bg-base-200 p-1">
                    @can('akses', 'Arus Kas Table')
                    <livewire:Aruskas.Rekapitulasi.TableRekap />
                    @endcan
                    @if (!Gate::allows('akses','Arus Kas Table'))
                        <div class="flex items-center justify-center min-h-[300px]">
                            <div class="card bg-base-100 shadow-xl max-w-md w-full">
                                <div class="card-body items-center text-center">
                                    <div class="w-16 h-16 rounded-full bg-error/10 flex items-center justify-center">
                                        <i class="fa-solid fa-triangle-exclamation text-3xl text-error"></i>
                                    </div>
                                    <h2 class="card-title text-error mt-4">
                                        Akses Ditolak
                                    </h2>
                                    <p class="text-base-content/70 text-sm">
                                        Anda tidak memiliki izin untuk melihat table arus kas.
                                        Silakan hubungi administrator untuk mendapatkan akses.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>