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
                            Rekam Medis Pasien
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pendaftaran.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Isi Kajian Awal Pasien
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-lg font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Kajian Awal (Anamnesa)
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {{-- Kolom Kiri: Form --}}
                <div class="lg:col-span-3 space-y-6">
                    <form wire:submit.prevent="create" class="space-y-6">
                        {{-- SECTION: INFORMASI --}}
    {{ $pasienTerdaftar }}

                        <div class="bg-base-100 shadow rounded-box p-6 space-y-6">
                            <h2 class="text-lg font-semibold border-b pb-2">Informasi Terkait</h2>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Tenaga Kesehatan Pengkaji</span>
                                </label>
                                <select wire:model.defer="pengkaji_id" class="select select-bordered w-full">
                                    <option value="">Pilih Nakes</option>
                                    {{-- <option value="1">Nakes A</option> --}}
                                </select>
                                @error('pengkaji_id') 
                                    <span class="text-sm text-red-500">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>

                        {{-- SECTION: FORM PILIHAN --}}
                        <div class="bg-base-100 shadow rounded-box p-6">
                            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Isi Anamnesa</h2>
                            <div x-data="formChoices()" x-init="initChoices()" class="space-y-6">
                                <!-- Select Multiple with Choices.js -->
                                <div>
                                    <label class="label font-semibold">Pilih Form yang Ingin Ditampilkan:</label>
                                    <select id="formSelect" multiple class="w-full hidden select" x-ref="formSelect">
                                        <option value="tanda-vital">Tanda Vital</option>
                                        <option value="pemeriksaan-fisik">Pemeriksaan Fisik</option>
                                        <option value="data-kesehatan">Data Kesehatan</option>
                                    </select>
                                </div>
                                <!-- Form 1 -->
                                <div x-show="selectedForms.includes('tanda-vital')" style="display: none">
                                    <x-kajianawal.tanda-vital model="tanda_vital" />
                                </div>
                                <!-- Form 2 -->
                                <div x-show="selectedForms.includes('pemeriksaan-fisik')" style="display: none">
                                    <x-kajianawal.pemeriksaan-fisik model="pemeriksaan_fisik" />
                                </div>
                                <!-- Form 2 -->
                                <div x-show="selectedForms.includes('data-kesehatan')" style="display: none">
                                    <x-kajianawal.data-kesehatan model="data_kesehatan" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Kolom Kanan: Aksi dan Biodata --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-20 space-y-6">
                        {{-- Tombol Aksi --}}
                        <div class="bg-base-100 shadow rounded-box p-4 pb-7">
                            <h3 class="font-semibold mb-4">Aksi</h3>
                            <button wire:click="create" class="btn btn-success w-full" wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="fa-solid fa-plus"></i> Simpan</span>
                                <span wire:loading.inline>Loading...</span>
                            </button>
                            <a wire:navigate href="{{ route('pendaftaran.data') }}" class="btn btn-primary w-full my-1">
                                <i class="fa-solid fa-rotate-left"></i>
                                Kembali
                            </a>
                        </div>

                        {{-- Informasi Biodata & Kunjungan --}}
                        <div class="bg-base-100 shadow rounded-box p-4 text-sm">
                            <h3 class="text-md font-semibold mb-3">Biodata & Catatan Kunjungan</h3>

                            <div class="space-y-4">
                                {{-- Data Klinik --}}
                                <div class="space-y-1">
                                    <h4 class="font-semibold mb-1">Catatan Kunjungan</h4>
                                    <div><span class="font-bold">Poliklinik:</span> Poli Umum</div>
                                    <div><span class="font-bold">No. Register:</span> REG-20250728-001</div>
                                    <div><span class="font-bold">No. IHS:</span> IHS31943</div>
                                    <div><span class="font-bold">Tanggal Kunjungan:</span> 2025-07-28</div>
                                </div>

                                {{-- Data Pribadi --}}
                                <div class="space-y-1">
                                    <h4 class="font-semibold mb-1">Biodata Pasien</h4>
                                    <div><span class="font-bold">Nama Pasien:</span> John Doe</div>
                                    <div><span class="font-bold">NIK:</span> 619343079023409</div>
                                    <div><span class="font-bold">Tanggal Lahir:</span> 1990-02-20</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
<script>
    function formChoices() {
        return {
            selectedForms: [],
            choicesInstance: null,

            initChoices() {
                this.choicesInstance = new Choices(this.$refs.formSelect, {
                    removeItemButton: true,
                    placeholderValue: 'Pilih form...',
                    shouldSort: false,
                });

                this.selectedForms = Array.from(this.$refs.formSelect.selectedOptions).map(opt => opt.value);

                this.$refs.formSelect.addEventListener('change', (event) => {
                    this.selectedForms = Array.from(event.target.selectedOptions).map(opt => opt.value);
                });
            }
        }
    }
</script>
