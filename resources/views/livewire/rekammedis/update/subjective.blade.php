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
                        <form wire:submit.prevent="updateSubjective" class="space-y-6">
                            <input type="hidden" wire:model.defer='nama_dokter'>
                            <input type="hidden" wire:model.defer='pasien_terdaftar_id'>

                            {{-- TABS SOAP --}}
                            <div class="tabs tabs-lift">
                                {{-- TABS SUBJECTIVE --}}
                                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="SUBJECTIVE" style="background-image: none;" checked/>
                                <div class="tab-content bg-base-100 border-base-300 p-2">
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
                                                @if ($pasienTerdaftar?->poliklinik?->nama_poli == 'Poli Kecantikan')
                                                    <option value="data-estetika" @selected($pasienTerdaftar?->poliklinik?->nama_poli == 'Poli Kecantikan')>Data Estetika</option>
                                                @elseif($pasienTerdaftar?->poliklinik?->nama_poli == 'Poli Umum')
                                                    <option value="data-kesehatan" @selected($pasienTerdaftar?->poliklinik?->nama_poli == 'Poli Umum')>Data Kesehatan</option>
                                                @elseif($pasienTerdaftar?->poliklinik?->nama_poli == 'Poli Gigi')
                                                    <option value="data-kesehatan" @selected($pasienTerdaftar?->poliklinik?->nama_poli == 'Poli Gigi')>Data Kesehatan</option>
                                                @endif
                                            </select>
                                        </div>
                                        <!-- Keluhan Utama -->
                                        <div class="form-control md:col-span-2">
                                            <label class="label">Keluhan Pasien<span class="text-error">*</span></label>
                                            <input type="text" value="{{ $kajian->dataKesehatan->keluhan_utama ?? null }}" wire:model="keluhan_utama" placeholder="Keluhan Utama" class="input input-bordered w-full" />
                                            @error('keluhan_utama') <span class="text-sm text-red-500">Mohon isi keluhan utama pasien untuk melanjutkan pemeriksaan.</span> @enderror
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
                            <button wire:click.prevent="updateSubjective" class="btn btn-primary w-full mb-1" wire:loading.attr="disabled">
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
</script>
