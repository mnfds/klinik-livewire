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

                <!-- Kolom Kiri (A + C) -->
                <div class="lg:col-span-3 space-y-6">
                    <div class="bg-base-100 text-base-content shadow rounded-box p-6 space-y-4">
                        <div class="tabs tabs-lift">
                            <!-- A: Biodata Pasien -->
                            <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Biodata" style="background-image: none;" checked="checked" />
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

                            <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Hasil Kajian" style="background-image: none;" />
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

                            <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Layanan" style="background-image: none;" />
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

                            {{-- SUBJECTIVE --}}
                            <div class="bg-base-200 shadow rounded-lg py-6 px-3">
                                <h2 class="text-lg font-semibold mb-4 border-b pb-2">SUBJECTIVE</h2>
                                <div x-data="formChoicesSubjective()" x-init="initChoicesSubjective()" x-effect="$wire.selected_forms_subjective = selectedFormsSubjective" class="space-y-6">
                                    <!-- Select Multiple with Choices.js -->
                                    <div>
                                        <label class="label font-semibold">Pilih Form yang Ingin Ditampilkan:</label>
                                        <select id="formSelect" multiple class="w-full hidden select" x-ref="formSelect">
                                            <option value="data-kesehatan">Data Kesehatan</option>
                                            <option value="subjective-2">Sub2</option>
                                        </select>
                                    </div>
                                    <!-- SUB-1 -->
                                    <div x-show="selectedFormsSubjective.includes('data-kesehatan')" style="display: none">
                                        <x-rekammedis.datakesehatan :data-kesehatan="$data_kesehatan" wire:model="data_kesehatan" />
                                    </div>
                                    <!-- SUB-2 -->
                                    <div x-show="selectedFormsSubjective.includes('subjective-2')" style="display: none">
                                        subjective 2
                                    </div>
                                </div>
                            </div>
                            {{-- OBJECTIVE --}}
                            <div class="bg-base-200 shadow rounded-lg py-6 px-3">
                                <h2 class="text-lg font-semibold mb-4 border-b pb-2">OBJECTIVE</h2>
                                <div x-data="formChoicesObjective()" x-init="initChoicesObjective()" x-effect="$wire.selected_forms_objective = selectedFormsObjective" class="space-y-6">
                                    <!-- Select Multiple with Choices.js -->
                                    <div>
                                        <label class="label font-semibold">Pilih Form yang Ingin Ditampilkan:</label>
                                        <select id="formSelect" multiple class="w-full hidden select" x-ref="formSelect">
                                            <option value="tanda-vital">Tanda Vital Pasien</option>
                                            <option value="pemeriksaan-fisik">Hasil Pemeriksaan Fisik</option>
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
                                        <x-rekammedis.tandavital :tanda-vital="$tanda_vital" wire:model="tanda_vital" />
                                    </div>
                                    <!-- Data Fisik -->
                                    <div x-show="selectedFormsObjective.includes('pemeriksaan-fisik')" style="display: none">
                                        <x-rekammedis.pemeriksaanfisik :pemeriksaan-fisik="$pemeriksaan_fisik" wire:model="pemeriksaan_fisik" />
                                    </div>
                                </div>
                            </div>
                            {{-- ASSESSMENT --}}
                            <div class="bg-base-200 shadow rounded-lg py-6 px-3">
                                <h2 class="text-lg font-semibold mb-4 border-b pb-2">ASSESSMENT</h2>
                                <div x-data="formChoicesAssessment()" x-init="initChoicesAssessment()" x-effect="$wire.selected_forms_assessment = selectedFormsAssessment" class="space-y-6">
                                    <!-- Select Multiple with Choices.js -->
                                    <div>
                                        <label class="label font-semibold">Pilih Form yang Ingin Ditampilkan:</label>
                                        <select id="formSelect" multiple class="w-full hidden select" x-ref="formSelect">
                                            <option value="diagnosa">Disgnosa</option>
                                            <option value="icd_10">ICD 10</option>
                                        </select>
                                    </div>
                                    <!-- ASS-1 -->
                                    <div x-show="selectedFormsAssessment.includes('diagnosa')" style="display: none">
                                        assessment 1
                                    </div>
                                    <!-- ASS-2 -->
                                    <div x-show="selectedFormsAssessment.includes('icd_10')" style="display: none">
                                        assessment 2
                                    </div>
                                </div>
                            </div>
                            {{-- PLAN --}}
                            <div class="bg-base-200 shadow rounded-lg py-6 px-3">
                                <h2 class="text-lg font-semibold mb-4 border-b pb-2">PLAN</h2>
                                <div x-data="formChoicesPlan()" x-init="initChoicesPlan()" x-effect="$wire.selected_forms_plan = selectedFormsPlan" class="space-y-6">
                                    <!-- Select Multiple with Choices.js -->
                                    <div>
                                        <label class="label font-semibold">Pilih Form yang Ingin Ditampilkan:</label>
                                        <select id="formSelect" multiple class="w-full hidden select" x-ref="formSelect">
                                            <option value="plan-1">Plan1</option>
                                            <option value="plan-2">Plan2</option>
                                        </select>
                                    </div>
                                    <!-- PLAN-1 -->
                                    <div x-show="selectedFormsPlan.includes('plan-1')" style="display: none">
                                        plan 1
                                    </div>
                                    <!-- PLAN-2 -->
                                    <div x-show="selectedFormsPlan.includes('plan-2')" style="display: none">
                                        plan 2
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Kolom Kanan (B + D) -->
                <div class="lg:col-span-1">
                    <div class="sticky top-10 space-y-6">
                        <!-- B: Button -->
                        <div class="bg-base-100 shadow rounded-box p-4 pb-7">
                            <h3 class="font-semibold mb-4">Aksi</h3>
                            <button wire:click.prevent="create" class="btn btn-success w-full mb-1" wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="fa-solid fa-plus"></i> Simpan</span>
                                <span wire:loading.inline>Loading...</span>
                            </button>
                            <button class="btn btn-primary mb-1 w-full">
                                <i class="fa-solid fa-hand-holding-heart"></i>
                                    Layanan Tersisa
                                <div class="badge badge-sm badge-base-200 text-base-content">99</div>
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
                this.choicesSubjective = new Choices(this.$refs.formSelect, {
                    removeItemButton: true,
                    placeholderValue: 'Pilih form...',
                    shouldSort: false,
                });

                this.selectedFormsSubjective = Array.from(this.$refs.formSelect.selectedOptions).map(opt => opt.value);

                this.$refs.formSelect.addEventListener('change', (event) => {
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
                this.choicesObjective = new Choices(this.$refs.formSelect, {
                    removeItemButton: true,
                    placeholderValue: 'Pilih form...',
                    shouldSort: false,
                });

                this.selectedFormsObjective = Array.from(this.$refs.formSelect.selectedOptions).map(opt => opt.value);

                this.$refs.formSelect.addEventListener('change', (event) => {
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
                this.choicesAssessment = new Choices(this.$refs.formSelect, {
                    removeItemButton: true,
                    placeholderValue: 'Pilih form...',
                    shouldSort: false,
                });

                this.selectedFormsAssessment = Array.from(this.$refs.formSelect.selectedOptions).map(opt => opt.value);

                this.$refs.formSelect.addEventListener('change', (event) => {
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
                this.choicesPlan = new Choices(this.$refs.formSelect, {
                    removeItemButton: true,
                    placeholderValue: 'Pilih form...',
                    shouldSort: false,
                });

                this.selectedFormsPlan = Array.from(this.$refs.formSelect.selectedOptions).map(opt => opt.value);

                this.$refs.formSelect.addEventListener('change', (event) => {
                    this.selectedFormsPlan = Array.from(event.target.selectedOptions).map(opt => opt.value);
                });
            }
        }
    }
</script>