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
                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Wanita" style="background-image: none;" checked/>
                <div class="tab-content bg-base-200 p-1">
                    <livewire:Kunjungan.Card.Wanita />
                    <livewire:Kunjungan.Wanita.GrafikHarian />
                    <livewire:Kunjungan.Wanita.GrafikBulanan />
                    <livewire:Kunjungan.Wanita.GrafikTahunan />
                </div>
                
                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Laki-Laki" style="background-image: none;"/>
                <div class="tab-content bg-base-200 p-1">
                    <livewire:Kunjungan.Card.Lakilaki />
                </div>

                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Rekapitulasi" style="background-image: none;"/>
                <div class="tab-content bg-base-200 p-1">
                    <livewire:Kunjungan.Card.Rekapitulasi />
                </div>

            </div>
        </div>
    </div>
</div>