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
                    <!-- Biodata -->
                    <div class="bg-base-100 text-base-content shadow rounded-box p-6 space-y-4">
                        <h2 class="text-lg font-semibold border-b pb-2">Biodata Pasien</h2>
                        <div class="space-y-2 text-sm mt-2">
                            <!-- Baris 1 -->
                            <div class="grid grid-cols-2 gap-x-6">
                                <div class="flex">
                                    <div class="w-32 font-bold">Nama</div>
                                    <div>: {{ $pasienTerdaftar?->pasien?->nama }}</div>
                                </div>
                                <div class="flex">
                                    <div class="w-32 font-bold">No. RM / No. IHS</div>
                                    <div>: {{ $pasienTerdaftar?->pasien?->no_register }} / {{ $pasienTerdaftar?->pasien?->no_ihs }}</div>
                                </div>
                            </div>

                            <!-- Baris 2 -->
                            <div class="grid grid-cols-2 gap-x-6">
                                <div class="flex">
                                    <div class="w-32 font-bold">Jenis Kelamin</div>
                                    <div>: {{ $pasienTerdaftar?->pasien?->jenis_kelamin }}</div>
                                </div>
                                <div class="flex">
                                    <div class="w-32 font-bold">Tanggal Lahir</div>
                                    <div>: {{ \Carbon\Carbon::parse($pasienTerdaftar?->pasien?->tanggal_lahir)->translatedFormat('d F Y') }}</div>
                                </div>
                                {{-- <div class="flex">
                                    <div class="w-32 font-bold">No. IHS</div>
                                    <div>: {{ $pasienTerdaftar->pasien->no_ihs }}</div>
                                </div> --}}
                            </div>

                            <!-- Baris 3 -->
                            {{-- <div class="grid grid-cols-2 gap-x-6">
                                <div class="flex">
                                    <div class="w-32 font-bold">Jenis Kelamin</div>
                                    <div>: {{ $pasienTerdaftar->pasien->jenis_kelamin }}</div>
                                </div>
                                <div class="flex">
                                    <div class="w-32 font-bold">NIK</div>
                                    <div>: {{ $pasienTerdaftar->pasien->nik }}</div>
                                </div>
                            </div> --}}

                        </div>
                    </div>
                    <!-- Form -->
                    <div class="bg-base-100 shadow rounded-box p-6 space-y-6">
                        <h2 class="text-lg font-semibold border-b pb-2">Form Rekam Medis</h2>
                        <form wire:submit.prevent="updateObjective" class="space-y-6">
                            <input type="hidden" wire:model.defer='nama_dokter'>
                            <input type="hidden" wire:model.defer='pasien_terdaftar_id'>

                            {{-- TABS SOAP --}}
                            <div class="tabs tabs-lift">
                                {{-- TABS OBJECTIVE --}}
                                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="OBJECTIVE" style="background-image: none;" checked/>
                                <div class="tab-content bg-base-100 border-base-300 p-2">
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
                                            @if ($pasienTerdaftar?->poliklinik?->nama_poli == 'Poli Kecantikan')
                                                <option value="pemeriksaan-estetika" @selected($pasienTerdaftar?->poliklinik?->nama_poli == 'Poli Kecantikan')>Pemeriksaan Kulit & Estetika</option>
                                            @elseif($pasienTerdaftar?->poliklinik?->nama_poli == 'Poli Umum')
                                                <option value="tanda-vital" @selected($pasienTerdaftar?->poliklinik?->nama_poli == 'Poli Umum')>Tanda Vital Pasien</option>
                                                <option value="pemeriksaan-fisik" @selected($pasienTerdaftar?->poliklinik?->nama_poli == 'Poli Umum')>Pemeriksaan Fisik</option>
                                            @elseif($pasienTerdaftar?->poliklinik?->nama_poli == 'Poli Gigi')
                                                <option value="tanda-vital" @selected($pasienTerdaftar?->poliklinik?->nama_poli == 'Poli Gigi')>Tanda Vital Pasien</option>
                                                <option value="pemeriksaan-fisik" @selected($pasienTerdaftar?->poliklinik?->nama_poli == 'Poli Gigi')>Pemeriksaan Fisik</option>
                                            @endif
                                            </select>
                                        </div>
                                        <!-- Tingkat Kesadaran -->
                                        <div class="form-control">
                                            <label class="label">Tingkat Kesadaran<span class="text-error">*</span></label>
                                            <select wire:model="tingkat_kesadaran" class="select select-bordered w-full">
                                                <option value="">Pilih Tingkatan</option>
                                                <option value="Sadar Baik/Alert">Sadar Baik/Alert</option>
                                                <option value="Berespon dengan kata-kata">Berespon dengan kata-kata</option>
                                                <option value="Hanya berespons jika dirangsang nyeri"> Hanya berespons jika dirangsang nyeri</option>
                                                <option value="Pasien tidak sadar">Pasien tidak sadar</option>
                                                <option value="Gelisah atau bingung">Gelisah atau bingung</option>
                                                <option value="Acute Confusional States">Acute Confusional States</option>
                                            </select>
                                            @error('keluhan_utama') <span class="text-sm text-red-500">Mohon tentukan tingkat kesadaran pasien untuk melanjutkan pemeriksaan.</span> @enderror
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
                            @can('akses', 'Rekam Medis Tambah')
                            <button wire:click.prevent="updateObjective" class="btn btn-primary w-full mb-1" wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="fa-solid fa-plus"></i> Update</span>
                                <span wire:loading.inline>Loading...</span>
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
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
</script>

{{-- Script ICD --}}
<script>
    function multiSelectIcd10() {
        return {
            open: false,
            selected: @entangle('icd10'),
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
                        this.filteredOptions = data; // sudah {code, name_id, name_en}
                    });
            },

            toggle(item) {
                const exists = this.selected.some(s => s.code === item.code);
                if (!exists) {
                    this.selected.push({
                        code: item.code,
                        name_id: item.name_id,
                        name_en: item.name_en,
                    });
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
