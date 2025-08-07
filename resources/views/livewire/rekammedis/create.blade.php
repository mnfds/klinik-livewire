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
                    <!-- A: Biodata Pasien -->
                    <div class="bg-base-100 shadow rounded-box p-6 space-y-4">
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

                    <!-- C: Form -->
                    <div class="bg-base-100 shadow rounded-box p-6 space-y-6">
                        <h2 class="text-lg font-semibold border-b pb-2">Form Rekam Medis</h2>
                        <form wire:submit.prevent="create" class="space-y-6">
                            <input type="hidden" wire:model.defer='id_pasien_terdaftar' value="{{  $pasienTerdaftar->id }}" name="id_pasien_terdaftar">

                            {{-- SUBJECTIVE --}}
                            <div class="bg-base-100 shadow rounded-box py-6 px-2">
                                <h2 class="text-lg font-semibold mb-4 border-b pb-2">SUBJECTIVE</h2>
                                <div x-data="formChoicesSubjective()" x-init="initChoicesSubjective()" x-effect="$wire.selected_forms_subjective = selectedFormsSubjective" class="space-y-6">
                                    <!-- Select Multiple with Choices.js -->
                                    <div>
                                        <label class="label font-semibold">Pilih Form yang Ingin Ditampilkan:</label>
                                        <select id="formSelect" multiple class="w-full hidden select" x-ref="formSelect">
                                            <option value="subjective-1">Sub1</option>
                                            <option value="subjective-2">Sub2</option>
                                        </select>
                                    </div>
                                    <!-- SUB-1 -->
                                    <div x-show="selectedFormsSubjective.includes('subjective-1')" style="display: none">
                                        subjective 1
                                    </div>
                                    <!-- SUB-2 -->
                                    <div x-show="selectedFormsSubjective.includes('subjective-2')" style="display: none">
                                        subjective 2
                                    </div>
                                </div>
                            </div>
                            {{-- OBJECTIVE --}}
                            <div class="bg-base-100 shadow rounded-box py-6 px-2">
                                <h2 class="text-lg font-semibold mb-4 border-b pb-2">OBJECTIVE</h2>
                                <div x-data="formChoicesObjective()" x-init="initChoicesObjective()" x-effect="$wire.selected_forms_objective = selectedFormsObjective" class="space-y-6">
                                    <!-- Select Multiple with Choices.js -->
                                    <div>
                                        <label class="label font-semibold">Pilih Form yang Ingin Ditampilkan:</label>
                                        <select id="formSelect" multiple class="w-full hidden select" x-ref="formSelect">
                                            <option value="objective-1">Obj1</option>
                                            <option value="objective-2">Obj2</option>
                                        </select>
                                    </div>
                                    <!-- OBJ-1 -->
                                    <div x-show="selectedFormsObjective.includes('objective-1')" style="display: none">
                                        objective 1
                                    </div>
                                    <!-- OBJ-2 -->
                                    <div x-show="selectedFormsObjective.includes('objective-2')" style="display: none">
                                        objective 2
                                    </div>
                                </div>
                            </div>
                            {{-- ASSESSMENT --}}
                            <div class="bg-base-100 shadow rounded-box py-6 px-2">
                                <h2 class="text-lg font-semibold mb-4 border-b pb-2">ASSESSMENT</h2>
                                <div x-data="formChoicesAssessment()" x-init="initChoicesAssessment()" x-effect="$wire.selected_forms_assessment = selectedFormsAssessment" class="space-y-6">
                                    <!-- Select Multiple with Choices.js -->
                                    <div>
                                        <label class="label font-semibold">Pilih Form yang Ingin Ditampilkan:</label>
                                        <select id="formSelect" multiple class="w-full hidden select" x-ref="formSelect">
                                            <option value="assessment-1">Ass1</option>
                                            <option value="assessment-2">Ass2</option>
                                        </select>
                                    </div>
                                    <!-- ASS-1 -->
                                    <div x-show="selectedFormsAssessment.includes('assessment-1')" style="display: none">
                                        assessment 1
                                    </div>
                                    <!-- ASS-2 -->
                                    <div x-show="selectedFormsAssessment.includes('assessment-2')" style="display: none">
                                        assessment 2
                                    </div>
                                </div>
                            </div>
                            {{-- PLAN --}}
                            <div class="bg-base-100 shadow rounded-box py-6 px-2">
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
                    <div class="sticky top-20 space-y-6">
                        <!-- B: Button -->
                        <div class="bg-base-100 shadow rounded-box p-4 pb-7">
                            <h3 class="font-semibold mb-4">Aksi</h3>
                            <button class="btn btn-success mb-1 w-full">
                                <i class="fa-solid fa-plus"></i> Simpan
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

                        <!-- D: Kajian Awal / Biodata -->
                        <div class="bg-base-100 shadow rounded-box p-4 space-y-3 text-sm h-80 overflow-y-auto">
                            <h3 class="font-semibold">Informasi Kajian Awal</h3>
                                    @if ($kajian)

                                        {{-- Pemeriksaan Fisik --}}
                                        @if ($kajian->pemeriksaanFisik)
                                            <div>
                                                <div class="font-semibold mb-1">Pemeriksaan Fisik</div>
                                                <div class="space-y-1">
                                                    <div>Tinggi Badan : {{ $kajian->pemeriksaanFisik->tinggi_badan }} cm</div>
                                                    <div>Berat Badan : {{ $kajian->pemeriksaanFisik->berat_badan }} kg</div>
                                                    <div>IMT : {{ $kajian->pemeriksaanFisik->imt }}</div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Tanda Vital --}}
                                        @if ($kajian->tandaVital)
                                            <div>
                                                <div class="font-semibold mb-1">Tanda Vital</div>
                                                <div class="space-y-1">
                                                    <div>Suhu Tubuh : {{ $kajian->tandaVital->suhu_tubuh }} °C</div>
                                                    <div>Nadi : {{ $kajian->tandaVital->nadi }} bpm</div>
                                                    <div>Tekanan Darah : {{ $kajian->tandaVital->sistole }}/{{ $kajian->tandaVital->diastole }} mmHg</div>
                                                    <div>Frekuensi Napas : {{ $kajian->tandaVital->frekuensi_pernapasan }} /menit</div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Data Kesehatan --}}
                                        @if ($kajian->dataKesehatan)
                                            <div>
                                                <div class="font-semibold mb-1">Data Kesehatan</div>
                                                <div class="space-y-1">
                                                    <div>Keluhan Utama : {{ $kajian->dataKesehatan->keluhan_utama ?? '-' }}</div>
                                                    <div>Status Perokok : {{ $kajian->dataKesehatan->status_perokok ?? '-' }}</div>

                                                    @php
                                                        $riwayat_penyakit = $kajian->dataKesehatan->riwayat_penyakit ? json_decode($kajian->dataKesehatan->riwayat_penyakit,true) : [];
                                                        $riwayat_alergi_obat = $kajian->dataKesehatan->riwayat_alergi_obat ? json_decode($kajian->dataKesehatan->riwayat_alergi_obat,true) : [];
                                                        $obat = $kajian->dataKesehatan->obat_sedang_dikonsumsi ? json_decode($kajian->dataKesehatan->obat_sedang_dikonsumsi,true) : [];
                                                        $alergi_lain = $kajian->dataKesehatan->riwayat_alergi_lainnya ? json_decode($kajian->dataKesehatan->riwayat_alergi_lainnya,true) : [];
                                                    @endphp

                                                    <div>
                                                        Riwayat Penyakit :
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

                                                    <div>
                                                        Riwayat Alergi Obat :
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

                                                    <div>
                                                        Obat Dikonsumsi :
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

                                                    <div>
                                                        Alergi Lainnya :
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
                                            <div>
                                                <div class="font-semibold mb-1">Data Estetika</div>
                                                @php
                                                    $decode = fn($v) => $v ? (is_array(json_decode($v, true)) ? json_decode($v, true) : [$v]) : [];
                                                    $problem = $decode($kajian->dataEstetika->problem_dihadapi);
                                                    $tindakan = $decode($kajian->dataEstetika->tindakan_sebelumnya);
                                                    $metode_kb = $decode($kajian->dataEstetika->metode_kb);
                                                @endphp
                                                <div class="space-y-1">
                                                    <div>
                                                        Problem Dihadapi :
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

                                                    <div>Lama Problem : {{ $kajian->dataEstetika->lama_problem ?? '-' }}</div>

                                                    <div>
                                                        Tindakan Sebelumnya :
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

                                                    <div>Penyakit Dialami : {{ $kajian->dataEstetika->penyakit_dialami ?? '-' }}</div>
                                                    <div>Alergi Kosmetik : {{ $kajian->dataEstetika->alergi_kosmetik ?? '-' }}</div>

                                                    <div>Sedang Hamil : {{ ucfirst($kajian->dataEstetika->sedang_hamil) ?? '-' }}</div>
                                                    <div>Usia Kehamilan : {{ $kajian->dataEstetika->usia_kehamilan ? $kajian->dataEstetika->usia_kehamilan.' bln' : '-' }}</div>

                                                    <div>
                                                        Metode KB :
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

                                                    <div>Pengobatan Saat Ini : {{ $kajian->dataEstetika->pengobatan_saat_ini ?? '-' }}</div>
                                                    <div>Produk Kosmetik : {{ $kajian->dataEstetika->produk_kosmetik ?? '-' }}</div>
                                                </div>
                                            </div>
                                        @endif

                                    @else
                                        <p class="italic text-gray-500">Belum ada kajian awal.</p>
                                    @endif
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