<div class="bg-base-200 p-4 rounded border border-base-200"
    x-data="racikanForm(@entangle('racikanItems'))">

    <div class="divider">Pemberian Obat Racikan</div>

    <template x-for="(racikan, i) in racikanItems" :key="racikan.id">
        <div class="mb-6 p-4 border border-base-300 rounded-lg bg-base-100">

            <!-- Data Racikan Utama -->
            <div class="flex items-center gap-2 mb-2">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Nama Racikan</span>
                    </label>
                    <input type="text" placeholder="Resep Obat Batuk Dan Flu"
                        class="input input-bordered w-full"
                        x-model="racikan.nama_racikan" />
                </div>
                    <button type="button" class="btn btn-error btn-sm"
                    @click="removeRacikan(i)"
                    x-show="racikanItems.length > 1">✕</button>
            </div>

            <div class="flex gap-2 mb-2">
                <div class="form-control w-20">
                    <label class="label">
                        <span class="label-text">Jumlah</span>
                    </label>
                    <input type="number" placeholder="12" class="input input-bordered w-full"
                        x-model="racikan.jumlah_racikan" />
                </div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Bentuk Obat</span>
                    </label>
                    <select x-model="racikan.satuan_racikan" class="select w-full select-bordered">
                        <option disabled selected>Pilih Bentuk Obat</option>
                            <optgroup label="Oral">
                                <option value="Tablet">Tablet</option>
                                <option value="Kaplet">Kaplet</option>
                                <option value="Kapsul">Kapsul</option>
                                <option value="Tablet Kunyah">Tablet Kunyah</option>
                                <option value="Tablet Effervescent">Tablet Effervescent</option>
                                <option value="Sirup">Sirup</option>
                                <option value="Suspensi">Suspensi</option>
                                <option value="Granula">Granula</option>
                            </optgroup>

                            <optgroup label="Injeksi & Parenteral">
                                <option value="Larutan Injeksi">Larutan Injeksi</option>
                                <option value="Infus">Infus</option>
                                <option value="Suspensi Injeksi">Suspensi Injeksi</option>
                                <option value="Serbuk Injeksi">Serbuk Injeksi</option>
                            </optgroup>

                            <optgroup label="Tetes & Semprot">
                                <option value="Tetes Mata">Tetes Mata</option>
                                <option value="Tetes Telinga">Tetes Telinga</option>
                                <option value="Tetes Hidung">Tetes Hidung</option>
                                <option value="Larutan Inhalasi">Larutan Inhalasi</option>
                                <option value="Suspensi Inhalasi">Suspensi Inhalasi</option>
                                <option value="Semprot Hidung">Semprot Hidung</option>
                            </optgroup>

                            <optgroup label="Topikal">
                                <option value="Krim">Krim</option>
                                <option value="Salep">Salep</option>
                                <option value="Gel">Gel</option>
                                <option value="Topical Spray">Topical Spray</option>
                            </optgroup>

                            <optgroup label="Lainnya">
                                <option value="Suppositoria">Suppositoria</option>
                                <option value="Enema">Enema</option>
                                <option value="Obat Kumur">Obat Kumur</option>
                            </optgroup>
                    </select>
                </div>
            </div>

            <div class="flex gap-2 mb-2">
                <div class="form-control w-20">
                    <label class="label">
                        <span class="label-text">Dosis</span>
                    </label>
                    <input type="number" placeholder="1" class="input input-bordered w-full"
                        x-model="racikan.dosis_obat_racikan" />
                </div>

                <div class="form-control w-10">
                    <label class="label">
                        <span class="label-text"></span>
                    </label>
                    <input type="text" placeholder="X" class="input input-ghost" />
                </div>
                
                <div class="form-control w-20">
                    <label class="label">
                        <span class="label-text">Hari</span>
                    </label>
                    <input type="number" placeholder="1" class="input input-bordered w-full"
                        x-model="racikan.hari_obat_racikan" />
                </div>
                
                <div class="form-control flex-1">
                    <label class="label">
                        <span class="label-text">Instruksi Pemakaian</span>
                    </label>
                    <input type="text" placeholder="Sebelum Makan" class="input input-bordered w-full"
                        x-model="racikan.aturan_pakai_racikan" />
                </div>
            </div>

            <div class="form-control mb-3">
                <label class="label">
                    <span class="label-text">Intruksi Racikan</span>
                </label>
                <input type="text" placeholder="Metode Racikan"
                    class="input input-bordered w-full"
                    x-model="racikan.metode_racikan" />
            </div>

            <!-- Bahan Racikan -->
            <label class="label mb-2">
                <span class="label-text">Bahan Racikan</span>
            </label>
            <template x-for="(bahan, j) in racikan.bahan" :key="bahan.id">
                <div class="flex gap-2 mb-2 items-center"
                    x-data="singleSelectObatRacikan(
                        () => bahan.nama_obat_racikan, 
                        (val) => { bahan.nama_obat_racikan = val }
                    )">
                    <div class="grid grid-cols-12 gap-2 mb-2">
                        <!-- Nama Obat -->
                        <div class="form-control col-span-8">
                            <label class="label">
                                <span class="label-text">Nama Obat</span>
                            </label>
                            <!-- Input search / pilih obat -->
                            <div class="relative w-full">
                                <template x-if="!selected">
                                    <input type="text" 
                                        class="input input-bordered w-full"
                                        placeholder="Cari obat..."
                                        x-model="search"
                                        @input.debounce.300ms="fetchOptions(); open = true"
                                        @focus="open = true" />
                                </template>

                                <!-- Tampilan jika sudah dipilih -->
                                <template x-if="selected">
                                    <div class="flex items-center justify-between bg-base-200 rounded-lg px-3 py-2">
                                        <span x-text="selected"></span>
                                        <button type="button" class="btn btn-xs btn-error ml-2" @click="remove()">✕</button>
                                    </div>
                                </template>

                                <!-- Dropdown hasil pencarian -->
                                <div x-show="open" 
                                    @click.outside="open = false"
                                    class="absolute z-10 mt-1 w-full bg-base-100 border rounded-lg shadow-lg max-h-40 overflow-y-auto">

                                    <template x-for="(item, index) in filteredOptions" :key="index">
                                        <div @click="choose(item)" 
                                            class="px-3 py-2 hover:bg-primary/50 cursor-pointer text-sm"
                                            :class="selected === item ? 'bg-primary text-white font-semibold' : ''">
                                            <span x-text="item"></span>
                                        </div>
                                    </template>

                                    <div x-show="filteredOptions.length === 0 && search" 
                                        class="px-3 py-2 text-sm text-gray-500">
                                        Tidak ada hasil.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Jumlah Obat -->
                        <div class="form-control col-span-2">
                            <label class="label">
                                <span class="label-text">Jumlah Obat</span>
                            </label>
                            <input type="number" placeholder="Jumlah"
                                class="input input-bordered w-full"
                                x-model="bahan.jumlah_obat_racikan" />
                        </div>
                        
                        <!-- Bentuk Obat -->
                        <div class="form-control col-span-2">
                            <label class="label">
                                <span class="label-text">Bentuk Obat</span>
                            </label>
                            <select class="select select-bordered w-full" x-model="bahan.satuan_obat_racikan">
                                <option value="">Pilih Bentuk Obat</option>

                                <optgroup label="Oral">
                                    <option value="Tablet">Tablet</option>
                                    <option value="Kaplet">Kaplet</option>
                                    <option value="Kapsul">Kapsul</option>
                                    <option value="Tablet Kunyah">Tablet Kunyah</option>
                                    <option value="Tablet Effervescent">Tablet Effervescent</option>
                                    <option value="Sirup">Sirup</option>
                                    <option value="Suspensi">Suspensi</option>
                                    <option value="Granula">Granula</option>
                                </optgroup>

                                <optgroup label="Injeksi & Parenteral">
                                    <option value="Larutan Injeksi">Larutan Injeksi</option>
                                    <option value="Infus">Infus</option>
                                    <option value="Suspensi Injeksi">Suspensi Injeksi</option>
                                    <option value="Serbuk Injeksi">Serbuk Injeksi</option>
                                </optgroup>

                                <optgroup label="Tetes & Semprot">
                                    <option value="Tetes Mata">Tetes Mata</option>
                                    <option value="Tetes Telinga">Tetes Telinga</option>
                                    <option value="Tetes Hidung">Tetes Hidung</option>
                                    <option value="Larutan Inhalasi">Larutan Inhalasi</option>
                                    <option value="Suspensi Inhalasi">Suspensi Inhalasi</option>
                                    <option value="Semprot Hidung">Semprot Hidung</option>
                                </optgroup>

                                <optgroup label="Topikal">
                                    <option value="Krim">Krim</option>
                                    <option value="Salep">Salep</option>
                                    <option value="Gel">Gel</option>
                                    <option value="Topical Spray">Topical Spray</option>
                                </optgroup>

                                <optgroup label="Lainnya">
                                    <option value="Suppositoria">Suppositoria</option>
                                    <option value="Enema">Enema</option>
                                    <option value="Obat Kumur">Obat Kumur</option>
                                </optgroup>
                            </select>

                        </div>
                    </div>

                    <!-- Tombol Hapus -->
                    <button type="button" class="btn btn-error btn-sm"
                        @click="removeBahan(i, j)"
                        x-show="racikan.bahan.length > 1">✕</button>
                </div>
            </template>

            <button type="button" class="btn btn-primary btn-xs mt-2" @click="addBahan(i)">+ Tambah Bahan</button>
        </div>
    </template>

    <!-- Tambah Racikan -->
    <button type="button" class="btn btn-primary btn-sm mt-4" @click="addRacikan">+ Tambah Racikan</button>
</div>

@push('scripts')
<script>
function racikanForm(livewireBinding) {
    return {
        racikanItems: livewireBinding,
        addRacikan() {
            this.racikanItems.push({
                id: Date.now() + Math.random(),
                nama_racikan: '',
                jumlah_racikan: '',
                satuan_racikan: '',
                dosis_obat_racikan: '',
                hari_obat_racikan: '',
                aturan_pakai_racikan: '',
                metode_racikan: '',
                bahan: [{
                    id: Date.now() + Math.random(),
                    nama_obat_racikan: '',
                    jumlah_obat_racikan: '',
                    satuan_obat_racikan: ''
                }]
            });
        },
        removeRacikan(i) {
            this.racikanItems.splice(i, 1);
        },
        addBahan(i) {
            this.racikanItems[i].bahan.push({
                id: Date.now() + Math.random(),
                nama_obat_racikan: '',
                jumlah_obat_racikan: '',
                satuan_obat_racikan: ''
            });
        },
        removeBahan(i, j) {
            this.racikanItems[i].bahan.splice(j, 1);
        }
    }
}

function singleSelectObatRacikan(getModel, setModel) {
    return {
        open: false,
        selected: getModel() || '',
        search: '',
        filteredOptions: [],

        fetchOptions() {
            if (this.search.trim() === '') {
                this.filteredOptions = [];
                return;
            }
            fetch(`/ajax/obat-kfa?q=${encodeURIComponent(this.search)}`)
                .then(r => r.json())
                .then(data => {
                    this.filteredOptions = data.map(obat => obat.text);
                });
        },
        choose(item) {
            this.selected = item;
            setModel(this.selected);
            this.search = '';
            this.filteredOptions = [];
            this.open = false;
        },
        remove() {
            this.selected = '';
            setModel('');
        }
    }
}
</script>
@endpush
