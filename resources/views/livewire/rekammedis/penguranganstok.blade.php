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
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Pasien
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Detail Pasien
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Rekam Medis Pasien
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Evaluasi
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                <div class="space-y-6">

                    {{-- ================= RENCANA LAYANAN ================= --}}
                    @if($rencanaDetail['rencana_layanan']->count())
                    <div class="p-4 border rounded-lg shadow">
                        <h2 class="text-lg font-bold mb-3">Rencana Layanan</h2>

                        @foreach($rencanaDetail['rencana_layanan'] as $layanan)
                        <div class="mb-4 border-b pb-3">
                            <p class="font-semibold">
                                {{ $layanan['nama_pelayanan'] }}
                                ({{ $layanan['jumlah'] }}x)
                            </p>

                            <ul class="ml-5 list-disc text-sm text-gray-700">
                                @foreach($layanan['bahan_baku'] as $bahan)
                                <li>
                                    {{ $bahan['nama_bahan'] }}
                                    - Total Pakai: {{ $bahan['total_pakai'] }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endforeach
                    </div>
                    @endif


                    {{-- ================= RENCANA TREATMENT ================= --}}
                    @if($rencanaDetail['rencana_treatment']->count())
                    <div class="p-4 border rounded-lg shadow">
                        <h2 class="text-lg font-bold mb-3">Rencana Treatment</h2>

                        @foreach($rencanaDetail['rencana_treatment'] as $treatment)
                        <div class="mb-4 border-b pb-3">
                            <p class="font-semibold">
                                {{ $treatment['nama_treatment'] }}
                                ({{ $treatment['jumlah'] }}x)
                            </p>

                            <ul class="ml-5 list-disc text-sm text-gray-700">
                                @foreach($treatment['bahan_baku'] as $bahan)
                                <li>
                                    {{ $bahan['nama_bahan'] }}
                                    - Total Pakai: {{ $bahan['total_pakai'] }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endforeach
                    </div>
                    @endif


                    {{-- ================= RENCANA BUNDLING ================= --}}
                    @if($rencanaDetail['rencana_bundling']->count())
                    <div class="p-4 border rounded-lg shadow">
                        <h2 class="text-lg font-bold mb-3">Rencana Bundling</h2>

                        @foreach($rencanaDetail['rencana_bundling'] as $bundling)
                        <div class="mb-6 border-b pb-4">

                            <p class="font-semibold text-blue-600">
                                {{ $bundling['nama_bundling'] }}
                                ({{ $bundling['jumlah_bundling'] }}x)
                            </p>

                            {{-- Treatment dalam Bundling --}}
                            @if($bundling['treatments']->count())
                            <div class="ml-4 mt-2">
                                <p class="font-medium">Treatment:</p>
                                @foreach($bundling['treatments'] as $treatment)
                                <div class="ml-4 mb-2">
                                    <p>{{ $treatment['nama_treatment'] }}</p>
                                    <ul class="ml-5 list-disc text-sm text-gray-700">
                                        @foreach($treatment['bahan_baku'] as $bahan)
                                        <li>
                                            {{ $bahan['nama_bahan'] }}
                                            - Total Pakai: {{ $bahan['total_pakai'] }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            {{-- Pelayanan dalam Bundling --}}
                            @if($bundling['pelayanans']->count())
                            <div class="ml-4 mt-2">
                                <p class="font-medium">Pelayanan:</p>
                                @foreach($bundling['pelayanans'] as $pelayanan)
                                <div class="ml-4 mb-2">
                                    <p>{{ $pelayanan['nama_pelayanan'] }}</p>
                                    <ul class="ml-5 list-disc text-sm text-gray-700">
                                        @foreach($pelayanan['bahan_baku'] as $bahan)
                                        <li>
                                            {{ $bahan['nama_bahan'] }}
                                            - Total Pakai: {{ $bahan['total_pakai'] }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endforeach
                            </div>
                            @endif

                        </div>
                        @endforeach
                    </div>
                    @endif


                    {{-- ================= JIKA TIDAK ADA DATA ================= --}}
                    @if(
                    !$rencanaDetail['rencana_layanan']->count() &&
                    !$rencanaDetail['rencana_treatment']->count() &&
                    !$rencanaDetail['rencana_bundling']->count()
                    )
                    <div class="p-6 text-center text-gray-500 border rounded-lg">
                        Tidak ada rencana layanan, treatment, atau bundling.
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>