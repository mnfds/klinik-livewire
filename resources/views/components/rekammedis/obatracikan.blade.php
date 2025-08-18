<div class="bg-base-200 p-4 rounded border border-base-200"
    x-data="{
        racikanItems: [{
            id: Date.now() + Math.random(),
            nama_racikan: '',
            jumlah_racikan: '',
            satuan_racikan: '',
            aturan_pakai_racikan: '',
            metode_racikan: '',
            bahan: [{
                id: Date.now() + Math.random(),
                nama_obat_racikan: '',
                jumlah_obat_racikan: '',
                satuan_obat_racikan: ''
            }]
        }],
        addRacikan() {
            this.racikanItems.push({
                id: Date.now() + Math.random(),
                nama_racikan: '',
                jumlah_racikan: '',
                satuan_racikan: '',
                aturan_pakai_racikan: '',
                metode_racikan: '',
                bahan: [{
                    id: Date.now() + Math.random(),
                    nama_obat_racikan: '',
                    jumlah_obat_racikan: '',
                    satuan_obat_racikan: ''
                }]
            });
            this.syncToLivewire();
        },
        removeRacikan(i) {
            this.racikanItems.splice(i, 1);
            this.syncToLivewire();
        },
        addBahan(i) {
            this.racikanItems[i].bahan.push({
                id: Date.now() + Math.random(),
                nama_obat_racikan: '',
                jumlah_obat_racikan: '',
                satuan_obat_racikan: ''
            });
            this.syncToLivewire();
        },
        removeBahan(i, j) {
            this.racikanItems[i].bahan.splice(j, 1);
            this.syncToLivewire();
        },
        syncItem() {
            this.syncToLivewire();
        },
        syncToLivewire() {
            // mapping untuk Livewire
            $wire.set('obat_racikan.nama_racikan', this.racikanItems.map(r => r.nama_racikan));
            $wire.set('obat_racikan.jumlah_racikan', this.racikanItems.map(r => r.jumlah_racikan));
            $wire.set('obat_racikan.satuan_racikan', this.racikanItems.map(r => r.satuan_racikan));
            $wire.set('obat_racikan.aturan_pakai_racikan', this.racikanItems.map(r => r.aturan_pakai_racikan));
            $wire.set('obat_racikan.metode_racikan', this.racikanItems.map(r => r.metode_racikan));

            // flatten bahan racikan
            $wire.set('bahan_racikan.nama_obat_racikan', this.racikanItems.flatMap(r => r.bahan.map(b => b.nama_obat_racikan)));
            $wire.set('bahan_racikan.jumlah_obat_racikan', this.racikanItems.flatMap(r => r.bahan.map(b => b.jumlah_obat_racikan)));
            $wire.set('bahan_racikan.satuan_obat_racikan', this.racikanItems.flatMap(r => r.bahan.map(b => b.satuan_obat_racikan)));
        }
    }"
>
    <div class="divider">Pemberian Obat Racikan</div>

    <template x-for="(racikan, i) in racikanItems" :key="racikan.id">
        <div class="mb-6 p-4 border border-base-300 rounded-lg bg-base-100">
            
            <!-- Data Racikan Utama -->
            <div class="flex items-center gap-2 mb-2">
                <input type="text" placeholder="Nama Racikan"
                    class="input input-bordered flex-1"
                    x-model="racikan.nama_racikan"
                    @input="syncItem()" />

                <button type="button" class="btn btn-error btn-sm"
                    @click="removeRacikan(i)"
                    x-show="racikanItems.length > 1">✕</button>
            </div>

            <div class="flex gap-2 mb-2">
                <input type="number" placeholder="Jumlah" class="input input-bordered w-24"
                    x-model="racikan.jumlah_racikan" @input="syncItem()" />

                <input type="text" placeholder="Satuan" class="input input-bordered w-24"
                    x-model="racikan.satuan_racikan" @input="syncItem()" />

                <input type="text" placeholder="Aturan Pakai" class="input input-bordered flex-1"
                    x-model="racikan.aturan_pakai_racikan" @input="syncItem()" />
            </div>

            <div class="form-control mb-3">
                <input type="text" placeholder="Metode Racikan"
                    class="input input-bordered"
                    x-model="racikan.metode_racikan"
                    @input="syncItem()" />
            </div>

            <!-- Bahan Racikan -->
            <p class="font-semibold mb-2">Bahan Racikan</p>
            <template x-for="(bahan, j) in racikan.bahan" :key="bahan.id">
                <div class="flex gap-2 mb-2 items-center"
                    x-data="singleSelectObatRacikan(
                        () => bahan.nama_obat_racikan, 
                        (val) => { bahan.nama_obat_racikan = val; syncItem(); }
                    )">
                    
                    <!-- Input search / pilih obat -->
                    <div class="relative flex-1">
                        <template x-if="!selected">
                            <input type="text" 
                                class="input input-bordered w-full"
                                placeholder="Cari obat..."
                                x-model="search"
                                @input.debounce.300ms="fetchOptions(); open = true"
                                @focus="open = true"
                            />
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

                    <!-- Input Jumlah -->
                    <input type="number" placeholder="Jumlah"
                        class="input input-bordered w-24"
                        x-model="bahan.jumlah_obat_racikan"
                        @input="syncItem()" />

                    <!-- Input Satuan -->
                    <input type="text" placeholder="Satuan"
                        class="input input-bordered w-24"
                        x-model="bahan.satuan_obat_racikan"
                        @input="syncItem()" />

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
    function singleSelectObatRacikan(getModel, setModel) {
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
