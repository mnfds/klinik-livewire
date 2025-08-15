<div class="bg-base-200 p-4 rounded border border-base-200"
    x-data="{
        obatItems: [{
            id: Date.now() + Math.random(),
            produkdanobat_id: [],
            jumlah_produkdanobat: '',
            satuan_produkdanobat: '',
            aturan_pakai_produkdanobat: ''
        }],
        addObat() {
            this.obatItems.push({
                id: Date.now() + Math.random(),
                produkdanobat_id: [],
                jumlah_produkdanobat: '',
                satuan_produkdanobat: '',
                aturan_pakai_produkdanobat: ''
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
            $wire.set('rencana_pengobatan.produkdanobat_id', this.obatItems.map(item => item.produkdanobat_id));
            $wire.set('rencana_pengobatan.jumlah_produkdanobat', this.obatItems.map(item => item.jumlah_produkdanobat));
            $wire.set('rencana_pengobatan.satuan_produkdanobat', this.obatItems.map(item => item.satuan_produkdanobat));
            $wire.set('rencana_pengobatan.aturan_pakai_produkdanobat', this.obatItems.map(item => item.aturan_pakai_produkdanobat));
        }
    }"
>
    <div class="divider">Rencana Pengobatan</div>
    <div class="mb-4 p-4 border-base-300 rounded-lg bg-base-100">
        <p class="font-bold text-base mb-3">Obat Non Racikan</p>

        <template x-for="(item, index) in obatItems" :key="item.id">
            <div class="mb-4 p-4 border border-base-200 rounded-lg bg-base-100">
                <!-- Baris 1: Nama Obat -->
                <div class="flex items-center gap-2">
                    <div class="flex-1"
                        x-data="multiSelectObat(() => item.produkdanobat_id, (val) => { item.produkdanobat_id = val; syncItemObat(index); })"
                        x-init="init()"
                    >
                        <div class="relative" @click="setTimeout(() => open = true, 10)">
                            <div class="w-full border border-gray-300 bg-base-100 rounded-2xl p-1 flex flex-wrap items-center gap-2 min-h-[2.5rem]">
                                <template x-for="(tag, idx) in selected" :key="idx">
                                    <span class="bg-primary text-sm rounded-full px-3 py-1 flex items-center gap-1">
                                        <span x-text="tag"></span>
                                        <button type="button" @click.stop="remove(tag)">×</button>
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
                                        <div @click="toggle(opt)"
                                            class="px-3 py-2 hover:bg-primary/50 rounded-2xl cursor-pointer text-sm m-1"
                                            :class="selected.includes(opt) ? 'bg-primary font-semibold' : ''">
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
                        <input type="number" min="1" placeholder="Jumlah"
                            class="input input-bordered w-full"
                            x-model="item.jumlah_produkdanobat"
                            @input="syncItemObat(index)" />
                    </div>

                    <div class="form-control w-24">
                        <label class="label">
                            <span class="label-text">Satuan</span>
                        </label>
                        <input type="text" placeholder="Satuan"
                            class="input input-bordered w-full"
                            x-model="item.satuan_produkdanobat"
                            @input="syncItemObat(index)" />
                    </div>

                    <div class="form-control flex-1">
                        <label class="label">
                            <span class="label-text">Aturan Pakai</span>
                        </label>
                        <input type="text" placeholder="Aturan Pakai"
                            class="input input-bordered w-full"
                            x-model="item.aturan_pakai_produkdanobat"
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
    function multiSelectObat(getModel, setModel) {
        return {
            open: false,
            selected: getModel(),
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
                fetch(`/ajax/obat-kfa?q=${encodeURIComponent(this.search)}`)
                    .then(r => r.json())
                    .then(data => {
                        this.filteredOptions = data.map(obat => obat.text);
                    });
            },
            toggle(item) {
                if (!this.selected.includes(item)) {
                    this.selected.push(item);
                } else {
                    this.selected = this.selected.filter(i => i !== item);
                }
                setModel(this.selected);
                this.search = '';
                this.filteredOptions = [];
            },
            remove(item) {
                this.selected = this.selected.filter(i => i !== item);
                setModel(this.selected);
            }
        }
    }
</script>
@endpush
