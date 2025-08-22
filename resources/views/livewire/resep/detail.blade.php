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
                        <a href="{{ route('resep.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Resep
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('resep.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Detail Resep
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Detail
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                <div class="p-6 text-base-content space-y-4">
                    <div class="tabs tabs-lift">
                        <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Obat Non Racik" style="background-image: none;" />
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                            <h3>Obat Non Racikan</h3>
                            <ul>
                            @foreach($pasienTerdaftar->rekamMedis->obatNonRacikanRM as $nonRacik)
                                <li>{{ $nonRacik->nama_obat_non_racikan }} - {{ $nonRacik->dosis_obat_non_racikan }} x {{ $nonRacik->hari_obat_non_racikan }}, {{ $nonRacik->aturan_pakai_obat_non_racikan }}</li>
                            @endforeach
                            </ul>
                        </div>

                        <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Obat Racikan" checked="checked" style="background-image: none;" />
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                            <h3>Obat Racikan</h3>
                            @foreach($pasienTerdaftar->rekamMedis->obatRacikanRM as $racikan)
                                <div>
                                    <strong>{{ $racikan->nama_racikan }}</strong>
                                    <ul>
                                        @foreach($racikan->bahanRacikan as $bahan)
                                            <li>{{ $bahan->nama_obat_racikan }} - {{ $bahan->jumlah }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>