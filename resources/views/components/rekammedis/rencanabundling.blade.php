<div class="bg-base-200 p-4 rounded border border-base-200"
    x-data="bundlingForm()"
        x-init="
        $watch('bundlingItems', () => {
            const total = calcTotal();
            window.dispatchEvent(
                new CustomEvent('total-bundling-updated', { detail: total })
            );
        })
    "
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
                            @change="onChangeBundling(index)"
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
                            @input="onChangeJumlahBundling(index)"
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
                                    @if(isset($bundle['treatmentBundlings']) && count($bundle['treatmentBundlings']) > 0)
                                        <ul class="space-y-2">
                                            @foreach($bundle['treatmentBundlings'] as $tb)
                                                <li class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-start border rounded p-2">
                                                    <span class="text-sm">{{ $tb['treatment']['nama_treatment'] ?? '-' }}</span>

                                                    <!-- Jumlah Awal -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Jumlah Tersedia</label>
                                                        <input type="number"
                                                            class="input input-bordered w-full bg-base-200"
                                                            :value="(item.jumlah_bundling * {{ $tb->jumlah ?? 0 }})"
                                                            readonly>
                                                    </div>

                                                    <!-- Dipakai -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Dipakai</label>
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" class="btn btn-xs btn-error"
                                                                @click="
                                                                    initDetailIfMissing(item, 'treatments', '{{ $tb['id'] }}', {
                                                                        idKey: 'treatments_id',
                                                                        idValue: {{ $tb['treatment']['id'] }},
                                                                        perBundle: {{ $tb->jumlah ?? 0 }}
                                                                    });
                                                                    decrementDetail(item, 'treatments', '{{ $tb['id'] }}', index);
                                                                ">
                                                                -
                                                            </button>

                                                            <input type="number"
                                                                class="input input-bordered w-16 text-center"
                                                                :value="item.details.treatments['{{ $tb['id'] }}'] ? item.details.treatments['{{ $tb['id'] }}'].jumlah_terpakai : 0"
                                                                readonly>

                                                            <button type="button" class="btn btn-xs btn-success"
                                                                @click="
                                                                    initDetailIfMissing(item, 'treatments', '{{ $tb['id'] }}', {
                                                                        idKey: 'treatments_id',
                                                                        idValue: {{ $tb['treatment']['id'] }},
                                                                        perBundle: {{ $tb->jumlah ?? 0 }}
                                                                    });
                                                                    incrementDetail(item, 'treatments', '{{ $tb['id'] }}', index);
                                                                ">
                                                                +
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Sisa -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Sisa</label>
                                                        <input type="number"
                                                            class="input input-bordered w-full bg-base-200"
                                                            :value="(item.jumlah_bundling * {{ $tb->jumlah ?? 0 }}) - (item.details.treatments['{{ $tb['id'] }}'] ? item.details.treatments['{{ $tb['id'] }}'].jumlah_terpakai : 0)"
                                                            readonly>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-sm">Tidak Tersedia</p>
                                    @endif

                                    {{-- Pelayanan --}}
                                    <p class="mt-2 text-sm font-medium">Pelayanan:</p>
                                    @if(isset($bundle['pelayananBundlings']) && count($bundle['pelayananBundlings']) > 0)
                                        <ul class="space-y-2">
                                            @foreach($bundle['pelayananBundlings'] as $pb)
                                                <li class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-start border rounded p-2">
                                                    <span class="text-sm">{{ $pb['pelayanan']['nama_pelayanan'] ?? '-' }}</span>

                                                    <!-- Jumlah Awal -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Jumlah Tersedia</label>
                                                        <input type="number"
                                                            class="input input-bordered w-full bg-base-200"
                                                            :value="(item.jumlah_bundling * {{ $pb->jumlah ?? 0 }})"
                                                            readonly>
                                                    </div>

                                                    <!-- Dipakai -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Dipakai</label>
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" class="btn btn-xs btn-error"
                                                                @click="
                                                                    initDetailIfMissing(item, 'pelayanans', '{{ $pb['id'] }}', {
                                                                        idKey: 'pelayanan_id',
                                                                        idValue: {{ $pb['pelayanan_id'] }},
                                                                        perBundle: {{ $pb->jumlah ?? 0 }}
                                                                    });
                                                                    decrementDetail(item, 'pelayanans', '{{ $pb['id'] }}', index);
                                                                ">
                                                                -
                                                            </button>

                                                            <input type="number"
                                                                class="input input-bordered w-16 text-center"
                                                                :value="item.details.pelayanans['{{ $pb['id'] }}'] ? item.details.pelayanans['{{ $pb['id'] }}'].jumlah_terpakai : 0"
                                                                readonly>

                                                            <button type="button" class="btn btn-xs btn-success"
                                                                @click="
                                                                    initDetailIfMissing(item, 'pelayanans', '{{ $pb['id'] }}', {
                                                                        idKey: 'pelayanan_id',
                                                                        idValue: {{ $pb['pelayanan_id'] }},
                                                                        perBundle: {{ $pb->jumlah ?? 0 }}
                                                                    });
                                                                    incrementDetail(item, 'pelayanans', '{{ $pb['id'] }}', index);
                                                                ">
                                                                +
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Sisa -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Sisa</label>
                                                        <input type="number"
                                                            class="input input-bordered w-full bg-base-200"
                                                            :value="(item.jumlah_bundling * {{ $pb->jumlah ?? 0 }}) - (item.details.pelayanans['{{ $pb['id'] }}'] ? item.details.pelayanans['{{ $pb['id'] }}'].jumlah_terpakai : 0)"
                                                            readonly>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-sm">Tidak Tersedia</p>
                                    @endif

                                    {{-- Produk / Obat --}}
                                    <p class="mt-2 text-sm font-medium">Produk / Obat:</p>
                                    @if(isset($bundle['produkObatBundlings']) && count($bundle['produkObatBundlings']) > 0)
                                        <ul class="space-y-2">
                                            @foreach($bundle['produkObatBundlings'] as $prb)
                                                <li class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-start border rounded p-2">
                                                    <span class="text-sm">
                                                        {{ $prb['produk']['nama_dagang'] ?? '-' }}
                                                    </span>

                                                    <!-- Jumlah Awal -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Jumlah Tersedia</label>
                                                        <input type="number"
                                                            class="input input-bordered w-full bg-base-200"
                                                            :value="(item.jumlah_bundling * {{ $prb->jumlah ?? 0 }})"
                                                            readonly>
                                                    </div>

                                                    <!-- Dipakai -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Dipakai</label>
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" class="btn btn-xs btn-error"
                                                                @click="
                                                                    initDetailIfMissing(item, 'produks', '{{ $prb['id'] }}', {
                                                                        idKey: 'produk_obat_id',
                                                                        idValue: {{ $prb['produk_id'] }},
                                                                        perBundle: {{ $prb->jumlah ?? 0 }}
                                                                    });
                                                                    decrementDetail(item, 'produks', '{{ $prb['id'] }}', index);
                                                                ">
                                                                -
                                                            </button>

                                                            <input type="number"
                                                                class="input input-bordered w-16 text-center"
                                                                :value="item.details.produks['{{ $prb['id'] }}'] ? item.details.produks['{{ $prb['id'] }}'].jumlah_terpakai : 0"
                                                                readonly>

                                                            <button type="button" class="btn btn-xs btn-success"
                                                                @click="
                                                                    initDetailIfMissing(item, 'produks', '{{ $prb['id'] }}', {
                                                                        idKey: 'produk_obat_id',
                                                                        idValue: {{ $prb['produk_id'] }},
                                                                        perBundle: {{ $prb->jumlah ?? 0 }}
                                                                    });
                                                                    incrementDetail(item, 'produks', '{{ $prb['id'] }}', index);
                                                                ">
                                                                +
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Sisa -->
                                                    <div>
                                                        <label class="block text-xs mb-1">Sisa</label>
                                                        <input type="number"
                                                            class="input input-bordered w-full bg-base-200"
                                                            :value="(item.jumlah_bundling * {{ $prb->jumlah ?? 0 }}) - (item.details.produks['{{ $prb['id'] }}'] ? item.details.produks['{{ $prb['id'] }}'].jumlah_terpakai : 0)"
                                                            readonly>
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
                // unified details container (per bundling row)
                details: {
                    treatments: {},   // keyed by treatmentBundling id
                    pelayanans: {},   // keyed by pelayananBundling id
                    produks: {}       // keyed by produkBundling id
                }
            }],

            // server-provided list of bundlings (contains nested treatment/pelayanan/produk info)
            bundlings: @json($layanandanbundling['bundling']),

            // helpers
            getBundling(id) {
                return this.bundlings.find(b => String(b.id) === String(id));
            },

            formatCurrency(value) {
                if (value === null || value === undefined) return 'Rp 0';
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(Number(value));
            },

            // update potongan
            updatePotongan(i, val) {
                let number = parseInt((val||'').toString().replace(/[^\d]/g, '')) || 0;
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

            // calc harga
            calcHargaAsli(item) {
                let bundling = this.getBundling(item.bundling_id);
                let harga = bundling ? Number(bundling.harga || 0) : 0;
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

            // action helpers for details
            initDetailIfMissing(item, type, key, opts) {
                // type: 'treatments' | 'pelayanans' | 'produks'
                // key: the bundling-detail id (string)
                // opts: { idKey: 'treatment_id'|'pelayanan_id'|'produk_obat_id', idValue, perBundle }
                if (!item.details[type]) item.details[type] = {};
                if (!item.details[type][key]) {
                    item.details[type][key] = {};
                    item.details[type][key][opts.idKey] = opts.idValue;
                    item.details[type][key].jumlah_per_bundle = Number(opts.perBundle || 0);
                    item.details[type][key].jumlah_awal = (Number(item.jumlah_bundling) || 0) * Number(opts.perBundle || 0);
                    item.details[type][key].jumlah_terpakai = 0;
                } else {
                    // refresh jumlah_awal in case jumlah_bundling has changed
                    item.details[type][key].jumlah_per_bundle = Number(opts.perBundle || 0);
                    item.details[type][key].jumlah_awal = (Number(item.jumlah_bundling) || 0) * Number(opts.perBundle || 0);
                    if (!('jumlah_terpakai' in item.details[type][key])) item.details[type][key].jumlah_terpakai = 0;
                }
            },

            incrementDetail(item, type, key, index) {
                let detail = item.details[type][key];
                if (!detail) return;
                let maxVal = (Number(item.jumlah_bundling) || 0) * (Number(detail.jumlah_per_bundle) || 0);
                detail.jumlah_terpakai = Math.min((detail.jumlah_terpakai || 0) + 1, maxVal);
                // sync after change
                this.syncItemBundling(index);
            },

            decrementDetail(item, type, key, index) {
                let detail = item.details[type][key];
                if (!detail) return;
                detail.jumlah_terpakai = Math.max((detail.jumlah_terpakai || 0) - 1, 0);
                // sync after change
                this.syncItemBundling(index);
            },

            // when user selects bundling, we want to reset details and optionally prefill jumlah_awal if needed
            onChangeBundling(index) {
                // keep existing counts? currently we'll reset detail container to empty (but header values persist)
                let item = this.bundlingItems[index];
                item.details = { treatments: {}, pelayanans: {}, produks: {} };
                // sync header and details to Livewire
                this.syncItemBundling(index);
            },

            onChangeJumlahBundling(index) {
                // Recalculate jumlah_awal for any existing details of this bundling
                let item = this.bundlingItems[index];
                Object.keys(item.details.treatments || {}).forEach(k => {
                    let d = item.details.treatments[k];
                    if (d && 'jumlah_per_bundle' in d) {
                        d.jumlah_awal = (Number(item.jumlah_bundling) || 0) * Number(d.jumlah_per_bundle || 0);
                        // ensure terpakai <= awal
                        if ((d.jumlah_terpakai || 0) > d.jumlah_awal) d.jumlah_terpakai = d.jumlah_awal;
                    }
                });
                Object.keys(item.details.pelayanans || {}).forEach(k => {
                    let d = item.details.pelayanans[k];
                    if (d && 'jumlah_per_bundle' in d) {
                        d.jumlah_awal = (Number(item.jumlah_bundling) || 0) * Number(d.jumlah_per_bundle || 0);
                        if ((d.jumlah_terpakai || 0) > d.jumlah_awal) d.jumlah_terpakai = d.jumlah_awal;
                    }
                });
                Object.keys(item.details.produks || {}).forEach(k => {
                    let d = item.details.produks[k];
                    if (d && 'jumlah_per_bundle' in d) {
                        d.jumlah_awal = (Number(item.jumlah_bundling) || 0) * Number(d.jumlah_per_bundle || 0);
                        if ((d.jumlah_terpakai || 0) > d.jumlah_awal) d.jumlah_terpakai = d.jumlah_awal;
                    }
                });

                // then sync
                this.syncItemBundling(index);
            },

            // aksi add/remove bundling
            addBundling() {
                this.bundlingItems.push({
                    bundling_id: '',
                    jumlah_bundling: 1,
                    potongan: 0,
                    diskon: 0,
                    subtotal: 0,
                    details: {
                        treatments: {},
                        pelayanans: {},
                        produks: {}
                    }
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

            // sync a single item (header + details) to Livewire
            syncItemBundling(i) {
                let item = this.bundlingItems[i];
                let subtotal = this.calcSubtotal(item);
                item.subtotal = subtotal;

                // ensure details jumlah_awal kept up-to-date (in case header changed)
                // treatments
                Object.keys(item.details.treatments || {}).forEach(k => {
                    let d = item.details.treatments[k];
                    if (d) {
                        d.jumlah_awal = (Number(item.jumlah_bundling) || 0) * (Number(d.jumlah_per_bundle) || 0);
                        if ((d.jumlah_terpakai || 0) > d.jumlah_awal) d.jumlah_terpakai = d.jumlah_awal;
                    }
                });
                // pelayanans
                Object.keys(item.details.pelayanans || {}).forEach(k => {
                    let d = item.details.pelayanans[k];
                    if (d) {
                        d.jumlah_awal = (Number(item.jumlah_bundling) || 0) * (Number(d.jumlah_per_bundle) || 0);
                        if ((d.jumlah_terpakai || 0) > d.jumlah_awal) d.jumlah_terpakai = d.jumlah_awal;
                    }
                });
                // produks
                Object.keys(item.details.produks || {}).forEach(k => {
                    let d = item.details.produks[k];
                    if (d) {
                        d.jumlah_awal = (Number(item.jumlah_bundling) || 0) * (Number(d.jumlah_per_bundle) || 0);
                        if ((d.jumlah_terpakai || 0) > d.jumlah_awal) d.jumlah_terpakai = d.jumlah_awal;
                    }
                });

                // sync header
                @this.set(`rencana_bundling.bundling_id.${i}`, item.bundling_id);
                @this.set(`rencana_bundling.jumlah_bundling.${i}`, item.jumlah_bundling);
                @this.set(`rencana_bundling.potongan.${i}`, item.potongan);
                @this.set(`rencana_bundling.diskon.${i}`, item.diskon);
                @this.set(`rencana_bundling.subtotal.${i}`, item.subtotal);

                // sync details into nested details.* arrays on Livewire
                @this.set(`rencana_bundling.details.treatments.${i}`, item.details.treatments);
                @this.set(`rencana_bundling.details.pelayanans.${i}`, item.details.pelayanans);
                @this.set(`rencana_bundling.details.produks.${i}`, item.details.produks);
            },

            // full sync for all items
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
                    @this.set(`rencana_bundling.details.treatments.${i}`, {});
                    @this.set(`rencana_bundling.details.pelayanans.${i}`, {});
                    @this.set(`rencana_bundling.details.produks.${i}`, {});
                }
            }
        }
    }
</script>
