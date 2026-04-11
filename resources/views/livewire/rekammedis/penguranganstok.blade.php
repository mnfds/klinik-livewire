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
                        <a class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Rekam Medis Pasien
                        </a>
                    </li>
                    <li>
                        <a class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Penggunaan Bahan
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Penggunaan Bahan Pada Tindakan
            </h1>
        </div>
        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <div class="lg:col-span-3 space-y-6">
                    @php $bahanCounter = 0; @endphp
                    <form wire:submit.prevent="saved" class="space-y-6">

                        {{-- ================= RENCANA LAYANAN ================= --}}
                        @if($rencanaDetail['rencana_layanan']->count())
                            <div class="p-4 border-base-300 rounded-lg shadow bg-base-100">
                                <h2 class="text-lg font-bold mb-3">Pelayanan Medis</h2>

                                @foreach($rencanaDetail['rencana_layanan'] as $layanan)
                                    <div class="mb-4 border-b pb-3">
                                        <p class="font-semibold">
                                            {{ $layanan['nama_pelayanan'] }} ({{ $layanan['jumlah'] }}x)
                                        </p>

                                        <ul class="ml-5 list-disc text-sm text-gray-700">
                                            @foreach($layanan['bahan_baku'] as $bahan)
                                                @if($bahan['bahan_id'])
                                                    <li class="flex items-center gap-2 mt-1">
                                                        <span class="flex-1">{{ $bahan['nama_bahan'] }}</span>
                                                        <input
                                                            type="number"
                                                            min="0"
                                                            wire:model.defer="bahanInputs.{{ $bahanCounter }}.qty"
                                                            class="input input-sm input-bordered w-24 text-right"
                                                        >
                                                    </li>
                                                    @php $bahanCounter++; @endphp
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @endif


                        {{-- ================= RENCANA TREATMENT ================= --}}
                        @if($rencanaDetail['rencana_treatment']->count())
                            <div class="p-4 border-base-300 rounded-lg shadow bg-base-100">
                                <h2 class="text-lg font-bold mb-3">Treatment</h2>

                                @foreach($rencanaDetail['rencana_treatment'] as $treatment)
                                    <div class="mb-4 border-b pb-3">
                                        <p class="font-semibold">
                                            {{ $treatment['nama_treatment'] }} ({{ $treatment['jumlah'] }}x)
                                        </p>

                                        <ul class="ml-5 list-disc text-sm text-gray-700">
                                            @foreach($treatment['bahan_baku'] as $bahan)
                                                @if($bahan['bahan_id'])
                                                    <li class="flex items-center gap-2 mt-1">
                                                        <span class="flex-1">{{ $bahan['nama_bahan'] }}</span>
                                                        <input
                                                            type="number"
                                                            min="0"
                                                            wire:model.defer="bahanInputs.{{ $bahanCounter }}.qty"
                                                            class="input input-sm input-bordered w-24 text-right"
                                                        >
                                                    </li>
                                                    @php $bahanCounter++; @endphp
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @endif


                        {{-- ================= BUNDLING USAGE: TREATMENT ================= --}}
                        {{-- Item treatment dari sisa bundling yang diambil pada kunjungan ini --}}
                        @if($rencanaDetail['bundling_usage_treatment']->count())
                            <div class="p-4 border-base-300 rounded-lg shadow bg-base-100">
                                <h2 class="text-lg font-bold mb-1">Item Bundling — Treatment</h2>
                                <p class="text-xs text-gray-500 mb-3">
                                    Bahan berikut diambil dari sisa bundling pada kunjungan ini.
                                </p>

                                @foreach($rencanaDetail['bundling_usage_treatment'] as $usage)
                                    <div class="mb-4 border-b pb-3">
                                        <p class="font-semibold">
                                            {{ $usage['nama_bundling'] }}
                                            <span class="font-normal text-gray-500 text-sm">
                                                — {{ $usage['nama_treatment'] }}
                                            </span>
                                        </p>

                                        <ul class="ml-5 list-disc text-sm text-gray-700">
                                            @foreach($usage['bahan_baku'] as $bahan)
                                                @if($bahan['bahan_id'])
                                                    <li class="flex items-center gap-2 mt-1">
                                                        <span class="flex-1">{{ $bahan['nama_bahan'] }}</span>
                                                        <input
                                                            type="number"
                                                            min="0"
                                                            wire:model.defer="bahanInputs.{{ $bahanCounter }}.qty"
                                                            class="input input-sm input-bordered w-24 text-right"
                                                        >
                                                    </li>
                                                    @php $bahanCounter++; @endphp
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @endif


                        {{-- ================= BUNDLING USAGE: PELAYANAN ================= --}}
                        {{-- Item pelayanan dari sisa bundling yang diambil pada kunjungan ini --}}
                        @if($rencanaDetail['bundling_usage_pelayanan']->count())
                            <div class="p-4 border-base-300 rounded-lg shadow bg-base-100">
                                <h2 class="text-lg font-bold mb-1">Item Bundling — Pelayanan</h2>
                                <p class="text-xs text-gray-500 mb-3">
                                    Bahan berikut diambil dari sisa bundling pada kunjungan ini.
                                </p>

                                @foreach($rencanaDetail['bundling_usage_pelayanan'] as $usage)
                                    <div class="mb-4 border-b pb-3">
                                        <p class="font-semibold">
                                            {{ $usage['nama_bundling'] }}
                                            <span class="font-normal text-gray-500 text-sm">
                                                — {{ $usage['nama_pelayanan'] }}
                                            </span>
                                        </p>

                                        <ul class="ml-5 list-disc text-sm text-gray-700">
                                            @foreach($usage['bahan_baku'] as $bahan)
                                                @if($bahan['bahan_id'])
                                                    <li class="flex items-center gap-2 mt-1">
                                                        <span class="flex-1">{{ $bahan['nama_bahan'] }}</span>
                                                        <input
                                                            type="number"
                                                            min="0"
                                                            wire:model.defer="bahanInputs.{{ $bahanCounter }}.qty"
                                                            class="input input-sm input-bordered w-24 text-right"
                                                        >
                                                    </li>
                                                    @php $bahanCounter++; @endphp
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @endif


                        {{-- ================= JIKA TIDAK ADA DATA ================= --}}
                        @if(
                            !$rencanaDetail['rencana_layanan']->count() &&
                            !$rencanaDetail['rencana_treatment']->count() &&
                            !$rencanaDetail['bundling_usage_treatment']->count() &&
                            !$rencanaDetail['bundling_usage_pelayanan']->count()
                        )
                            <div class="p-6 text-center text-gray-500 border rounded-lg">
                                Tidak ada rencana layanan, treatment, maupun item bundling yang diambil.
                            </div>
                        @endif

                    </form>
                </div>

                <div class="lg:col-span-1">
                    <div class="sticky top-10 space-y-6">
                        <div class="bg-base-100 shadow rounded-box p-4 pb-7">
                            <h3 class="font-semibold mb-4">Aksi</h3>
                            @can('akses', 'Rekam Medis Tambah')
                            <button wire:click.prevent="saved" class="btn btn-primary w-full mb-1" wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="fa-solid fa-plus"></i> Simpan</span>
                                <span wire:loading.inline>Loading...</span>
                            </button>
                            @endcan
                            @can('akses', 'Pasien Riwayat Rekam Medis')
                            <a wire:navigate href="{{ route('rekam-medis-pasien.data', ['pasien_id' => $pasienTerdaftar->pasien->id]) }}"
                                class="btn btn-info mb-1 w-full" >
                                <i class="fa-solid fa-book-medical"></i> Riwayat Rekam Medis
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>