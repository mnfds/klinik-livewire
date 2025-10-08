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
                Detail Resep Obat
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                <!-- ATAS (opsional, kalau butuh full width di atas konten) -->
                <div class="lg:col-span-4">
                    <div class="bg-base-100 shadow rounded-box p-4">
                        <div class="tabs tabs-lift">

                            <!-- Tab Informasi Pasien -->
                            <input type="radio" name="info" class="tab bg-transparent text-base-content"
                                aria-label="Informasi Pasien" style="background-image: none;" />
                            <div class="tab-content bg-base-100 border-base-300 p-6">
                                <h3 class="font-semibold mb-2">Informasi Pasien</h3>
                                <div class="space-y-2 text-sm mt-2">

                                    <!-- Baris 1 -->
                                    <div class="grid grid-cols-2 gap-x-6">
                                        <div class="flex">
                                            <div class="w-32 font-bold">Nama</div>
                                            <div>: {{ $pasienTerdaftar->pasien->nama }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-bold">No. RM</div>
                                            <div>: {{ $pasienTerdaftar->pasien->no_register }}</div>
                                        </div>
                                    </div>

                                    <!-- Baris 2 -->
                                    <div class="grid grid-cols-2 gap-x-6">
                                        <div class="flex">
                                            <div class="w-32 font-bold">Tanggal Lahir</div>
                                            <div>:
                                                {{
                                                \Carbon\Carbon::parse($pasienTerdaftar->pasien->tanggal_lahir)->translatedFormat('d
                                                F Y') }}
                                            </div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-bold">No. IHS</div>
                                            <div>: {{ $pasienTerdaftar->pasien->no_ihs }}</div>
                                        </div>
                                    </div>

                                    <!-- Baris 3 -->
                                    <div class="grid grid-cols-2 gap-x-6">
                                        <div class="flex">
                                            <div class="w-32 font-bold">Jenis Kelamin</div>
                                            <div>: {{ $pasienTerdaftar->pasien->jenis_kelamin }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-bold">NIK</div>
                                            <div>: {{ $pasienTerdaftar->pasien->nik }}</div>
                                        </div>
                                    </div>

                                    <!-- Baris 4 -->
                                    <div class="grid grid-cols-2 gap-x-6">
                                        <div class="flex">
                                            <div class="w-32 font-bold">Poliklinik</div>
                                            <div>: {{ $pasienTerdaftar->poliklinik->nama_poli }}</div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-bold">Nakes</div>
                                            <div>: {{ $pasienTerdaftar->dokter->nama_dokter }}</div>
                                        </div>
                                    </div>

                                </div>
                                <h3 class="font-semibold mt-2 mb-1">Informasi Tambahan</h3>
                                <div class="space-y-2 text-sm mt-2">
                                    <!-- Baris 1 -->
                                    <div class="grid grid-cols-2 gap-x-6">
                                        <div class="flex">
                                            <div class="w-32 font-bold">Status Perokok</div>
                                            <div>: {{ $pasienTerdaftar->rekamMedis->dataKesehatanRM->status_perokok ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-bold">Status Kehamilan</div>
                                            <div>
                                                : {{ $pasienTerdaftar->rekamMedis->dataEstetikaRM?->usia_kehamilan 
                                                    ? $pasienTerdaftar->rekamMedis->dataEstetikaRM->usia_kehamilan . ' bulan' 
                                                    : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Informasi Medis -->
                            <input type="radio" name="info" class="tab bg-transparent text-base-content"
                                aria-label="Informasi Medis" checked="checked" style="background-image: none;" />
                            <div class="tab-content bg-base-100 border-base-300 p-6">
                                <h3 class="font-semibold mb-2">Informasi Medis</h3>
                                <div class="space-y-4 text-sm mt-2">

                                    <!-- Alergi Obat -->
                                    <div>
                                        <div class="font-bold mb-1">Alergi Obat</div>
                                        @php
                                        $alergiObat =
                                        json_decode($pasienTerdaftar->rekamMedis->dataKesehatanRM->riwayat_alergi_obat
                                        ?? '[]', true);
                                        @endphp
                                        @if(!empty($alergiObat))
                                        <ul class="list-disc list-inside">
                                            @foreach($alergiObat as $item)
                                            <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                        @else
                                        <div>-</div>
                                        @endif
                                    </div>

                                    <!-- Alergi Lainnya -->
                                    <div>
                                        <div class="font-bold mb-1">Alergi Lainnya</div>
                                        @php
                                        $alergiLainnya =
                                        json_decode($pasienTerdaftar->rekamMedis->dataKesehatanRM->riwayat_alergi_lainnya
                                        ?? '[]', true);
                                        @endphp
                                        @if(!empty($alergiLainnya))
                                        <ul class="list-disc list-inside">
                                            @foreach($alergiLainnya as $item)
                                            <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                        @else
                                        <div>-</div>
                                        @endif
                                    </div>

                                    <!-- Obat Sedang Dikonsumsi -->
                                    <div>
                                        <div class="font-bold mb-1">Obat Sedang Dikonsumsi</div>
                                        @php
                                        $obatSedangDikonsumsi =
                                        json_decode($pasienTerdaftar->rekamMedis->dataKesehatanRM->obat_sedang_dikonsumsi
                                        ?? '[]', true);
                                        @endphp
                                        @if(!empty($obatSedangDikonsumsi))
                                        <ul class="list-disc list-inside">
                                            @foreach($obatSedangDikonsumsi as $item)
                                            <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                        @else
                                        <div>-</div>
                                        @endif
                                    </div>

                                    <!-- Riwayat Penyakit -->
                                    <div>
                                        <div class="font-bold mb-1">Riwayat Penyakit</div>
                                        @php
                                        $riwayatPenyakit =
                                        json_decode($pasienTerdaftar->rekamMedis->dataKesehatanRM->riwayat_penyakit ??
                                        '[]', true);
                                        @endphp
                                        @if(!empty($riwayatPenyakit))
                                        <ul class="list-disc list-inside">
                                            @foreach($riwayatPenyakit as $item)
                                            <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                        @else
                                        <div>-</div>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <!-- Tab Tanda Vital -->
                            <input type="radio" name="info" class="tab bg-transparent text-base-content"
                                aria-label="Informasi Tanda Vital" style="background-image: none;" />
                            <div class="tab-content bg-base-100 border-base-300 p-6">
                                <h3 class="font-semibold mb-2">Tanda Vital</h3>
                                <div class="space-y-2 text-sm mt-2">
                                    <div class="grid grid-cols-2 gap-x-6">
                                        <div class="flex">
                                            <div class="w-32 font-bold">Suhu Tubuh</div>
                                            <div>
                                                : {{ $pasienTerdaftar->rekamMedis->tandaVitalRM->suhu_tubuh ?? '-' }}°C
                                            </div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-bold">Nadi</div>
                                            <div>
                                                : {{ $pasienTerdaftar->rekamMedis->tandaVitalRM->nadi ?? '-' }} x/menit
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-x-6">
                                        <div class="flex">
                                            <div class="w-32 font-bold">Tekanan Darah</div>
                                            <div>
                                                : {{ $pasienTerdaftar->rekamMedis->tandaVitalRM->sistole ?? '-' }}
                                                /{{ $pasienTerdaftar->rekamMedis->tandaVitalRM->diastole ?? '-' }} mmHg
                                            </div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-bold">Frekuensi Napas</div>
                                            <div>
                                                : {{ $pasienTerdaftar->rekamMedis->tandaVitalRM->frekuensi_pernapasan ??
                                                '-' }} x/menit
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Pemeriksaan Fisik -->
                            <input type="radio" name="info" class="tab bg-transparent text-base-content"
                                aria-label="Informasi Pemeriksaan Fisik" style="background-image: none;" />
                            <div class="tab-content bg-base-100 border-base-300 p-6">
                                <h3 class="font-semibold mb-2">Pemeriksaan Fisik</h3>
                                <div class="space-y-2 text-sm mt-2">
                                    <div class="grid grid-cols-2 gap-x-6">
                                        <div class="flex">
                                            <div class="w-32 font-bold">Tinggi Badan</div>
                                            <div>
                                                : {{ $pasienTerdaftar->rekamMedis->pemeriksaanFisikRM->tinggi_badan ??
                                                '-' }} cm
                                            </div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-bold">Berat Badan</div>
                                            <div>
                                                : {{ $pasienTerdaftar->rekamMedis->pemeriksaanFisikRM->berat_badan ??
                                                '-' }} kg
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-x-6">
                                        <div class="flex">
                                            <div class="w-32 font-bold">IMT</div>
                                            <div>
                                                : {{ $pasienTerdaftar->rekamMedis->pemeriksaanFisikRM->imt ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KONTEN UTAMA -->
                <div class="lg:col-span-4">
                    <div class="bg-base-100 shadow rounded-box">
                        <div class="p-6 text-base-content space-y-6">
                            <form wire:submit.prevent="store">
                                {{-- INPUT OBAT NON RACIK DAN RACIK --}}
                                <div class="mb-2">
                                    <div class="tabs tabs-lift">
                                        {{-- NON RACIK --}}
                                        <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Obat Non Racik" checked="checked" style="background-image: none;"/>
                                        <div class="tab-content bg-base-100 border-base-300 p-6">
                                            {{-- INFORMASI OBAT NON RACIK DARI DOKTER --}}
                                            <div class="mb-4">
                                                <h3 class="font-semibold mb-4">
                                                    Obat Non Racik
                                                </h3>
                                                <details class="collapse bg-base-100 border-base-300 border">
                                                    <summary class="collapse-title font-semibold">Resep Dokter</summary>

                                                    <div class="collapse-content text-sm space-y-4">
                                                        @forelse ($obat_dokter_nonracikan as $nonracik)
                                                            <div class="p-4 rounded-lg border border-base-300 bg-base-200">
                                                                <p><span class="font-semibold">Nama Obat:</span> {{ $nonracik['nama_obat_non_racikan'] }}</p>
                                                                <p><span class="font-semibold">Jumlah:</span> {{ $nonracik['jumlah_obat_non_racikan'] }} {{ $nonracik['satuan_obat_non_racikan'] }}</p>
                                                                <p><span class="font-semibold">Dosis:</span> {{ $nonracik['dosis_obat_non_racikan'] }} x {{ $nonracik['hari_obat_non_racikan'] }}</p>
                                                                <p class="col-span-2"><span class="font-semibold">Aturan Pakai:</span> {{ $nonracik['aturan_pakai_obat_non_racikan'] }}</p>
                                                            </div>
                                                        @empty
                                                            <p class="text-gray-500 italic">Tidak ada data resep dari dokter.</p>
                                                        @endforelse
                                                    </div>
                                                </details>
                                            </div>
                                            {{-- INPUT OBAT NON RACIK DARI APOTEKER --}}
                                            <div>
                                                <h3 class="font-semibold mb-4">Input Obat</h3>

                                                <div class="rounded-lg border border-base-300 bg-base-200 p-4 space-y-4">
                                                    {{-- BARIS 1 --}}
                                                    <div class="grid grid-cols-1 md:grid-cols-8 gap-4 items-end">
                                                        {{-- Nama Obat --}}
                                                        <div class="form-control md:col-span-3">
                                                            <label class="label font-semibold">Nama</label>
                                                            <input wire:model.defer="input_apoteker_nonracikan.nama_obat" type="text" class="input input-bordered w-full" placeholder="Nama obat" />
                                                        </div>

                                                        {{-- Jumlah --}}
                                                        <div class="form-control md:col-span-1">
                                                            <label class="label font-semibold">Jumlah</label>
                                                            <input wire:model.defer="input_apoteker_nonracikan.jumlah_obat" type="number" class="input input-bordered w-full" placeholder="0" />
                                                        </div>

                                                        {{-- Satuan --}}
                                                        <div class="form-control md:col-span-1">
                                                            <label class="label font-semibold">Satuan</label>
                                                            <input wire:model.defer="input_apoteker_nonracikan.satuan_obat" type="text" class="input input-bordered w-full" placeholder="Tablet" />
                                                        </div>

                                                        {{-- Harga --}}
                                                        <div class="form-control md:col-span-1">
                                                            <label class="label font-semibold">Harga</label>
                                                            <input wire:model.defer="input_apoteker_nonracikan.harga_obat" type="text" class="input input-bordered w-full" placeholder="Rp 0" />
                                                        </div>

                                                        {{-- Subtotal --}}
                                                        <div class="form-control md:col-span-2">
                                                            <label class="label font-semibold">Subtotal</label>
                                                            <input wire:model.defer="input_apoteker_nonracikan.total_obat" type="text" class="input input-bordered w-full" placeholder="Rp 0" />
                                                        </div>
                                                    </div>

                                                    {{-- BARIS 2 --}}
                                                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                                                        {{-- Dosis x Hari (inline) --}}
                                                        <div class="form-control md:col-span-2">
                                                            <label class="label font-semibold">Dosis × Hari</label>
                                                            <div class="flex items-center gap-2">
                                                                <input wire:model.defer="input_apoteker_nonracikan.dosis" type="text" class="input input-bordered w-full" placeholder="3" />
                                                                <span class="text-lg font-semibold">×</span>
                                                                <input wire:model.defer="input_apoteker_nonracikan.hari" type="text" class="input input-bordered w-full" placeholder="1" />
                                                            </div>
                                                        </div>

                                                        {{-- Aturan Pakai --}}
                                                        <div class="form-control md:col-span-4">
                                                            <label class="label font-semibold">Instruksi Pemakaian</label>
                                                            <input wire:model.defer="input_apoteker_nonracikan.aturan_pakai" type="text" class="input input-bordered w-full" placeholder="sesudah makan" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- RACIK --}}
                                        <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Obat Racikan" style="background-image: none;"/>
                                        <div class="tab-content bg-base-100 border-base-300 p-6">
                                            {{-- INFORMASI OBAT RACIKAN DARI DOKTER --}}
                                            <div class="mb-4">
                                                <h3 class="font-semibold mb-4">
                                                    Obat Racik
                                                </h3>
                                                <details class="collapse bg-base-100 border-base-300 border">
                                                    <summary class="collapse-title font-semibold">Resep Dokter</summary>

                                                    <div class="collapse-content text-sm space-y-4">
                                                        @forelse ($obat_dokter_racikan as $racik)
                                                            <div class="p-4 rounded-lg border border-base-300 bg-base-200">
                                                                <p><span class="font-semibold">Nama Obat:</span> {{ $racik['nama_racikan'] }}</p>
                                                                <p><span class="font-semibold">Jumlah:</span> {{ $racik['jumlah_racikan'] }} {{ $racik['satuan_racikan'] }}</p>
                                                                <p><span class="font-semibold">Dosis:</span> {{ $racik['dosis_obat_racikan'] }} x {{ $racik['hari_obat_racikan'] }}</p>
                                                                <p class="col-span-2"><span class="font-semibold">Aturan Pakai:</span> {{ $racik['aturan_pakai_racikan'] }}</p>
                                                                <div class="mt-2 p-2 border rounded bg-base-100">
                                                                    <h4>Bahan Racikan:</h4>
                                                                    @foreach ($racik['bahan'] as $bahan)
                                                                    <div class="text-sm">{{ $bahan['nama_obat_racikan'] }}, {{ $bahan['jumlah_obat_racikan'] }} {{ $bahan['satuan_obat_racikan'] }}</div>
                                                                        
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <p class="text-gray-500 italic">Tidak ada data resep dari dokter.</p>
                                                        @endforelse
                                                    </div>
                                                </details>
                                            </div>
                                            {{-- INPUT NAMA RACIKAN DAN OBAT YANG DI RACIK DARI APOTEKER --}}
                                            <div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- EMBALASE DAN TUSLAH --}}
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end mb-2 border bg-base-200 border-base-300 rounded-lg p-3">
                                    {{-- Tuslah --}}
                                    <div class="form-control">
                                        <label class="label font-semibold">Tuslah</label>
                                        <input wire:model.defer="data_apoteker.tuslah"
                                            type="text"
                                            class="input input-bordered w-full"
                                            placeholder="Rp. 0" />
                                    </div>

                                    {{-- Embalase --}}
                                    <div class="form-control">
                                        <label class="label font-semibold">Embalase</label>
                                        <input wire:model.defer="data_apoteker.embalase"
                                            type="text"
                                            class="input input-bordered w-full"
                                            placeholder="Rp. 0" />
                                    </div>

                                    {{-- Rekam Medis ID (hidden) --}}
                                    <input type="hidden" wire:model.defer="data_apoteker.rekam_medis_id">
                                </div>
                                {{-- RINCIAN HARGA --}}
                                <div class="items-end border bg-base-200 border-base-300 rounded-lg p-3 my-3">
                                    <div class="border-b-2 border-dotted border-neutral mb-2">
                                        <p class="font-semibold text-sm">Total Obat Non Racik : Rp.</p>
                                        <p class="font-semibold text-sm">Total Obat Racik : Rp.</p>
                                    </div>
                                    <div class="mt-2">
                                        <p class="font-semibold text-sm">Total Keseluruhan : Rp.</p>
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary w-full">
                                        simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>