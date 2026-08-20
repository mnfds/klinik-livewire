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
                {{-- <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Klinik" style="background-image: none;"/>
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
                </div> --}}
                
                {{-- <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Apotik" style="background-image: none;"/>
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
                </div> --}}

                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Pendapatan & Pengeluaran" style="background-image: none;"/>
                <div class="tab-content bg-base-200 p-1">
                    @can('akses', 'Pendapatan')
                    <livewire:Pendapatanlainnya.data />
                    <br>
                    @endcan
                    @can('akses', 'Pengeluaran')
                    <livewire:Uangkeluar.data />
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
                <div class="tab-content bg-base-100 shadow-md border border-neutral/50 rounded-2xl p-1">
                    @can('akses', 'Arus Kas Table')
                    <div class="tabs tabs-border p-2">
                        <input type="radio" name="table_tabs" class="tab bg-transparent text-base-content" aria-label="Harian" style="background-image: none;"/>
                        <div class="tab-content bg-base-1 p-1">
                            <livewire:Aruskas.Rekapitulasi.TableRekap />
                        </div>
                        <input type="radio" name="table_tabs" class="tab bg-transparent text-base-content" aria-label="Bulanan" style="background-image: none;"/>
                        <div class="tab-content bg-base-100 p-1">
                            <livewire:Aruskas.Rekapitulasi.TableRekapBulanan />
                        </div>
                        <input type="radio" name="table_tabs" class="tab bg-transparent text-base-content" aria-label="Tahunan" style="background-image: none;"/>
                        <div class="tab-content bg-base-100 p-1">
                            <livewire:Aruskas.Rekapitulasi.TableRekapTahunan />
                        </div>
                    </div>
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