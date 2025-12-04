<div class="bg-base-200 p-4 rounded border border-base-200">
@props([
    'dataKesehatan' => [
        'status_perokok' => null,
        'riwayat_penyakit' => null,
        'riwayat_alergi_obat' => null,
        'obat_sedang_dikonsumsi' => null,
        'riwayat_alergi_lainnya' => null,
    ]
])

    <div class="divider">Data Kesehatan</div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <!-- Status Perokok -->
        <div class="form-control">
            <label class="label">Status Perokok</label>
            <select wire:model="data_kesehatan.status_perokok" class="select select-bordered w-full">
                <option value="">Status Perokok</option>
                <option value="tidak">Tidak</option>
                <option value="iya">Iya</option>
                <option value="berhenti">Berhenti</option>
            </select>
        </div>
        
        <!-- Riwayat Penyakit -->
        <div class="form-control" x-data="multiSelect()" x-init="init()">
            <label class="label">Riwayat Penyakit</label>

            <div class="relative">
                <div class="w-full border border-gray-300 bg-base-100 rounded-2xl p-1 flex flex-wrap items-center gap-2"
                    :class="{ 'ring-2 ring-black': open }"
                    @click="open = true">

                    <!-- Selected tags -->
                    <template x-for="(tag, index) in selected" :key="index">
                        <span class="bg-primary text-sm rounded-full px-3 py-1 flex items-center gap-1">
                            <span x-text="tag"></span>
                            <button type="button" class="font-bold text-md" @click.stop="remove(tag)">×</button>
                        </span>
                    </template>

                    <!-- Search box -->
                    <input type="text"
                        x-model="search"
                        @input.debounce.300ms="fetchOptions(); open = true"
                        class="flex-grow min-w-[8ch] text-sm border-none bg-base-100"
                        placeholder="Cari riwayat penyakit..." />
                </div>

                <!-- Dropdown -->
                <div x-show="open" @click.outside="open = false"
                    class="absolute z-10 mt-1 w-full bg-base-200 border border-gray-500 rounded-lg shadow-lg max-h-40 overflow-y-auto">

                    <!-- Loading -->
                    <div x-show="loading" class="px-3 py-2 text-sm">Mencari...</div>

                    <!-- Hasil -->
                    <template x-for="item in filteredOptions" :key="item.name">
                        <div @click="toggle(item)"
                            class="px-3 py-2 hover:bg-primary/50 rounded-2xl cursor-pointer text-sm m-1">
                            <span x-text="item.label"></span>
                        </div>
                    </template>

                    <!-- No result -->
                    <div x-show="!loading && filteredOptions.length === 0"
                        class="px-3 py-2 text-sm">
                        Tidak ada hasil.
                    </div>
                </div>
            </div>

            <span class="text-xs text-gray-400 mt-1">* Klik untuk pilih, klik ulang untuk hapus</span>
        </div>

        <!-- Riwayat Alergi Obat -->
        <div class="form-control" x-data="multiSelectAlergiObat()" x-init="init()">
            <label class="label">
                Riwayat Alergi Obat
            </label>
            <!-- Input Area -->
            <div
                class="relative" @click="setTimeout(() => open = true, 10)">
                <div class="w-full border border-gray-300 bg-base-100 text-base-primary rounded-2xl p-1 flex flex-wrap items-center gap-2 min-h-[2.5rem] focus-within:ring-2 focus-within:ring-black transition":class="{ 'ring-2 ring-black': open }">
                    <!-- Selected tags -->
                    <template x-for="(tag, index) in selected" :key="index">
                        <span class="bg-primary text-sm rounded-full px-3 py-1 flex items-center gap-1">
                            <span x-text="tag"></span>
                            <button type="button" class="font-bold text-md" @click.stop="remove(tag)">×</button>
                        </span>
                    </template>

                    <!-- Input search -->
                    <input type="text" class="flex-grow min-w-[8ch] text-sm border-none rounded-xl bg-base-100" placeholder="Ketik untuk cari obat..." x-model="search" @focus="open = true" @input.debounce.300ms="fetchOptions(); open = true"/>
                </div>

                <!-- Dropdown Menu -->
                <div x-show="open" @click.outside="open = false" class="absolute z-10 mt-1 w-full bg-base-200 border border-gray-500 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                    <!-- Hasil ditemukan -->
                    <template x-if="filteredOptions.length > 0">
                        <template x-for="(item, index) in filteredOptions" :key="index">
                            <div @click="toggle(item)" class="px-3 py-2 hover:bg-primary/50 rounded-2xl cursor-pointer text-sm m-1" :class="selected.includes(item) ? 'bg-primary rounded-2xl font-semibold' : ''">
                                <span x-text="item"></span>
                            </div>
                        </template>
                    </template>

                    <!-- Tidak ada hasil -->
                    <div
                        x-show="filteredOptions.length === 0"
                        class="px-3 py-2 text-sm text-base-content bg-base-200 border-gray-500"
                    >
                        Tidak ada hasil.
                    </div>
                </div>
            </div>

            <!-- Binding ke Livewire -->
            <input type="hidden" wire:model="riwayat_alergi_obat" x-model="selected">

            <span class="text-xs text-gray-400 mt-1">* Klik untuk pilih, klik ulang untuk hapus</span>
        </div>

        @php
            $listAlergi = [
                'DINGIN','Seledri','Debu','Amoxillin','Udang','Soba','Telur','Ikan','Buah',
                'Bawang Putih','Gandum','Jagung','Susu','Moster','Kacang','Daging Unggas',
                'Daging Merah','Beras','Wijen','Kerang','Kedelai','Sulfit','Tartrazine',
                'Kacang Pohon','Gandum','Serbuk Sari','Kucing','Anjing','Sengatan Serangga',
                'Cetakan','Parfum','Kosmetik','Getah','Air','Rangsangan Dingin','Tungau Debu Rumah',
                'Nikel','Emas','Kromium','Kobalt Klorida','Formaldehida','Pengembang Fotografi',
                'Riwaat Sinusitis','Susu Dan Telur','Toluenesulfonamide Formaldehyde',
                'Glyceryl Monothioglycolate','Paraphenylenediamine','Getah',
                'Dimethylaminopropylamine (Dmapa)','Fungisida',
            ];
        @endphp
        <!-- Riwayat Alergi Lainnya -->
        <div class="form-control" x-data="multiSelectAlergi()" x-init="init()">
            <label class="label">
                Riwayat Alergi Lainnya
            </label>
            <!-- Input Area -->
            <div class="relative ">
                <div
                    class="w-full border border-gray-300 bg-base-100 text-base-primary rounded-2xl p-1 flex flex-wrap items-center gap-2 min-h-[2.5rem] focus-within:ring-2 focus-within:ring-black transition" :class="{ 'ring-2 ring-black': open }" @click="setTimeout(() => open = true, 10)">
                    <!-- Selected tags -->
                    <template x-for="(tag, index) in selected" :key="index">
                        <span class="bg-primary text-sm rounded-full px-3 py-1 flex items-center gap-1">
                            <span x-text="tag"></span>
                            <button type="button" class="font-bold text-md" @click.stop="remove(tag)">|×</button>
                        </span>
                    </template>

                    <!-- Input for search -->
                    <input type="text" class="flex-grow min-w-[8ch] text-sm border-none rounded-xl bg-base-100" placeholder="Cari riwayat penyakit..." x-model="search" @focus="open = true" @input="open = true" />
                </div>

                <!-- Dropdown Menu -->
                <div x-show="open" @click.outside="open = false" class="absolute z-10 mt-1 w-full bg-base-200 border border-gray-500 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                    <!-- Jika ada hasil -->
                    <template x-if="filteredOptions.length > 0">
                        <template x-for="(item, index) in filteredOptions" :key="index">
                            <div
                                @click="toggle(item)" class="px-3 py-2 hover:bg-primary/50 rounded-2xl cursor-pointer  text-sm m-1" :class="selected.includes(item) ? 'bg-primary rounded-2xl font-semibold' : ''">
                                <span x-text="item"></span>
                            </div>
                        </template>
                    </template>

                    <!-- Jika tidak ada hasil -->
                    <div x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-base-content bg-base-200 border-gray-500">
                        Tidak ada hasil.
                    </div>
                </div>
            </div>

            <!-- Hidden binding untuk Livewire -->
            <input type="hidden" wire:model="riwayat_alergi_lainnya" x-model="selected">

            <span class="text-xs text-gray-400 mt-1">* Klik untuk pilih, klik ulang untuk hapus</span>
        </div>

        <!-- Obat Sedang Dikonsumsi -->
        <div class="form-control" x-data="multiSelectObat()" x-init="init()">
            <label class="label">
                Obat yang sedang dikonsumsi
            </label>
            <!-- Input Area -->
            <div
                class="relative" @click="setTimeout(() => open = true, 10)">
                <div class="w-full border border-gray-300 bg-base-100 text-base-primary rounded-2xl p-1 flex flex-wrap items-center gap-2 min-h-[2.5rem] focus-within:ring-2 focus-within:ring-black transition":class="{ 'ring-2 ring-black': open }">
                    <!-- Selected tags -->
                    <template x-for="(tag, index) in selected" :key="index">
                        <span class="bg-primary text-sm rounded-full px-3 py-1 flex items-center gap-1">
                            <span x-text="tag"></span>
                            <button type="button" class="font-bold text-md" @click.stop="remove(tag)">×</button>
                        </span>
                    </template>

                    <!-- Input search -->
                    <input type="text" class="flex-grow min-w-[8ch] text-sm border-none rounded-xl bg-base-100" placeholder="Ketik untuk cari obat..." x-model="search" @focus="open = true" @input.debounce.300ms="fetchOptions(); open = true"/>
                </div>

                <!-- Dropdown Menu -->
                <div x-show="open" @click.outside="open = false" class="absolute z-10 mt-1 w-full bg-base-200 border border-gray-500 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                    <!-- Hasil ditemukan -->
                    <template x-if="filteredOptions.length > 0">
                        <template x-for="(item, index) in filteredOptions" :key="index">
                            <div @click="toggle(item)" class="px-3 py-2 hover:bg-primary/50 rounded-2xl cursor-pointer text-sm m-1" :class="selected.includes(item) ? 'bg-primary rounded-2xl font-semibold' : ''">
                                <span x-text="item"></span>
                            </div>
                        </template>
                    </template>

                    <!-- Tidak ada hasil -->
                    <div
                        x-show="filteredOptions.length === 0"
                        class="px-3 py-2 text-sm text-base-content bg-base-200 border-gray-500"
                    >
                        Tidak ada hasil.
                    </div>
                </div>
            </div>

            <!-- Binding ke Livewire -->
            <input type="hidden" wire:model="obat_sedang_dikonsumsi" x-model="selected">

            <span class="text-xs text-gray-400 mt-1">* Klik untuk pilih, klik ulang untuk hapus</span>
        </div>

    </div>
</div>
@push('scripts')
{{-- RIWAYAT PENYAKIT --}}
<script>
    function multiSelect() {
        return {
            open: false,
            selected: @entangle('data_kesehatan.riwayat_penyakit'),
            search: '',
            options: [],
            loading: false,
            lastQuery: '',

            async fetchOptions() {
                if (this.search.length < 2) {
                    this.options = [];
                    return;
                }

                if (this.search === this.lastQuery) return;
                this.lastQuery = this.search;

                this.loading = true;

                try {
                    const res = await fetch(`/ajax/icd_10?q=${encodeURIComponent(this.search)}`);
                    const data = await res.json();

                    this.options = data.map(i => ({
                        label: `${i.code} - ${i.name_id}`,
                        name: i.name_id
                    }));

                } finally {
                    this.loading = false;
                }
            },

            get filteredOptions() {
                return this.options.filter(i => !this.selected.includes(i.name));
            },

            init() {
                if (!Array.isArray(this.selected)) {
                    this.selected = [];
                }
            },

            toggle(item) {
                if (!this.selected.includes(item.name)) {
                    this.selected.push(item.name);
                } else {
                    this.selected = this.selected.filter(i => i !== item.name);
                }

                this.search = '';
                this.options = [];
            },

            remove(item) {
                this.selected = this.selected.filter(i => i !== item);
            }
        }
    }
</script>

{{-- RIWAYAT ALERGI LAINNYA --}}
<script>
    function multiSelectAlergi() {
        return {
            open: false,
            selected: @entangle('data_kesehatan.riwayat_alergi_lainnya'),
            options: @js($listAlergi),
            search: '',

            get filteredOptions() {
                if (this.search === '') return this.options;
                return this.options.filter(item =>
                    item.toLowerCase().includes(this.search.toLowerCase())
                );
            },

            init() {
                if (!Array.isArray(this.selected)) {
                    this.selected = [];
                }
            },

            toggle(item) {
                const index = this.selected.indexOf(item);
                if (index === -1) {
                    this.selected.push(item);
                } else {
                    this.selected.splice(index, 1);
                }
                this.search = ''; // reset setelah pilih
            },

            remove(item) {
                const index = this.selected.indexOf(item);
                if (index !== -1) {
                    this.selected.splice(index, 1);
                }
            }
        }
    }
</script>

{{-- LIST OBAT --}}
<script>
    function multiSelectObat() {
        return {
            open: false,
            selected: @entangle('data_kesehatan.obat_sedang_dikonsumsi'),
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
                    .then(response => response.json())
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
                this.search = '';
                this.filteredOptions = [];
            },

            remove(item) {
                this.selected = this.selected.filter(i => i !== item);
            }
        }
    }
</script>

{{-- Alergi OBAT --}}
<script>
    function multiSelectAlergiObat() {
        return {
            open: false,
            selected: @entangle('data_kesehatan.riwayat_alergi_obat'),
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
                    .then(response => response.json())
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
                this.search = '';
                this.filteredOptions = [];
            },

            remove(item) {
                this.selected = this.selected.filter(i => i !== item);
            }
        }
    }
</script>

@endpush