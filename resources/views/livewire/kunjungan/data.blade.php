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
                            Laporan Kunjungan Pasien
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Kunjungan Pasien
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- TABS -->
            <div class="tabs tabs-border">
                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Wanita" style="background-image: none;"/>
                <div class="tab-content bg-base-200 p-1">
                    @can('akses', 'Kunjungan Pasien Wanita Card')
                    <livewire:Kunjungan.Card.Wanita />
                    @endcan
                    @can('akses', 'Kunjungan Pasien Wanita Harian')
                    <livewire:Kunjungan.Wanita.GrafikHarian />
                    @endcan
                    @can('akses', 'Kunjungan Pasien Wanita Bulanan')
                    <livewire:Kunjungan.Wanita.GrafikBulanan />
                    @endcan
                    @can('akses', 'Kunjungan Pasien Wanita Tahunan')
                    <livewire:Kunjungan.Wanita.GrafikTahunan />
                    @endcan
                    @if (
                        !Gate::allows('akses','Kunjungan Pasien Wanita Harian') &&
                        !Gate::allows('akses','Kunjungan Pasien Wanita Bulanan') &&
                        !Gate::allows('akses','Kunjungan Pasien Wanita Tahunan') &&
                        !Gate::allows('akses','Kunjungan Pasien Wanita Card')
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
                                        Anda tidak memiliki izin untuk melihat laporan kunjungan pasien wanita.
                                        Silakan hubungi administrator untuk mendapatkan akses.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Pria" style="background-image: none;" />
                <div class="tab-content bg-base-200 p-1">
                    @can('akses', 'Kunjungan Pasien Pria Card')
                    <livewire:Kunjungan.Card.Lakilaki />
                    @endcan
                    @can('akses', 'Kunjungan Pasien Pria Harian')
                    <livewire:Kunjungan.Lakilaki.GrafikHarian />
                    @endcan
                    @can('akses', 'Kunjungan Pasien Pria Bulanan')
                    <livewire:Kunjungan.Lakilaki.GrafikBulanan />
                    @endcan
                    @can('akses', 'Kunjungan Pasien Pria Tahunan')
                    <livewire:Kunjungan.Lakilaki.GrafikTahunan />
                    @endcan
                    @if (
                        !Gate::allows('akses','Kunjungan Pasien Pria Harian') &&
                        !Gate::allows('akses','Kunjungan Pasien Pria Bulanan') &&
                        !Gate::allows('akses','Kunjungan Pasien Pria Tahunan') &&
                        !Gate::allows('akses','Kunjungan Pasien Pria Card')
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
                                        Anda tidak memiliki izin untuk melihat laporan kunjungan pasien pria.
                                        Silakan hubungi administrator untuk mendapatkan akses.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Best Items" style="background-image: none;"/>
                <div class="tab-content bg-base-200 p-1">
                    @can('akses', 'Produk Terbaik')
                    <livewire:Kunjungan.Items.Bundling />
                    <livewire:Kunjungan.Items.Produk />
                    <livewire:Kunjungan.Items.Treatment />
                    <livewire:Kunjungan.Items.Pelayanan />
                    @endcan
                    @if (!Gate::allows('akses','Produk Terbaik'))
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
                                        Anda tidak memiliki izin untuk melihat daftar best items.
                                        Silakan hubungi administrator untuk mendapatkan akses.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Rekapitulasi" style="background-image: none;" checked/>
                <div class="tab-content bg-base-200 p-1">
                    @can('akses', 'Kunjungan Pasien Rekapitulasi Card')
                    <livewire:Kunjungan.Card.Rekapitulasi />
                    @endcan
                    @can('akses', 'Kunjungan Pasien Rekapitulasi Harian')
                    <livewire:Kunjungan.Rekapitulasi.GrafikHarian />
                    @endcan
                    @can('akses', 'Kunjungan Pasien Rekapitulasi Bulanan')
                    <livewire:Kunjungan.Rekapitulasi.GrafikBulanan />
                    @endcan
                    @can('akses', 'Kunjungan Pasien Rekapitulasi Tahunan')
                    <livewire:Kunjungan.Rekapitulasi.GrafikTahunan />
                    @endcan
                    @if (
                        !Gate::allows('akses','Kunjungan Pasien Rekapitulasi Harian') &&
                        !Gate::allows('akses','Kunjungan Pasien Rekapitulasi Bulanan') &&
                        !Gate::allows('akses','Kunjungan Pasien Rekapitulasi Tahunan') &&
                        !Gate::allows('akses','Kunjungan Pasien Rekapitulasi Card')
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
                                        Anda tidak memiliki izin untuk melihat laporan rekapitulasi kunjungan pasien.
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