@props([
    'rencanaLayanan' => [
        'pelayanan_id'     => [],
        'jumlah_pelayanan' => [],
    ],
    'rencanaLayananLabels' => [],
    'layanandanbundling' => [
        'layanan' => [],
        'bundling' => [],
    ]
])

<div class="bg-base-200 p-4 rounded border border-base-200"
    x-data="layananForm(
        @js($rencanaLayanan['pelayanan_id']     ?? []),
        @js($rencanaLayanan['jumlah_pelayanan'] ?? []),
        @js($rencanaLayananLabels               ?? [])
    )"
    x-init="init()"
>
    <div class="divider">Rencana Tindakan Medis</div>

    <div class="mb-6">
        <div class="flex items-center mb-2">
            <button type="button" class="btn btn-primary btn-sm mx-1" @click="addLayanan">
                + Tambah Layanan
            </button>
        </div>

        <div class="mb-4 border p-4 rounded-lg bg-base-100">
            <div class="flex font-semibold mb-2">
                <div class="flex-1">Pilih Layanan</div>
                <div class="w-20"></div>
            </div>

            <template x-for="(item, index) in layananItems" :key="item.uid">
                <div class="flex flex-col gap-1 mb-4">

                    {{-- Child scope hanya untuk search, tombol hapus di luar --}}
                    <div class="relative"
                        x-data="searchLayananChild(
                            () => item.search_label,
                            (l) => {
                                item.pelayanan_id = l.id;
                                item.search_label = l.nama;
                                syncItemLayanan(index);
                            }
                        )"
                        x-init="init()"
                    >
                        <input type="text"
                            class="input input-bordered w-full"
                            placeholder="Cari Layanan..."
                            :value="search"
                            @input.debounce.300ms="item.search_label = ''; search = $event.target.value; searchLayanan()"
                        >

                        <input type="hidden"
                            :name="`rencanaLayanan[pelayanan_id][${index}]`"
                            x-model="item.pelayanan_id"
                        >

                        <div class="absolute left-0 right-0 top-full mt-1 z-50
                                bg-white border rounded w-full max-h-48 overflow-y-auto shadow-lg"
                            x-show="show && results.length"
                            @click.outside="show = false">
                            <template x-for="l in results" :key="l.id">
                                <div class="px-3 py-2 hover:bg-gray-200 cursor-pointer"
                                    @click="selectLayanan(l)">
                                    <span x-text="l.nama"></span>
                                </div>
                            </template>
                        </div>

                        <input type="number" min="1"
                            class="input input-bordered w-32 hidden"
                            x-model.number="item.jumlah_pelayanan"
                            @input="syncItemLayanan(index)"
                        >
                    </div>

                    {{-- ✅ Tombol hapus di luar child scope agar bisa akses removeLayanan --}}
                    <button type="button"
                        class="btn btn-error btn-sm w-20 mt-1"
                        @click="removeLayanan(index)"
                        x-show="layananItems.length > 1">
                        Hapus
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function layananForm(
        existingIds    = [],
        existingJumlah = [],
        existingLabels = []
    ) {
        return {
            layananItems: existingIds.length > 0
                ? existingIds.map((id, i) => ({
                    uid:              Date.now() + i,
                    pelayanan_id:     id,
                    jumlah_pelayanan: existingJumlah[i] ?? 1,
                    search_label:     existingLabels[i] ?? '',
                }))
                : [{ uid: Date.now(), pelayanan_id: '', jumlah_pelayanan: 1, search_label: '' }],

            init() {
                // ✅ Listener untuk sync sebelum submit
                window.addEventListener('before-submit', () => {
                    this.fullSyncToLivewire();
                });
            },

            addLayanan() {
                this.layananItems.push({
                    uid:              Date.now() + Math.random(),
                    pelayanan_id:     '',
                    jumlah_pelayanan: 1,
                    search_label:     '',
                });
                this.fullSyncToLivewire();
            },

            removeLayanan(index) {
                this.layananItems.splice(index, 1);
                this.fullSyncToLivewire();
            },

            syncItemLayanan(i) {
                this.fullSyncToLivewire();
            },

            fullSyncToLivewire() {
                const ids    = this.layananItems.map(item => item.pelayanan_id);
                const jumlah = this.layananItems.map(item => item.jumlah_pelayanan);

                {{-- ✅ @this tersedia karena ini file blade --}}
                @this.set('rencana_layanan', {
                    pelayanan_id:     ids,
                    jumlah_pelayanan: jumlah,
                });
            },
        }
    }

    function searchLayananChild(getLabel, onSelect) {
        return {
            results: [],
            search:  '',
            show:    false,

            init() {
                const label = getLabel ? getLabel() : '';
                if (label) this.search = label;
            },

            async searchLayanan() {
                if (this.search.length < 2) {
                    this.results = [];
                    this.show = false;
                    return;
                }
                let res = await fetch('{{ route('ajax.layanan') }}?q=' + this.search);
                this.results = await res.json();
                this.show = true;
            },

            selectLayanan(l) {
                this.search = l.nama;
                this.show   = false;
                onSelect(l);
            },
        }
    }
</script>