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
        
        @php
            $listPenyakit = [
                'Alergi Panadol','Anemia','Appendicitis','Arthritis','Asma',
                'Asma (Terkontrol)','Asam Urat','Ayan','Batuk','Batuk, Gatal Tenggorok','Batuk, Pilek',
                'Batu Ginjal','Batu Ginjal Tahun 2020','Cancer Payudara Post Operasi Pengangkatan 1 Thn',
                'Cacar Air','Caries Molar 3','Cemas','Cervicalgia','Cholesterol','Alergi Amoxilin','Covid',
                'Diabetes Melitus','Diabetes Melitus Type 2','Diabetes','Diare','Dislipidemia','Dm',
                'Dyspepsia','Dyspesia','Flu','Gangangguan Pencernaan (Maag)','Gatak','Genitic','Glukosa',
                'Hemoroid','Hepatitis','Hiperkolesterolemia','Hipertensi','Hypertensi','Hypertention',
                'Hypotensi','Isk','Ischaemia Heart Disease','Jantung','Kanker','Kanker Usus Besar',
                'Keloid','Kencing Manis','Kista Indung Telur','Kolesterol','Maag,','Meriang','Migrain',
                'Neuritis','Nefrolithiasis','Obesitas','Operasi 1X Di Rs','Osteoarthritis',
                'Oesteoartritis','Parkinson','Pemasangan Ring Jantung','Pembengkakan Jantung',
                'Pjk','Post Amputasi','Post Debridement','Post Operasi Acl','Pusing Baru',
                'Readang Paru','Rematoid Artritis','Rhinitis','Sakit Gigi Terus Menerus Akibat Tidak Gosok Gigi',
                'Sakit Maag','Sakit Ulu Hati','Sinusitis','Skizofrenia','Stroke','Suspect Virus',
                'Tbc Kelenjar Tahun 2009 Dan 2017','Tb Paru','Talasemia','Thalasemia','Tifoid',
                'Tifus','Tinea','Tipes','Tonsilitis','Tuberculosis','Vertigo',
            ];
        @endphp
        <!-- Riwayat Penyakit -->
        <div class="form-control" x-data="multiSelect()" x-init="init()">
            <label class="label">
                Riwayat Penyakit
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
            <input type="hidden" wire:model="riwayat_penyakit" x-model="selected">

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
            options: @js($listPenyakit),
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