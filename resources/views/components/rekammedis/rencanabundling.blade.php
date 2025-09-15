<div class="bg-base-200 p-4 rounded border border-base-200"
    x-data="bundlingForm()"
>
    @props([
        'rencanaBundling' => [
            'bundling_id' => [],
            'jumlah_bundling' => [],
            'potongan' => [],
            'diskon' => [],
            'subtotal' => [],
        ],
        'layanandanbundling' => [
            'treatment' => [],
            'bundling' => [],
        ]
    ])

    <div class="divider">Paket Bundling</div>

    <!-- Tombol tambah -->
    <div class="mb-6">
        <button type="button"
            class="btn btn-primary btn-sm"
            @click="addBundling">
            + Tambah Bundling
        </button>
    </div>

    <!-- List Bundling -->
    <div class="space-y-4">
        <template x-for="(item, index) in bundlingItems" :key="index">
            <div class="p-4 border rounded-lg bg-base-100 space-y-3">

                <!-- Baris 1: Bundling + Jumlah -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Bundling</label>
                        <select class="select select-bordered w-full"
                            :name="`rencanaBundling[bundling_id][${index}]`"
                            x-model="item.bundling_id"
                            @change="syncItemBundling(index)"
                        >
                            <option value="">-- Pilih Bundling --</option>
                            @foreach($layanandanbundling['bundling'] as $bundle)
                                <option value="{{ $bundle['id'] }}">
                                    {{ $bundle['nama'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Jumlah</label>
                        <input type="number" min="1"
                            class="input input-bordered w-full"
                            x-model.number="item.jumlah_bundling"
                            @input="syncItemBundling(index)"
                        >
                    </div>
                </div>

                <!-- Baris 2: Harga Asli + Potongan + Diskon + Subtotal -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                    <!-- Harga Asli -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Harga Asli</label>
                        <input type="text"
                            class="input input-bordered w-full bg-base-200"
                            :value="formatCurrency(calcHargaAsli(item))"
                            readonly
                        >
                    </div>

                    <!-- Potongan Harga -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Potongan (Rp)</label>
                        <input type="text"
                            class="input input-bordered w-full"
                            :value="formatCurrency(item.potongan)"
                            @input="e => updatePotongan(index, e.target.value)"
                        >
                    </div>

                    <!-- Diskon -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Diskon</label>
                        <div class="flex items-center">
                            <input type="number" min="0" max="100"
                                class="input input-bordered w-full"
                                x-model.number="item.diskon"
                                @input="updateDiskon(index, item.diskon)"
                            >
                            <span class="ml-2">%</span>
                        </div>
                    </div>

                    <!-- Subtotal -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Subtotal</label>
                        <input type="text"
                            class="input input-bordered w-full bg-base-200"
                            :value="formatCurrency(calcSubtotal(item))"
                            readonly
                        >
                    </div>
                </div>

                {{-- Detail isi bundling --}}
                <template x-if="item.bundling_id">
                    <div class="ml-4 mb-4 p-3 border rounded bg-base-200">
                        @foreach($layanandanbundling['bundling'] as $bundle)
                            <template x-if="item.bundling_id == '{{ $bundle['id'] }}'">
                                <div>
                                    <p class="font-semibold">{{ $bundle['nama'] }}</p>

                                    {{-- Treatment --}}
                                    <p class="mt-2 text-sm font-medium">Treatments:</p>
                                    @if(!empty($bundle['treatmentBundlings']))
                                        <ul class="space-y-2">
                                            @foreach($bundle['treatmentBundlings'] as $tb)
                                                <li class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-start border rounded p-2">
                                                    <span class="text-sm">{{ $tb['treatment']['nama_treatment'] ?? '-' }}</span>

                                                    <!-- Jumlah Awal -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Jumlah Tersedia</label>
                                                        <input type="number"
                                                            class="input input-bordered w-full bg-base-200"
                                                            :value="item.jumlah_bundling * {{ $tb->jumlah ?? 0 }}"
                                                            readonly
                                                        >
                                                    </div>

                                                    <!-- Dipakai -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Dipakai</label>
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" class="btn btn-xs btn-outline"
                                                                @click="item.treatmentsDipakai['{{ $tb['id'] }}'] = Math.max((item.treatmentsDipakai['{{ $tb['id'] }}'] ?? 0) - 1, 0)">
                                                                -
                                                            </button>
                                                            <input type="number"
                                                                class="input input-bordered w-16 text-center"
                                                                x-model.number="item.treatmentsDipakai['{{ $tb['id'] }}']"
                                                                readonly
                                                            >
                                                            <button type="button" class="btn btn-xs btn-outline"
                                                                @click="item.treatmentsDipakai['{{ $tb['id'] }}'] = Math.min((item.treatmentsDipakai['{{ $tb['id'] }}'] ?? 0) + 1, (item.jumlah_bundling * {{ $tb->jumlah ?? 0 }}))">
                                                                +
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Sisa -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Sisa</label>
                                                        <input type="number"
                                                            class="input input-bordered w-full bg-base-200"
                                                            :value="(item.jumlah_bundling * {{ $tb->jumlah ?? 0 }}) - (item.treatmentsDipakai['{{ $tb['id'] }}'] ?? 0)"
                                                            readonly
                                                        >
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-sm">Tidak Tersedia</p>
                                    @endif

                                    {{-- Pelayanan --}}
                                    <p class="mt-2 text-sm font-medium">Pelayanan:</p>
                                    @if(!empty($bundle['pelayananBundlings']))
                                        <ul class="space-y-2">
                                            @foreach($bundle['pelayananBundlings'] as $pb)
                                                <li class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-start border rounded p-2">
                                                    <span class="text-sm">{{ $pb['pelayanan']['nama_pelayanan'] ?? '-' }}</span>

                                                    <!-- Jumlah Awal -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Jumlah Tersedia</label>
                                                        <input type="number"
                                                            class="input input-bordered w-full bg-base-200"
                                                            :value="item.jumlah_bundling * {{ $pb->jumlah ?? 0 }}"
                                                            readonly
                                                        >
                                                    </div>

                                                    <!-- Dipakai -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Dipakai</label>
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" class="btn btn-xs btn-outline"
                                                                @click="item.pelayananDipakai['{{ $pb['id'] }}'] = Math.max((item.pelayananDipakai['{{ $pb['id'] }}'] ?? 0) - 1, 0)">
                                                                -
                                                            </button>
                                                            <input type="number"
                                                                class="input input-bordered w-16 text-center"
                                                                x-model.number="item.pelayananDipakai['{{ $pb['id'] }}']"
                                                                readonly
                                                            >
                                                            <button type="button" class="btn btn-xs btn-outline"
                                                                @click="item.pelayananDipakai['{{ $pb['id'] }}'] = Math.min((item.pelayananDipakai['{{ $pb['id'] }}'] ?? 0) + 1, (item.jumlah_bundling * {{ $pb->jumlah ?? 0 }}))">
                                                                +
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Sisa -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Sisa</label>
                                                        <input type="number"
                                                            class="input input-bordered w-full bg-base-200"
                                                            :value="(item.jumlah_bundling * {{ $pb->jumlah ?? 0 }}) - (item.pelayananDipakai['{{ $pb['id'] }}'] ?? 0)"
                                                            readonly
                                                        >
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-sm">Tidak Tersedia</p>
                                    @endif

                                    {{-- Produk / Obat --}}
                                    <p class="mt-2 text-sm font-medium">Produk / Obat:</p>
                                    @if(!empty($bundle['produkBundlings']))
                                        <ul class="space-y-2">
                                            @foreach($bundle['produkBundlings'] as $prb)
                                                <li class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-start border rounded p-2">
                                                    <span class="text-sm">
                                                        {{ $prb['produk']['nama_produk'] ?? $prb['produk']['nama_obat'] ?? '-' }}
                                                    </span>

                                                    <!-- Jumlah Awal -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Jumlah Tersedia</label>
                                                        <input type="number"
                                                            class="input input-bordered w-full bg-base-200"
                                                            :value="item.jumlah_bundling * {{ $prb->jumlah ?? 0 }}"
                                                            readonly
                                                        >
                                                    </div>

                                                    <!-- Dipakai -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Dipakai</label>
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" class="btn btn-xs btn-outline"
                                                                @click="item.productsDipakai['{{ $prb['id'] }}'] = Math.max((item.productsDipakai['{{ $prb['id'] }}'] || 0) - 1, 0)">
                                                                -
                                                            </button>
                                                            <input type="number"
                                                                class="input input-bordered w-16 text-center"
                                                                x-model.number="item.productsDipakai['{{ $prb['id'] }}']"
                                                                readonly
                                                            >
                                                            <button type="button" class="btn btn-xs btn-outline"
                                                                @click="item.productsDipakai['{{ $prb['id'] }}'] = Math.min((item.productsDipakai['{{ $prb['id'] }}'] || 0) + 1, (item.jumlah_bundling * {{ $prb->jumlah ?? 0 }}))">
                                                                +
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Sisa -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Sisa</label>
                                                        <input type="number"
                                                            class="input input-bordered w-full bg-base-200"
                                                            :value="(item.jumlah_bundling * {{ $prb->jumlah ?? 0 }}) - (item.productsDipakai['{{ $prb['id'] }}'] || 0)"
                                                            readonly
                                                        >
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-sm">Tidak Tersedia</p>
                                    @endif
                                </div>
                            </template>
                        @endforeach
                    </div>
                </template>

                <!-- Tombol Hapus -->
                <div class="flex justify-end">
                    <button type="button"
                        class="btn btn-error btn-sm"
                        @click="removeBundling(index)"
                        x-show="bundlingItems.length > 1">
                        Hapus
                    </button>
                </div>
            </div>
        </template>

        <!-- Footer Total -->
        <div class="flex justify-end mt-4 font-bold text-lg">
            <div class="mr-4">Total:</div>
            <div x-text="formatCurrency(calcTotal())"></div>
        </div>
    </div>
</div>


<script>
    function bundlingForm() {
        return {
            // state
            bundlingItems: [{
                bundling_id: '',
                jumlah_bundling: 1,
                potongan: 0,
                diskon: 0,
                subtotal: 0,
                treatmentsDipakai: {},
                pelayananDipakai: {},
                produkDipakai: {}
            }],
            bundlings: @json($layanandanbundling['bundling']),

            // helpers
            getBundling(id) {
                return this.bundlings.find(b => b.id == id);
            },

            formatCurrency(value) {
                if (!value) return 'Rp 0';
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(value);
            },

            // update potongan
            updatePotongan(i, val) {
                let number = parseInt(val.replace(/[^\d]/g, '')) || 0;
                this.bundlingItems[i].potongan = number;
                this.syncItemBundling(i);
            },

            // update diskon
            updateDiskon(i, val) {
                let number = parseInt(val) || 0;
                if (number < 0) number = 0;
                if (number > 100) number = 100;
                this.bundlingItems[i].diskon = number;
                this.syncItemBundling(i);
            },

            // kalkulasi
            calcHargaAsli(item) {
                let bundling = this.getBundling(item.bundling_id);
                let harga = bundling ? Number(bundling.harga) : 0;
                let jumlah = Number(item.jumlah_bundling) || 1;
                return harga * jumlah;
            },

            calcSubtotal(item) {
                let hargaAsli = this.calcHargaAsli(item);
                let potongan = Number(item.potongan) || 0;
                let diskon = Number(item.diskon) || 0;
                let afterPotongan = hargaAsli - potongan;
                if (afterPotongan < 0) afterPotongan = 0;
                return afterPotongan - (afterPotongan * (diskon / 100));
            },

            calcTotal() {
                return this.bundlingItems.reduce((total, item) => total + this.calcSubtotal(item), 0);
            },

            // aksi
            addBundling() {
                this.bundlingItems.push({
                    bundling_id: '',
                    jumlah_bundling: 1,
                    potongan: 0,
                    diskon: 0,
                    subtotal: 0,
                    treatmentsDipakai: {},
                    pelayananDipakai: {},
                    produkDipakai: {}
                });
                this.syncBundlingToLivewire();
            },

            removeBundling(index) {
                this.bundlingItems.splice(index, 1);
                this.reindexBundling();
                this.syncBundlingToLivewire();
                this.cleanupBundlingLivewire();
            },

            reindexBundling() {
                this.bundlingItems = this.bundlingItems.map(item => ({ ...item }));
            },

            syncItemBundling(i) {
                let item = this.bundlingItems[i];
                let subtotal = this.calcSubtotal(item);
                item.subtotal = subtotal;

                @this.set(`rencana_bundling.bundling_id.${i}`, item.bundling_id);
                @this.set(`rencana_bundling.jumlah_bundling.${i}`, item.jumlah_bundling);
                @this.set(`rencana_bundling.potongan.${i}`, item.potongan);
                @this.set(`rencana_bundling.diskon.${i}`, item.diskon);
                @this.set(`rencana_bundling.subtotal.${i}`, item.subtotal);
                    // sync detail isi bundling
                @this.set(`rencana_bundling.treatments.${i}`, item.treatmentsDipakai);
                @this.set(`rencana_bundling.pelayanans.${i}`, item.pelayananDipakai);
                @this.set(`rencana_bundling.produks.${i}`, item.produkDipakai);
            },

            syncBundlingToLivewire() {
                this.bundlingItems.forEach((item, i) => this.syncItemBundling(i));
            },

            cleanupBundlingLivewire() {
                let length = this.bundlingItems.length;
                for (let i = length; i < 100; i++) {
                    @this.set(`rencana_bundling.bundling_id.${i}`, null);
                    @this.set(`rencana_bundling.jumlah_bundling.${i}`, null);
                    @this.set(`rencana_bundling.potongan.${i}`, null);
                    @this.set(`rencana_bundling.diskon.${i}`, null);
                    @this.set(`rencana_bundling.subtotal.${i}`, null);
                }
            }
        }
    }
</script>
