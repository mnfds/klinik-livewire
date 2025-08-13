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
                            Riwayat Kunjungan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Rekam Medis
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Isi Rekam Medis
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-lg font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Rekam Medis Pasien
            </h1>
        </div>

        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                <!-- Kolom Kiri -->
                <div class="lg:col-span-3 space-y-6">
                    <div class="bg-base-100 text-base-content shadow rounded-box p-6 space-y-4">
                        <div class="tabs tabs-lift">
                            <!-- A: Biodata Pasien -->
                            <label class="tab gap-2 cursor-pointer">
                                <input type="radio" name="my_tabs_3" class="hidden" checked />
                                <i class="fa-solid fa-id-card"></i>
                                Biodata
                            </label>
                            <div class="tab-content bg-base-100 border-base-300 p-6 text-base-content">
                                <h2 class="text-lg font-semibold border-b pb-2">Biodata Pasien</h2>
                                <div class="space-y-2 text-sm">

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
                                            <div>: {{ \Carbon\Carbon::parse($pasienTerdaftar->pasien->tanggal_lahir)->translatedFormat('d F Y') }}</div>
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

                                </div>
                            </div>
                            <!-- A: Hasil Kajian Awal -->
                            <label class="tab gap-2 cursor-pointer">
                                <input type="radio" name="my_tabs_3" class="hidden" />
                                <i class="fa-solid fa-clipboard-list"></i>
                                Hasil Kajian
                            </label>
                            <div class="tab-content bg-base-100 border-base-300 p-6 text-base-content">
                                <h2 class="text-lg font-semibold border-b pb-2">Hasil Kajian Awal (Anamnesa)</h2>
                                <div class="space-y-2 text-sm">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">

                                        {{-- Pemeriksaan Fisik --}}
                                        @if ($kajian->pemeriksaanFisik)
                                            <div>
                                                <div class="font-semibold mb-1">Pemeriksaan Fisik</div>
                                                <div class="grid grid-cols-[auto_1fr] gap-x-2 gap-y-1">
                                                    <div>Tinggi Badan</div><div>: {{ $kajian->pemeriksaanFisik->tinggi_badan }} cm</div>
                                                    <div>Berat Badan</div><div>: {{ $kajian->pemeriksaanFisik->berat_badan }} kg</div>
                                                    <div>IMT</div><div>: {{ $kajian->pemeriksaanFisik->imt }}</div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Tanda Vital --}}
                                        @if ($kajian->tandaVital)
                                            <div>
                                                <div class="font-semibold mb-1">Tanda Vital</div>
                                                <div class="grid grid-cols-[auto_1fr] gap-x-2 gap-y-1">
                                                    <div>Suhu Tubuh</div><div>: {{ $kajian->tandaVital->suhu_tubuh }} °C</div>
                                                    <div>Nadi</div><div>: {{ $kajian->tandaVital->nadi }} bpm</div>
                                                    <div>Tekanan Darah</div><div>: {{ $kajian->tandaVital->sistole }}/{{ $kajian->tandaVital->diastole }} mmHg</div>
                                                    <div>Frekuensi Napas</div><div>: {{ $kajian->tandaVital->frekuensi_pernapasan }} /menit</div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Data Kesehatan --}}
                                        @if ($kajian->dataKesehatan)
                                            @php
                                                $riwayat_penyakit = json_decode($kajian->dataKesehatan->riwayat_penyakit ?? '[]', true);
                                                $riwayat_alergi_obat = json_decode($kajian->dataKesehatan->riwayat_alergi_obat ?? '[]', true);
                                                $obat = json_decode($kajian->dataKesehatan->obat_sedang_dikonsumsi ?? '[]', true);
                                                $alergi_lain = json_decode($kajian->dataKesehatan->riwayat_alergi_lainnya ?? '[]', true);
                                            @endphp

                                            <div class="md:col-span-2">
                                                <div class="font-semibold mb-1">Data Kesehatan</div>
                                                <div class="grid grid-cols-[auto_1fr] gap-x-2 gap-y-1">
                                                    <div>Keluhan Utama</div><div>: {{ $kajian->dataKesehatan->keluhan_utama ?? '-' }}</div>
                                                    <div>Status Perokok</div><div>: {{ $kajian->dataKesehatan->status_perokok ?? '-' }}</div>

                                                    <div>Riwayat Penyakit</div>
                                                    <div>:
                                                        @if(empty($riwayat_penyakit))
                                                            -
                                                        @else
                                                            <ul class="ml-4 list-disc">
                                                                @foreach ($riwayat_penyakit as $r)
                                                                    <li>{{ $r }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>

                                                    <div>Riwayat Alergi Obat</div>
                                                    <div>:
                                                        @if(empty($riwayat_alergi_obat))
                                                            -
                                                        @else
                                                            <ul class="ml-4 list-disc">
                                                                @foreach ($riwayat_alergi_obat as $r)
                                                                    <li>{{ $r }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>

                                                    <div>Obat Dikonsumsi</div>
                                                    <div>:
                                                        @if(empty($obat))
                                                            -
                                                        @else
                                                            <ul class="ml-4 list-disc">
                                                                @foreach ($obat as $r)
                                                                    <li>{{ $r }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>

                                                    <div>Alergi Lainnya</div>
                                                    <div>:
                                                        @if(empty($alergi_lain))
                                                            -
                                                        @else
                                                            <ul class="ml-4 list-disc">
                                                                @foreach ($alergi_lain as $r)
                                                                    <li>{{ $r }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Data Estetika --}}
                                        @if ($kajian->dataEstetika)
                                            @php
                                                $decode = fn($v) => $v ? (is_array(json_decode($v, true)) ? json_decode($v, true) : [$v]) : [];
                                                $problem = $decode($kajian->dataEstetika->problem_dihadapi);
                                                $tindakan = $decode($kajian->dataEstetika->tindakan_sebelumnya);
                                                $metode_kb = $decode($kajian->dataEstetika->metode_kb);
                                            @endphp

                                            <div class="md:col-span-2">
                                                <div class="font-semibold mb-1">Data Estetika</div>
                                                <div class="grid grid-cols-[auto_1fr] gap-x-2 gap-y-1">
                                                    <div>Problem Dihadapi</div>
                                                    <div>:
                                                        @if(empty($problem))
                                                            -
                                                        @else
                                                            <ul class="ml-4 list-disc">
                                                                @foreach ($problem as $r)
                                                                    <li>{{ $r }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>

                                                    <div>Lama Problem</div><div>: {{ $kajian->dataEstetika->lama_problem ?? '-' }}</div>

                                                    <div>Tindakan Sebelumnya</div>
                                                    <div>:
                                                        @if(empty($tindakan))
                                                            -
                                                        @else
                                                            <ul class="ml-4 list-disc">
                                                                @foreach ($tindakan as $r)
                                                                    <li>{{ $r }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>

                                                    <div>Penyakit Dialami</div><div>: {{ $kajian->dataEstetika->penyakit_dialami ?? '-' }}</div>
                                                    <div>Alergi Kosmetik</div><div>: {{ $kajian->dataEstetika->alergi_kosmetik ?? '-' }}</div>
                                                    <div>Sedang Hamil</div><div>: {{ ucfirst($kajian->dataEstetika->sedang_hamil) ?? '-' }}</div>
                                                    <div>Usia Kehamilan</div><div>: {{ $kajian->dataEstetika->usia_kehamilan ? $kajian->dataEstetika->usia_kehamilan . ' bln' : '-' }}</div>

                                                    <div>Metode KB</div>
                                                    <div>:
                                                        @if(empty($metode_kb))
                                                            -
                                                        @else
                                                            <ul class="ml-4 list-disc">
                                                                @foreach ($metode_kb as $r)
                                                                    <li>{{ $r }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>

                                                    <div>Pengobatan Saat Ini</div><div>: {{ $kajian->dataEstetika->pengobatan_saat_ini ?? '-' }}</div>
                                                    <div>Produk Kosmetik</div><div>: {{ $kajian->dataEstetika->produk_kosmetik ?? '-' }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- A: Layanan Tersisa -->
                            <label class="tab bg-transparent text-base-content gap-2 cursor-pointer">
                                <input type="radio" name="my_tabs_3" class="hidden" />
                                <span class="flex items-center gap-2">
                                    <i class="fa-solid fa-hand-holding-heart"></i>
                                    Layanan Tersisa
                                    <div class="badge badge-sm badge-primary text-base-primary">99</div>
                                </span>
                            </label>

                            <div class="tab-content bg-base-100 border-base-300 p-6 text-base-content">
                                Layanan/Tindakan Tersisa
                            </div>
                        </div>
                    </div>

                    <!-- C: Form -->
                    <div class="bg-base-100 shadow rounded-box p-6 space-y-6">
                        <h2 class="text-lg font-semibold border-b pb-2">Form Rekam Medis</h2>
                        <form wire:submit.prevent="create" class="space-y-6">
                            <input type="hidden" wire:model.defer='id_pasien_terdaftar' value="{{  $pasienTerdaftar->id }}" name="id_pasien_terdaftar">

                            {{-- NAVIGATION --}}
                            <div class="bg-base-100 text-base-content shadow rounded-box py-4 px-8 flex gap-6 justify-between sticky top-0 z-50">
                                <a href="#subjective" class="font-semibold hover:text-primary">SUBJECTIVE</a>
                                <a href="#objective" class="font-semibold hover:text-primary">OBJECTIVE</a>
                                <a href="#assessment" class="font-semibold hover:text-primary">ASSESSMENT</a>
                                <a href="#plan" class="font-semibold hover:text-primary">PLAN</a>
                            </div>

                            {{-- SUBJECTIVE --}}
                            <div id="subjective" class="bg-base-200 shadow rounded-lg py-6 px-3 scroll-mt-16"
                                x-data="formChoicesSubjective()" 
                                x-init="initChoicesSubjective()" 
                                x-effect="$wire.selected_forms_subjective = selectedFormsSubjective">

                                <h2 class="text-lg font-semibold mb-4 border-b pb-2">SUBJECTIVE</h2>
                                <!-- Select Multiple with Choices.js -->
                                <div wire:ignore>
                                    <label class="label font-semibold">Pilih Form yang Ingin Ditampilkan:</label>
                                    <select id="formSelect" multiple class="w-full hidden select" x-ref="formSelectSubjective">
                                        <option value="data-kesehatan">Data Kesehatan</option>
                                        <option value="data-estetika">Data Estetika</option>
                                    </select>
                                </div>
                                <!-- DATA KESEHATAN -->
                                <div x-show="selectedFormsSubjective.includes('data-kesehatan')" style="display: none">
                                    <x-rekammedis.datakesehatan :dataKesehatan="$data_kesehatan" wire:model="data_kesehatan" />
                                </div>
                                <!-- DATA ESTETIKA -->
                                <div x-show="selectedFormsSubjective.includes('data-estetika')" style="display: none">
                                    <x-rekammedis.dataestetika :dataEstetika="$data_estetika" wire:model="data_estetika" />
                                </div>
                            </div>
                            {{-- OBJECTIVE --}}
                            <div id="objective" class="bg-base-200 shadow rounded-lg py-6 px-3 scroll-mt-16"
                                x-data="formChoicesObjective()"
                                x-init="initChoicesObjective()"
                                x-effect="$wire.selected_forms_objective = selectedFormsObjective">
                                    
                                <h2 class="text-lg font-semibold mb-4 border-b pb-2">OBJECTIVE</h2>
                                <!-- Select Multiple with Choices.js -->
                                <div wire:ignore>
                                    <label class="label font-semibold">Pilih Form yang Ingin Ditampilkan:</label>
                                    <select id="formSelect" multiple class="w-full hidden select" x-ref="formSelectObjective">
                                        <option value="tanda-vital">Tanda Vital Pasien</option>
                                        <option value="pemeriksaan-fisik">Pemeriksaan Fisik</option>
                                        <option value="pemeriksaan-estetika">Pemeriksaan Kulit & Estetika</option>
                                    </select>
                                </div>
                                <!-- Tingkat Kesadaran -->
                                <div class="form-control">
                                    <label class="label">Tingkat Kesadaran</label>
                                    <select wire:model="tingkat_kesadaran" class="select select-bordered w-full">
                                        <option value="">Pilih Tingkatan</option>
                                        <option value="Sadar Baik/Alert">Sadar Baik/Alert</option>
                                        <option value="Berespon dengan kata-kata">Berespon dengan kata-kata</option>
                                        <option value="Hanya berespons jika dirangsang nyeri"> Hanya berespons jika dirangsang nyeri</option>
                                        <option value="Pasien tidak sadar">Pasien tidak sadar</option>
                                        <option value="Gelisah atau bingung">Gelisah atau bingung</option>
                                        <option value="Acute Confusional States ">Acute Confusional States </option>
                                    </select>
                                </div>
                                <!-- Tanda Vital -->
                                <div x-show="selectedFormsObjective.includes('tanda-vital')" style="display: none">
                                    <x-rekammedis.tandavital :tandaVital="$tanda_vital" wire:model="tanda_vital" />
                                </div>
                                <!-- Data Fisik -->
                                <div x-show="selectedFormsObjective.includes('pemeriksaan-fisik')" style="display: none">
                                    <x-rekammedis.pemeriksaanfisik :pemeriksaanFisik="$pemeriksaan_fisik" wire:model="pemeriksaan_fisik" />
                                </div>
                                <!-- Data Kulit Dan Estetika -->
                                <div x-show="selectedFormsObjective.includes('pemeriksaan-estetika')" style="display: none">
                                    <x-rekammedis.pemeriksaanestetika :pemeriksaanEstetika="$pemeriksaan_estetika" wire:model="pemeriksaan_estetika" />
                                </div>
                            </div>
                            {{-- ASSESSMENT --}}
                            <div id="assessment" class="bg-base-200 shadow rounded-lg py-6 px-3 scroll-mt-16"
                                x-data="formChoicesAssessment()"
                                x-init="initChoicesAssessment()"
                                x-effect="$wire.selected_forms_assessment = selectedFormsAssessment">
                                    
                                <h2 class="text-lg font-semibold mb-4 border-b pb-2">ASSESSMENT</h2>
                                <!-- Select Multiple with Choices.js -->
                                <div wire:ignore>
                                    <label class="label font-semibold">Pilih Form yang Ingin Ditampilkan:</label>
                                    <select id="formSelect" multiple class="w-full hidden select" x-ref="formSelectAssessment">
                                        <option value="diagnosa">Diagnosa</option>
                                        <option value="icd_10">ICD 10</option>
                                    </select>
                                </div>
                                    
                                <!-- ICD 10 -->
                                <div x-show="selectedFormsAssessment.includes('icd_10')" style="display: none">
                                    <div class="form-control" x-data="multiSelectIcd10()" x-init="init()">
                                        <label class="label">ICD 10</label>

                                        <!-- Input Area -->
                                        <div class="relative" @click="setTimeout(() => open = true, 10)">
                                            <div class="w-full border border-gray-300 bg-base-100 rounded-2xl p-1 flex flex-wrap items-center gap-2 min-h-[2.5rem] focus-within:ring-2 focus-within:ring-black transition" :class="{ 'ring-2 ring-black': open }">

                                                <!-- Selected tags -->
                                                <template x-for="(tag, index) in selected" :key="tag.code">
                                                    <span class="bg-primary text-sm rounded-full px-3 py-1 flex items-center gap-1">
                                                        <span x-text="tag.label"></span>
                                                        <button type="button" @click.stop="remove(tag.code)">×</button>
                                                    </span>
                                                </template>

                                                <!-- Input search -->
                                                <input type="text" class="flex-grow min-w-[8ch] text-sm border-none rounded-xl bg-base-100"
                                                    placeholder="Ketik disini untuk cari Diagnosa ICD 10..."
                                                    x-model="search"
                                                    @focus="open = true"
                                                    @input.debounce.300ms="fetchOptions(); open = true" />
                                            </div>

                                            <!-- Dropdown Menu -->
                                            <div x-show="open" @click.outside="open = false"
                                                class="absolute z-10 mt-1 w-full bg-base-200 border border-gray-500 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                                                <template x-if="filteredOptions.length > 0">
                                                    <template x-for="item in filteredOptions" :key="item.code">
                                                        <div @click="toggle(item)"
                                                            class="px-3 py-2 hover:bg-primary/50 rounded-2xl cursor-pointer text-sm m-1"
                                                            :class="selected.some(s => s.code === item.code) ? 'bg-primary rounded-2xl font-semibold' : ''">
                                                            <span x-text="item.label"></span>
                                                        </div>
                                                    </template>
                                                </template>

                                                <div x-show="filteredOptions.length === 0"
                                                    class="px-3 py-2 text-sm text-base-content">
                                                    Tidak ada hasil.
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Binding ke Livewire: kirim array code saja -->
                                        <input type="hidden" wire:model="icd10" :value="JSON.stringify(selected.map(s => s.code))">

                                        <span class="text-xs text-gray-400 mt-1">* Klik untuk pilih, klik ulang untuk hapus</span>
                                    </div>
                                </div>

                                <!-- Diagnosa -->
                                <div x-show="selectedFormsAssessment.includes('diagnosa')" style="display: none">
                                    <div class="form-control">
                                        <label class="label block mb-1">
                                            <span class="label-text">Diagnosa</span>
                                        </label>
                                        <textarea wire:model="diagnosa" class="textarea textarea-bordered w-full"
                                            placeholder="Tuliskan diagnosis utama pasien, misal: Demam tifoid, hipertensi tahap 2">
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                            {{-- PLAN --}}
                            <div id="plan" class="bg-base-200 shadow rounded-lg py-6 px-3 scroll-mt-16"
                                x-data="formChoicesPlan()"
                                x-init="initChoicesPlan()"
                                x-effect="$wire.selected_forms_plan = selectedFormsPlan">

                                <h2 class="text-lg font-semibold mb-4 border-b pb-2">PLAN</h2>
                                <!-- Select Multiple dengan Choices.js -->
                                <div wire:ignore>
                                    <label class="label font-semibold">Pilih Form yang Ingin Ditampilkan:</label>
                                    <select id="formSelect" multiple class="w-full hidden select" x-ref="formSelect">
                                        <option value="rencana-layanan">Rencana Layanan Atau Tindakan</option>
                                        <option value="rencana-pengobatan">Rencana Pengobatan</option>
                                    </select>
                                </div>

                                <!-- Rencana Layanan/Tindakan -->
                                <div 
                                    x-show="selectedFormsPlan.includes('rencana-layanan')" 
                                    style="display: none"
                                    wire:ignore
                                >
                                    <x-rekammedis.rencanalayanan 
                                        :layanandanbundling="$layanandanbundling" 
                                        :rencanaLayanan="$rencana_layanan"
                                    />
                                </div>

                                <!-- Rencana Pengobatan -->
                                <div 
                                    x-show="selectedFormsPlan.includes('rencana-pengobatan')" 
                                    style="display: none"
                                    wire:ignore
                                >
                                    <x-rekammedis.rencanapengobatan 
                                        :rencanaPengobatan="$rencana_pengobatan"
                                    />
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="lg:col-span-1">
                    <div class="sticky top-10 space-y-6">
                        <!-- B: Button -->
                        <div class="bg-base-100 shadow rounded-box p-4 pb-7">
                            <h3 class="font-semibold mb-4">Aksi</h3>
                            <button wire:click.prevent="create" class="btn btn-success w-full mb-1" wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="fa-solid fa-plus"></i> Simpan</span>
                                <span wire:loading.inline>Loading...</span>
                            </button>
                            <a wire:navigate href="{{ route('rekam-medis-pasien.data', ['pasien_id' => $pasienTerdaftar->pasien->id]) }}"
                                class="btn btn-info mb-1 w-full" >
                                <i class="fa-solid fa-book-medical"></i> Riwayat Rekam Medis
                            </a>
                        </div>

                    </div>
                </div>
                
            </div>
        </div>

    </div>
</div>
<script>
    function formChoicesSubjective() {
        return {
            selectedFormsSubjective: [],
            choicesSubjective: null,

            initChoicesSubjective() {
                this.choicesSubjective = new Choices(this.$refs.formSelectSubjective, {
                    removeItemButton: true,
                    placeholderValue: 'Pilih form...',
                    shouldSort: false,
                });

                this.selectedFormsSubjective = Array.from(this.$refs.formSelectSubjective.selectedOptions).map(opt => opt.value);

                this.$refs.formSelectSubjective.addEventListener('change', (event) => {
                    this.selectedFormsSubjective = Array.from(event.target.selectedOptions).map(opt => opt.value);
                });
            }
        }
    }

    function formChoicesObjective() {
        return {
            selectedFormsObjective: [],
            choicesObjective: null,

            initChoicesObjective() {
                this.choicesObjective = new Choices(this.$refs.formSelectObjective, {
                    removeItemButton: true,
                    placeholderValue: 'Pilih form...',
                    shouldSort: false,
                });

                this.selectedFormsObjective = Array.from(this.$refs.formSelectObjective.selectedOptions).map(opt => opt.value);

                this.$refs.formSelectObjective.addEventListener('change', (event) => {
                    this.selectedFormsObjective = Array.from(event.target.selectedOptions).map(opt => opt.value);
                });
            }
        }
    }

    function formChoicesAssessment() {
        return {
            selectedFormsAssessment: [],
            choicesAssessment: null,

            initChoicesAssessment() {
                this.choicesAssessment = new Choices(this.$refs.formSelectAssessment, {
                    removeItemButton: true,
                    placeholderValue: 'Pilih form...',
                    shouldSort: false,
                });

                this.selectedFormsAssessment = Array.from(this.$refs.formSelectAssessment.selectedOptions).map(opt => opt.value);

                this.$refs.formSelectAssessment.addEventListener('change', (event) => {
                    this.selectedFormsAssessment = Array.from(event.target.selectedOptions).map(opt => opt.value);
                });
            }
        }
    }

    function formChoicesPlan() {
        return {
            selectedFormsPlan: [],
            choicesPlan: null,

            initChoicesPlan() {
                // Init Choices.js hanya sekali
                this.choicesPlan = new Choices(this.$refs.formSelect, {
                    removeItemButton: true,
                    placeholderValue: 'Pilih form...',
                    shouldSort: false,
                });

                // Set value awal
                this.selectedFormsPlan = Array.from(this.$refs.formSelect.selectedOptions).map(opt => opt.value);

                // Update Alpine state jika pilihan berubah
                this.$refs.formSelect.addEventListener('change', (event) => {
                    this.selectedFormsPlan = Array.from(event.target.selectedOptions).map(opt => opt.value);
                });
            }
        }
    }
</script>

{{-- GET ICD --}}
<script>
    function multiSelectIcd10() {
        return {
            open: false,
            selected: @entangle('icd10'), // pastikan property Livewire sesuai
            search: '',
            filteredOptions: [],

            init() {
                if (!Array.isArray(this.selected)) {
                    this.selected = [];
                }
            },

            fetchOptions() {
                if (this.search.trim() === '') {
                    this.filteredOptions = [];
                    return;
                }

                fetch(`/ajax/icd_10?q=${encodeURIComponent(this.search)}`)
                    .then(res => res.json())
                    .then(data => {
                        this.filteredOptions = data; // sudah {code, label}
                    });
            },

            toggle(item) {
                const exists = this.selected.some(s => s.code === item.code);
                if (!exists) {
                    this.selected.push(item);
                } else {
                    this.selected = this.selected.filter(s => s.code !== item.code);
                }
                this.search = '';
                this.filteredOptions = [];
            },

            remove(code) {
                this.selected = this.selected.filter(s => s.code !== code);
            }
        }
    }
</script>
