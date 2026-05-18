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
                        <form wire:submit.prevent="updateAssessment" class="space-y-6">
                            <input type="hidden" wire:model.defer='nama_dokter'>
                            <input type="hidden" wire:model.defer='pasien_terdaftar_id'>

                            {{-- TABS SOAP --}}
                            <div class="tabs tabs-lift">
                                {{-- TABS ASSESSMENT --}}
                                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="ASSESSMENT" style="background-image: none;" checked/>
                                <div class="tab-content bg-base-100 border-base-300 p-2">
                                    {{-- ASSESSMENT --}}
                                    <div id="assessment" class="bg-base-200 shadow rounded-lg py-6 px-3 scroll-mt-16"
                                        x-data="formChoicesAssessment()"
                                        x-init="initChoicesAssessment()"
                                        x-effect="$wire.selected_forms_assessment = selectedFormsAssessment">
                                            
                                        <h2 class="text-lg font-semibold mb-4 border-b pb-2">ASSESSMENT</h2>
                                            
                                        <!-- ICD 10 -->
                                        {{-- <div x-show="selectedFormsAssessment.includes('icd_10')" style="display: none"> --}}
                                            <div class="form-control" x-data="multiSelectIcd10()" x-init="init()">
                                                <label class="label">ICD 10<span class="text-error">*</span></label>

                                                <!-- Input Area -->
                                                <div class="relative" @click="setTimeout(() => open = true, 10)">
                                                    <div class="w-full border border-gray-300 bg-base-100 rounded-2xl p-1 flex flex-wrap items-center gap-2 min-h-[2.5rem] focus-within:ring-2 focus-within:ring-black transition"
                                                        :class="{ 'ring-2 ring-black': open }">

                                                        <!-- Selected tags -->
                                                        <template x-for="(tag, index) in selected" :key="tag.code">
                                                            <span class="bg-primary text-sm rounded-full px-3 py-1 flex items-center gap-1">
                                                                <span x-text="`${tag.code} - ${tag.name_id}`"></span>
                                                                <button type="button" @click.stop="remove(tag.code)">×</button>
                                                            </span>
                                                        </template>

                                                        <!-- Input search -->
                                                        <input type="text"
                                                            class="flex-grow min-w-[8ch] text-sm border-none rounded-xl bg-base-100"
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
                                                                    <span x-text="`${item.code} - ${item.name_id}`"></span>
                                                                </div>
                                                            </template>
                                                        </template>

                                                        <div x-show="filteredOptions.length === 0"
                                                            class="px-3 py-2 text-sm text-base-content">
                                                            Tidak ada hasil.
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Binding ke Livewire: kirim array full object -->
                                                <input type="hidden" wire:model="icd10" :value="JSON.stringify(selected)">

                                                <span class="text-xs text-gray-400 mt-1">* Klik untuk pilih, klik ulang untuk hapus</span>
                                            </div>
                                            @error('icd10') <span class="text-sm text-red-500">Mohon tentukan diagnosa ICD 10 pasien untuk melanjutkan pemeriksaan.</span> @enderror

                                        <!-- Diagnosa -->
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
                            <button wire:click.prevent="updateAssessment" class="btn btn-primary w-full mb-1" wire:loading.attr="disabled">
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