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
                                aria-label="Informasi Pasien" />
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
                                            <div class="w-32 font-bold">No. Register</div>
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
                                            <div>: {{ $pasienTerdaftar->rekamMedis->dataKesehatanRM->status_perokok }}
                                            </div>
                                        </div>
                                        <div class="flex">
                                            <div class="w-32 font-bold">Status Kehamilan</div>
                                            <div>: {{
                                                $pasienTerdaftar->rekamMedis->dataEstetikaRM->usia_kehamilan
                                                ? $pasienTerdaftar->rekamMedis->dataEstetikaRM->usia_kehamilan . '
                                                bulan'
                                                : '-'
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Informasi Medis -->
                            <input type="radio" name="info" class="tab bg-transparent text-base-content"
                                aria-label="Informasi Medis" checked="checked" />
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
                                aria-label="Informasi Tanda Vital" />
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
                                aria-label="Informasi Pemeriksaan Fisik" />
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

                            {{-- Data Resep --}}
                            <div class="tabs tabs-lift">

                                <!-- Tab Non Racikan -->
                                <input type="radio" name="tab_resep" class="tab bg-transparent text-base-content"
                                    aria-label="Obat Non Racik" checked="checked" />
                                <div class="tab-content bg-base-100 border-base-300 p-6">
                                    <h3 class="font-semibold mb-4">Obat Non Racikan</h3>

                                    <!-- Resep Dokter -->
                                    <details class="collapse collapse-arrow border rounded-lg mb-4">
                                        <summary class="collapse-title font-semibold">Resep Dokter</summary>
                                        <div class="collapse-content text-sm space-y-3">
                                            @forelse($obatNonRacikanItems as $obat)
                                                <div class="p-3 border rounded-lg bg-base-200">
                                                    <strong>{{ $obat['nama_obat_non_racikan'] }}</strong><br>
                                                    Jumlah: {{ $obat['jumlah_obat_non_racikan'] }} {{ $obat['satuan_obat_non_racikan'] }} <br>
                                                    Dosis: {{ $obat['dosis_obat_non_racikan'] }} × {{ $obat['hari_obat_non_racikan'] }} hari <br>
                                                    Aturan Pakai: {{ $obat['aturan_pakai_obat_non_racikan'] }}
                                                </div>
                                            @empty
                                                <p class="text-sm text-gray-500">Tidak ada obat non racikan.</p>
                                            @endforelse
                                        </div>
                                    </details>

                                    <!-- Input Apoteker (dinamis) -->
                                    <div x-data="{
                                            inputs: [{ inventory: '', jumlah: '', satuan: '', harga_satuan: '', total: '' }],
                                            addInput() { 
                                                this.inputs.push({ inventory: '', jumlah: '', satuan: '', harga_satuan: '', total: '' }) 
                                            },
                                            removeInput(i) { 
                                                this.inputs.splice(i, 1) 
                                            },
                                            hitungTotal(i) {
                                                let item = this.inputs[i];
                                                let jumlah = parseFloat(item.jumlah) || 0;
                                                let harga = parseFloat(item.harga_satuan) || 0;
                                                this.inputs[i].total = jumlah * harga;
                                            }
                                        }" class="space-y-4">

                                        <template x-for="(item, i) in inputs" :key="i">
                                            <div class="p-2 border rounded-lg bg-base-100 space-y-2">
                                                <div class="flex gap-1">
                                                    <!-- Inventory Apotek -->
                                                    <select x-model="item.inventory" class="select select-bordered flex-1">
                                                        <option value="">-- pilih obat --</option>
                                                        <!-- TODO: isi dari inventory via select2/ajax -->
                                                    </select>

                                                    <!-- Jumlah -->
                                                    <input type="number" x-model="item.jumlah" 
                                                        @input="hitungTotal(i)"
                                                        class="input input-bordered w-24" 
                                                        placeholder="Jumlah" />

                                                    <!-- Satuan -->
                                                    <input type="text" x-model="item.satuan" 
                                                        class="input input-bordered w-24" 
                                                        placeholder="Satuan" />

                                                    <!-- Harga Satuan -->
                                                    <input type="number" x-model="item.harga_satuan" 
                                                        @input="hitungTotal(i)"
                                                        class="input input-bordered w-28" 
                                                        placeholder="Harga/satuan" />

                                                    <!-- Total -->
                                                    <input type="number" x-model="item.total" readonly
                                                        class="input input-bordered w-28 bg-base-200" 
                                                        placeholder="Total" />

                                                    <!-- Hapus -->
                                                    <button type="button" class="btn btn-error btn-sm" @click="removeInput(i)">✕</button>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Tombol tambah input -->
                                        <button type="button" class="btn btn-primary btn-sm" @click="addInput">+ Tambah Obat</button>
                                    </div>
                                </div>
                                <!-- Tab Racikan -->
                                <input type="radio" name="tab_resep" class="tab bg-transparent text-base-content"
                                    aria-label="Obat Racikan" />
                                <div class="tab-content bg-base-100 border-base-300 p-6">
                                    <h3 class="font-semibold mb-4">Obat Racikan</h3>

                                    <!-- Resep Dokter -->
                                    <details class="collapse collapse-arrow border rounded-lg mb-4">
                                        <summary class="collapse-title font-semibold">Resep Dokter</summary>
                                        <div class="collapse-content text-sm space-y-4">
                                            @forelse($obatRacikanItems as $racik)
                                                <div class="p-3 border rounded-lg bg-base-200">
                                                    <strong>{{ $racik['nama_racikan'] }}</strong><br>
                                                    Jumlah: {{ $racik['jumlah_racikan'] }} {{ $racik['satuan_racikan'] }} <br>
                                                    Dosis: {{ $racik['dosis_obat_racikan'] }} × {{ $racik['hari_obat_racikan'] }} hari <br>
                                                    Aturan Pakai: {{ $racik['aturan_pakai_racikan'] }} <br>
                                                    Intruksi Dokter: {{ $racik['metode_racikan'] }}

                                                    <div class="mt-2 p-2 border rounded bg-base-100">
                                                        <h4 class="font-semibold mb-2">Bahan Racikan:</h4>
                                                        @forelse($racik['bahan'] as $bahan)
                                                            <div class="text-sm">
                                                                - {{ $bahan['nama_obat_racikan'] }} : {{ $bahan['jumlah_obat_racikan'] }} {{ $bahan['satuan_obat_racikan'] }}
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

                                    <!-- Input Apoteker (dinamis untuk bahan racikan) -->
                                    <div x-data="{
                                            inputs: [{ inventory: '', jumlah: '', satuan: '', harga_satuan: '', total: '' }],
                                            addInput() {
                                                this.inputs.push({ inventory: '', jumlah: '', satuan: '', harga_satuan: '', total: '' })
                                            },
                                            removeInput(i) {
                                                this.inputs.splice(i, 1)
                                            },
                                            hitungTotal(i) {
                                                let item = this.inputs[i];
                                                let jumlah = parseFloat(item.jumlah) || 0;
                                                let harga = parseFloat(item.harga_satuan) || 0;
                                                this.inputs[i].total = jumlah * harga;
                                            }
                                        }" class="space-y-4">

                                        <template x-for="(item, i) in inputs" :key="i">
                                            <div class="p-4 border rounded-lg bg-base-100 space-y-2">
                                                <div class="flex gap-2">
                                                    <!-- Inventory Apotek -->
                                                    <select x-model="item.inventory" class="select select-bordered flex-1">
                                                        <option value="">-- pilih bahan --</option>
                                                        <!-- TODO: isi dari inventory via select2/ajax -->
                                                    </select>

                                                    <!-- Jumlah -->
                                                    <input type="number" x-model="item.jumlah"
                                                        @input="hitungTotal(i)"
                                                        class="input input-bordered w-24"
                                                        placeholder="Jumlah" />

                                                    <!-- Satuan -->
                                                    <input type="text" x-model="item.satuan"
                                                        class="input input-bordered w-24"
                                                        placeholder="Satuan" />

                                                    <!-- Harga Satuan -->
                                                    <input type="number" x-model="item.harga_satuan"
                                                        @input="hitungTotal(i)"
                                                        class="input input-bordered w-28"
                                                        placeholder="Harga/satuan" />

                                                    <!-- Total -->
                                                    <input type="number" x-model="item.total" readonly
                                                        class="input input-bordered w-28 bg-base-200"
                                                        placeholder="Total" />

                                                    <!-- Hapus -->
                                                    <button type="button" class="btn btn-error btn-sm" @click="removeInput(i)">✕</button>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Tombol tambah input -->
                                        <button type="button" class="btn btn-primary btn-sm" @click="addInput">+ Tambah Bahan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>