<div class="bg-base-200 p-4 rounded border border-base-200"
    x-data="{
        obatItems: [{
            id: Date.now() + Math.random(),
            nama_obat_non_racikan: '',
            jumlah_obat_non_racikan: '',
            satuan_obat_non_racikan: '',
            dosis_obat_non_racikan: '',
            hari_obat_non_racikan: '',
            aturan_pakai_obat_non_racikan: ''
        }],
        addObat() {
            this.obatItems.push({
                id: Date.now() + Math.random(),
                nama_obat_non_racikan: '',
                jumlah_obat_non_racikan: '',
                satuan_obat_non_racikan: '',
                dosis_obat_non_racikan: '',
                hari_obat_non_racikan: '',
                aturan_pakai_obat_non_racikan: ''
            });
            this.syncObatToLivewire();
        },
        removeObat(i) {
            this.obatItems.splice(i, 1);
            this.syncObatToLivewire();
        },
        syncItemObat() {
            this.syncObatToLivewire();
        },
        syncObatToLivewire() {
            $wire.set('obat_non_racikan.nama_obat_non_racikan', this.obatItems.map(item => item.nama_obat_non_racikan));
            $wire.set('obat_non_racikan.jumlah_obat_non_racikan', this.obatItems.map(item => item.jumlah_obat_non_racikan));
            $wire.set('obat_non_racikan.satuan_obat_non_racikan', this.obatItems.map(item => item.satuan_obat_non_racikan));
            $wire.set('obat_non_racikan.dosis_obat_non_racikan', this.obatItems.map(item => item.dosis_obat_non_racikan));
            $wire.set('obat_non_racikan.hari_obat_non_racikan', this.obatItems.map(item => item.hari_obat_non_racikan));
            $wire.set('obat_non_racikan.aturan_pakai_obat_non_racikan', this.obatItems.map(item => item.aturan_pakai_obat_non_racikan));
        }
    }"
>
    <div class="divider">Pemberian Obat Non Racikan</div>
    <div class="mb-4 p-4 border-base-300 rounded-lg bg-base-100">
        <p class="font-bold text-base mb-3">Obat Non Racikan</p>

        <template x-for="(item, index) in obatItems" :key="item.id">
            <div class="mb-4 p-4 border border-base-200 rounded-lg bg-base-100">
                <!-- Baris 1: Nama Obat -->
                <div class="flex items-center gap-2">
                    <div class="flex-1"
                        x-data="singleSelectObat(() => item.nama_obat_non_racikan, (val) => { item.nama_obat_non_racikan = val; syncItemObat(index); })"
                        x-init="init()"
                    >
                        <label class="label">
                            <span class="label-text">Nama Obat</span>
                        </label>
                        <div class="relative" @click="setTimeout(() => open = true, 10)">
                            <div class="w-full border border-gray-300 bg-base-100 rounded-xl p-1 flex flex-wrap items-center gap-2 min-h-[2.5rem]">
                                <template x-if="selected">
                                    <span class="bg-primary text-sm rounded-full px-3 py-1 flex items-center gap-1">
                                        <span x-text="selected"></span>
                                        <button type="button" @click.stop="remove()">×</button>
                                    </span>
                                </template>
                                <input type="text"
                                    class="flex-grow min-w-[8ch] text-sm border-none rounded-xl bg-base-100"
                                    placeholder="Ketik untuk cari obat..."
                                    x-model="search"
                                    @focus="open = true"
                                    @input.debounce.300ms="fetchOptions(); open = true"
                                />
                            </div>
                            <div x-show="open" @click.outside="open = false"
                                class="absolute z-10 mt-1 w-full bg-base-200 border border-gray-500 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                                <template x-if="filteredOptions.length > 0">
                                    <template x-for="(opt, idx) in filteredOptions" :key="idx">
                                        <div @click="choose(opt)"
                                            class="px-3 py-2 hover:bg-primary/50 rounded-2xl cursor-pointer text-sm m-1"
                                            :class="selected === opt ? 'bg-primary font-semibold' : ''">
                                            <span x-text="opt"></span>
                                        </div>
                                    </template>
                                </template>
                                <div x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-base-content">
                                    Tidak ada hasil.
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <!-- Tombol hapus -->
                    <button type="button" class="btn btn-error btn-sm" @click="removeObat(index)" x-show="obatItems.length > 1">
                        ✕
                    </button>
                </div>
    
                <!-- Baris 2: Jumlah, Satuan, Aturan Pakai -->
                <div class="flex gap-2 mt-2">
                    <div class="form-control w-20">
                        <label class="label">
                            <span class="label-text">Jumlah</span>
                        </label>
                        <input type="number" min="1" placeholder="10"
                            class="input input-bordered w-full"
                            x-model="item.jumlah_obat_non_racikan"
                            @input="syncItemObat(index)" />
                    </div>

                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">Bentuk Obat</span>
                        </label>
                        <select class="select select-bordered w-full" x-model="item.satuan_obat_non_racikan" @change="syncItemObat(index)">
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
                <div class="flex gap-2 mt-2">
                    <div class="form-control w-20">
                        <label class="label">
                            <span class="label-text">Dosis</span>
                        </label>
                        <input type="number" min="1" placeholder="3"
                            class="input input-bordered w-full"
                            x-model="item.dosis_obat_non_racikan"
                            @input="syncItemObat(index)" />
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
                        <input type="number" min="1" placeholder="1"
                            class="input input-bordered w-full"
                            x-model="item.hari_obat_non_racikan"
                            @input="syncItemObat(index)" />
                    </div>
                    <div class="form-control flex-1">
                        <label class="label">
                            <span class="label-text">Intruksi Pemakaian</span>
                        </label>
                        <input type="text" placeholder="Sesudah Makan"
                            class="input input-bordered w-full"
                            x-model="item.aturan_pakai_obat_non_racikan"
                            @input="syncItemObat(index)" />
                    </div>
                </div>
            </div>
        </template>
        
        <!-- Tombol tambah -->
        <button type="button" class="btn btn-primary btn-sm mt-2" @click="addObat">+ Tambah Obat</button>
    </div>
</div>

@push('scripts')
<script>
    function singleSelectObat(getModel, setModel) {
        return {
            open: false,
            selected: getModel() || '', // simpan 1 value
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
                setModel(this.selected);
            }
        }
    }
</script>
@endpush
