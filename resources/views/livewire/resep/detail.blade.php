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
                            <form wire:submit.prevent="create">
                                {{-- Data Resep --}}
                                <div class="tabs tabs-lift" x-data="totalKeseluruhan()">

                                    <!-- Tab Non Racikan -->
                                    <input type="radio" name="tab_resep" class="tab bg-transparent text-base-content"
                                        aria-label="Obat Non Racik" checked="checked" style="background-image: none;" />
                                    <div class="tab-content bg-base-100 border-base-300 p-6">
                                        <h3 class="font-semibold mb-4">Obat Non Racikan</h3>

                                        <!-- Resep Dokter -->
                                        <details class="collapse collapse-arrow border rounded-lg mb-4">
                                            <summary class="collapse-title font-semibold">Resep Dokter</summary>
                                            <div class="collapse-content text-sm space-y-3">
                                                @forelse($obatNonRacikanItems as $obat)
                                                <div class="p-3 border rounded-lg bg-base-200">
                                                    <strong>{{ $obat['nama_obat_non_racikan'] }}</strong><br>
                                                    Jumlah: {{ $obat['jumlah_obat_non_racikan'] }} {{
                                                    $obat['satuan_obat_non_racikan'] }} <br>
                                                    Dosis: {{ $obat['dosis_obat_non_racikan'] }} × {{
                                                    $obat['hari_obat_non_racikan'] }} hari <br>
                                                    Aturan Pakai: {{ $obat['aturan_pakai_obat_non_racikan'] }}
                                                </div>
                                                @empty
                                                <p class="text-sm text-gray-500">Tidak ada obat non racikan.</p>
                                                @endforelse
                                            </div>
                                        </details>

                                        <!-- Input Apoteker Non Racikan -->
                                        <div x-data="obatManager('nonracik')" x-init="$watch('inputs', value => {
                                                        $refs.nonracikHidden.value = JSON.stringify(value);
                                                        $refs.nonracikHidden.dispatchEvent(new Event('input'));
                                                    }, {deep:true})"
                                            class="border rounded-lg bg-base-100 p-2">
                                            <input type="hidden" x-ref="nonracikHidden" wire:model="obatNonracikFinal">
                                            <template x-for="(item, i) in inputs" :key="item.uid">
                                                <div class="mx-1 my-2">
                                                    <div class="flex gap-2 items-start">
                                                        <!-- Nama Obat -->
                                                        <div class="relative flex-1" x-data="searchObat(i, inputs)">
                                                            <input type="text" x-model="query" @input="search()"
                                                                @focus="open = true" @click.away="open = false"
                                                                class="input input-bordered w-full"
                                                                placeholder="Cari nama obat..." />

                                                            <div x-show="open && results.length > 0"
                                                                class="absolute z-50 bg-white border w-full max-h-48 overflow-y-auto rounded-lg mt-1 shadow">
                                                                <template x-for="result in results" :key="result.id">
                                                                    <div @click="select(result)"
                                                                        class="px-3 py-2 hover:bg-blue-100 cursor-pointer">
                                                                        <span x-text="result.text"></span>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>

                                                        <!-- Jumlah -->
                                                        <input type="number" x-model="item.jumlah"
                                                            @input="$dispatch('hitung-total',{index:i})"
                                                            class="input input-bordered w-20" placeholder="Jumlah" />

                                                        <!-- Satuan -->
                                                        <input type="text" x-model="item.satuan" readonly
                                                            class="input input-bordered w-20 bg-base-200"
                                                            placeholder="Satuan" />

                                                        <!-- Harga Satuan -->
                                                        <input type="text" x-model="item.harga_satuan_display" readonly
                                                            class="input input-bordered w-28 bg-base-200 text-right"
                                                            placeholder="Harga" />

                                                        <!-- Total -->
                                                        <input type="text" x-model="item.total_display" readonly
                                                            class="input input-bordered w-28 bg-base-200 text-right"
                                                            placeholder="Total" />

                                                        <button type="button" class="btn btn-error btn-sm"
                                                            @click="inputs.splice(i,1); updateGrandTotal()">✕</button>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Tombol Tambah -->
                                            <button type="button" class="btn btn-primary btn-sm" @click="addInput()">+
                                                Tambah Obat</button>
                                        </div>
                                    </div>

                                    <!-- Tab Racikan -->
                                    <input type="radio" name="tab_resep" class="tab bg-transparent text-base-content"
                                        aria-label="Obat Racikan" style="background-image: none;" />
                                    <div class="tab-content bg-base-100 border-base-300 p-6">
                                        <h3 class="font-semibold mb-4">Obat Racikan</h3>

                                        <!-- Resep Dokter -->
                                        <details class="collapse collapse-arrow border rounded-lg mb-4">
                                            <summary class="collapse-title font-semibold">Resep Dokter</summary>
                                            <div class="collapse-content text-sm space-y-3">
                                                @forelse($obatRacikanItems as $racik)
                                                <div class="p-3 border rounded-lg bg-base-200">
                                                    <strong>{{ $racik['nama_racikan'] }}</strong><br>
                                                    Jumlah: {{ $racik['jumlah_racikan'] }} {{ $racik['satuan_racikan']
                                                    }}
                                                    <br>
                                                    Dosis: {{ $racik['dosis_obat_racikan'] }} × {{
                                                    $racik['hari_obat_racikan'] }} hari <br>
                                                    Aturan Pakai: {{ $racik['aturan_pakai_racikan'] }} <br>
                                                    <div class="mt-2 p-2 border rounded bg-base-100">
                                                        <h4 class="font-semibold mb-1">Bahan Racikan:</h4>
                                                        @forelse($racik['bahan'] as $bahan)
                                                        <div class="text-sm">
                                                            - {{ $bahan['nama_obat_racikan'] }} : {{
                                                            $bahan['jumlah_obat_racikan'] }} {{
                                                            $bahan['satuan_obat_racikan'] }}
                                                        </div>
                                                        @empty
                                                        <p class="text-sm text-gray-500">Tidak ada bahan racikan.</p>
                                                        @endforelse
                                                    </div>
                                                </div>
                                                @empty
                                                <p class="text-sm text-gray-500">Tidak ada obat racikan.</p>
                                                @endforelse
                                            </div>
                                        </details>

                                        <!-- Input Apoteker Racikan -->
<!-- Racikan Per Resep -->
<div x-data="racikanManager()" 
     x-init="
        init();
        $watch('racikanList', value => {
            $refs.racikanHidden.value = JSON.stringify(value);
            $refs.racikanHidden.dispatchEvent(new Event('input'));
        }, { deep: true })
     "
     class="border rounded-lg bg-base-100 p-2">

    <!-- Hidden input untuk Livewire -->
    <input type="hidden" x-ref="racikanHidden" wire:model="obatRacikanFinal">

    <!-- Loop tiap racikan -->
    <template x-for="(racikan, rIndex) in racikanList" :key="racikan.uid">
        <div class="border rounded-lg p-4 mb-4 bg-base-100">

            <!-- Nama Racikan -->
            <div class="flex gap-2 items-center mb-3">
                <input type="text" x-model="racikan.nama_racikan"
                    placeholder="Nama Racikan" class="input input-bordered flex-1"/>
                <button type="button" @click="removeRacikan(rIndex)" class="btn btn-error btn-sm">Hapus Racikan</button>
            </div>

            <!-- Loop bahan racikan -->
            <template x-for="(bahan, bIndex) in racikan.bahan" :key="bahan.uid">
                <div class="flex gap-2 mb-2 items-start">
                    <div class="relative flex-1" x-data="searchObat(rIndex, bIndex, racikanList)">
                        <input type="text" x-model="query" @input="search()" @focus="open = true"
                            @click.away="open = false" placeholder="Cari nama obat..."
                            class="input input-bordered w-full" />
                        <div x-show="open && results.length > 0"
                            class="absolute z-50 bg-white border w-full max-h-48 overflow-y-auto rounded-lg mt-1 shadow">
                            <template x-for="result in results" :key="result.id">
                                <div @click="select(result)" class="px-3 py-2 hover:bg-blue-100 cursor-pointer">
                                    <span x-text="result.text"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <input type="number" x-model="bahan.jumlah" @input="hitungTotalBahan(rIndex, bIndex)"
                        placeholder="Jumlah" class="input input-bordered w-20"/>

                    <input type="text" x-model="bahan.satuan" readonly class="input input-bordered w-20 bg-base-200"/>

                    <input type="text" x-model="bahan.harga_satuan_display" readonly
                        class="input input-bordered w-28 bg-base-200 text-right"/>

                    <input type="text" x-model="bahan.total_display" readonly
                        class="input input-bordered w-28 bg-base-200 text-right"/>

                    <button type="button" @click="removeBahan(rIndex, bIndex)" class="btn btn-error btn-sm">✕</button>
                </div>
            </template>

            <button type="button" @click="addBahan(rIndex)" class="btn btn-primary btn-sm mt-1">+ Tambah Bahan</button>
        </div>
    </template>

    <button type="button" @click="addRacikan()" class="btn btn-primary btn-sm mb-4">+ Tambah Racikan Baru</button>
</div>

                                    </div>

                                </div>

                                <div class="mt-6 p-6 bg-base-200 rounded-lg flex items-center gap-6" 
                                    x-data="biayaTambahan()" x-init="$watch('tuslah', value => {
                                            $refs.tuslahHidden.value = value;
                                            $refs.tuslahHidden.dispatchEvent(new Event('input'));
                                        });
                                        $watch('embalase', value => {
                                            $refs.embalaseHidden.value = value;
                                            $refs.embalaseHidden.dispatchEvent(new Event('input'));
                                        });">

                                    <!-- Tuslah -->
                                    <div class="flex items-center gap-2 text-sm font-semibold">
                                        <label class="w-20">Tuslah :</label>
                                        <input type="text" x-model="tuslahDisplay" @input="updateFromDisplay('tuslah')"
                                            class="input input-sm w-32 text-right" placeholder="Rp 0" />
                                        <input type="hidden" x-ref="tuslahHidden" wire:model="tuslah">
                                    </div>

                                    <!-- Embalase -->
                                    <div class="flex items-center gap-2 text-sm font-semibold">
                                        <label class="w-20">Embalase :</label>
                                        <input type="text" x-model="embalaseDisplay" @input="updateFromDisplay('embalase')"
                                            class="input input-sm w-32 text-right" placeholder="Rp 0" />
                                        <input type="hidden" x-ref="embalaseHidden" wire:model="embalase">
                                    </div>
                                </div>

                                <!-- TOTAL KESELURUHAN (di luar tab) -->
                                <div class="mt-6 p-6 bg-base-200 rounded-lg space-y-2 text-left"
                                    x-data="totalKeseluruhan()" x-init="init()">
                                    <div class="text-sm font-semibold">
                                        Total Obat Non Racik :
                                        <span class="font-bold" x-text="formatCurrency(totalNonracik)"></span>
                                    </div>
                                    <div class="text-sm font-semibold">
                                        Total Obat Racikan :
                                        <span class="font-bold" x-text="formatCurrency(totalRacikan)"></span>
                                    </div>
                                    <hr class="my-2 border-gray-300">
                                    <div class="text-sm font-bold">
                                        Total Keseluruhan :
                                        <span x-text="formatCurrency(totalKeseluruhan())"></span>
                                    </div>
                                </div>

                                <button wire:click.prevent="create" class="btn btn-primary w-full mb-1" wire:loading.attr="disabled">
                                    <span wire:loading.remove>Update</span>
                                    <span wire:loading.inline>Loading...</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
        const currency = new Intl.NumberFormat('id-ID', { 
            style: 'currency', 
            currency: 'IDR', 
            minimumFractionDigits: 0 
        });
        const formatCurrency = val => (!val || isNaN(val)) ? 'Rp 0' : currency.format(val);

        // Komponen input dinamis (nonracik / racikan)
        function obatManager(type = 'nonracik') {
            return {
                inputs: [],
                addInput() {
                    this.inputs.push({
                        uid: Date.now() + Math.random(),
                        id:'', nama:'', jumlah:'', satuan:'',
                        harga_satuan:0, harga_satuan_display:'',
                        total:0, total_display:''
                    });
                    // kosong pun harus memicu rekap supaya 0 tetap konsisten
                    this.updateGrandTotal();
                },
                init() {
                    // Hitung total per item saat jumlah berubah
                    this.$el.addEventListener('hitung-total', e => {
                        const i = e.detail.index;
                        const item = this.inputs[i] || {};
                        const jumlah = parseFloat(item.jumlah) || 0;
                        const harga  = parseFloat(item.harga_satuan) || 0;
                        const total  = jumlah * harga;
                        item.total = total;
                        item.harga_satuan_display = formatCurrency(harga);
                        item.total_display = formatCurrency(total);
                        this.updateGrandTotal();
                    });

                    // Inisialisasi awal
                    this.updateGrandTotal();
                },
                updateGrandTotal() {
                    const total = this.inputs.reduce((sum, it) => sum + (parseFloat(it.total) || 0), 0);
                    // broadcast ke window supaya komponen total di bawah bisa menangkap
                    this.$dispatch('update-total', { type, total });
                },
                formatCurrency
            }
        }

        function racikanManager() {
            return {
                racikanList: [],
                init() {
                    // preload dari Livewire jika perlu
                },
                addRacikan() {
                    this.racikanList.push({
                        uid: Date.now() + Math.random(),
                        nama_racikan: '',
                        bahan: []
                    });
                },
                removeRacikan(rIndex) {
                    this.racikanList.splice(rIndex, 1);
                    this.updateGrandTotal();
                },
                addBahan(rIndex) {
                    this.racikanList[rIndex].bahan.push({
                        uid: Date.now() + Math.random(),
                        id: '', nama: '', jumlah: '', satuan: '',
                        harga_satuan: 0, harga_satuan_display: '',
                        total: 0, total_display: ''
                    });
                },
                removeBahan(rIndex, bIndex) {
                    this.racikanList[rIndex].bahan.splice(bIndex, 1);
                    this.updateGrandTotal();
                },
                // 👇 method baru — dipanggil oleh searchObat()
                updateBahanFromSearch(rIndex, bIndex, result) {
                    const bahan = this.racikanList[rIndex].bahan[bIndex];
                    bahan.id = result.id;
                    bahan.nama = result.text; // atau result.nama_obat_aktual
                    bahan.satuan = result.satuan || '';
                    bahan.harga_satuan = result.harga || 0;
                    bahan.harga_satuan_display = formatCurrency(result.harga || 0);
                    bahan.total = (parseFloat(bahan.jumlah) || 0) * (parseFloat(result.harga) || 0);
                    bahan.total_display = formatCurrency(bahan.total);

                    this.updateGrandTotal();
                },
                hitungTotalBahan(rIndex, bIndex) {
                    const bahan = this.racikanList[rIndex].bahan[bIndex];
                    const jumlah = parseFloat(bahan.jumlah) || 0;
                    const harga = parseFloat(bahan.harga_satuan) || 0;
                    bahan.total = jumlah * harga;
                    bahan.total_display = formatCurrency(bahan.total);
                    bahan.harga_satuan_display = formatCurrency(harga);
                    this.updateGrandTotal();
                },
                updateGrandTotal() {
                    let total = 0;
                    this.racikanList.forEach(r => {
                        r.bahan.forEach(b => total += parseFloat(b.total) || 0);
                    });
                    this.$dispatch('update-total', { type: 'racikan', total });
                }
            };
        }

        // Komponen pencarian obat (untuk dropdown search ajax)
        function searchObat(...args) {
            return {
                query: '',
                results: [],
                open: false,
                async search() {
                    if (this.query.length < 2) {
                        this.results = [];
                        return;
                    }
                    const res = await fetch(`{{ route('search.ProdukObat') }}?q=${encodeURIComponent(this.query)}`);
                    const data = await res.json();
                    this.results = data;
                },
                select(result) {
                    this.query = result.text;
                    this.open = false;

                    // --- CASE 1: DIPANGGIL DARI NON RACIK ---
                    if (args.length === 2) {
                        const [i, inputs] = args;
                        const item = inputs[i];
                        item.id = result.id;
                        item.nama = result.text;
                        item.satuan = result.satuan;
                        item.harga_satuan = result.harga;
                        item.harga_satuan_display = formatCurrency(result.harga);

                        const jumlah = parseFloat(item.jumlah) || 0;
                        const total = jumlah * (parseFloat(result.harga) || 0);
                        item.total = total;
                        item.total_display = formatCurrency(total);

                        this.$dispatch('hitung-total', { index: i });
                        return;
                    }

                    // --- CASE 2: DIPANGGIL DARI RACIKAN ---
                    if (args.length >= 3) {
                        const [rIndex, bIndex, racikanList] = args;
                        const bahan = racikanList[rIndex].bahan[bIndex];
                        bahan.id = result.id;
                        bahan.nama = result.text;
                        bahan.satuan = result.satuan;
                        bahan.harga_satuan = result.harga;
                        bahan.harga_satuan_display = formatCurrency(result.harga);

                        const jumlah = parseFloat(bahan.jumlah) || 0;
                        const total = jumlah * (parseFloat(result.harga) || 0);
                        bahan.total = total;
                        bahan.total_display = formatCurrency(total);

                        this.$dispatch('hitung-total', { rIndex, bIndex });
                    }
                },
            };
        }

        function biayaTambahan() {
            return {
                tuslah: 0,
                embalase: 0,
                tuslahDisplay: 'Rp 0',
                embalaseDisplay: 'Rp 0',

                init() {
                    this.tuslahDisplay   = formatCurrency(this.tuslah);
                    this.embalaseDisplay = formatCurrency(this.embalase);

                    // kirim initial value juga
                    this.$dispatch('update-biaya', { tuslah: this.tuslah, embalase: this.embalase });
                },

                updateFromDisplay(field) {
                    let raw = this[`${field}Display`].replace(/[^\d]/g, ''); // ambil angka saja
                    this[field] = parseInt(raw) || 0;
                    this[`${field}Display`] = formatCurrency(this[field]);
                    this.$dispatch('update-biaya', { tuslah: this.tuslah, embalase: this.embalase });
                }
            }
        }

        // Komponen ringkasan total (mendengar event dari window)
        function totalKeseluruhan() {
            return {
                totalNonracik: 0,
                totalRacikan:  0,
                tuslah: 0,
                embalase: 0,
                totalKeseluruhan() {
                    return (parseFloat(this.totalNonracik) || 0) +
                        (parseFloat(this.totalRacikan) || 0) +
                        (parseFloat(this.tuslah) || 0) +
                        (parseFloat(this.embalase) || 0);
                },
                formatCurrency,
                init() {
                    window.addEventListener('update-total', (e) => {
                        if (!e.detail) return;
                        if (e.detail.type === 'nonracik') this.totalNonracik = e.detail.total || 0;
                        if (e.detail.type === 'racikan')  this.totalRacikan  = e.detail.total || 0;
                    }, false);

                    window.addEventListener('update-biaya', (e) => {
                        if (!e.detail) return;
                        this.tuslah   = e.detail.tuslah   ?? this.tuslah;
                        this.embalase = e.detail.embalase ?? this.embalase;
                    }, false);
                }
            }
        }
</script>