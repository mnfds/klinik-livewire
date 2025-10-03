<div class="bg-base-200 p-4 rounded border border-base-200"
    x-data="{
        layananItems: [{ uid: Date.now(), pelayanan_id: '', jumlah_pelayanan: 1 }],

        addLayanan() {
            this.layananItems.push({ uid: Date.now() + Math.random(), pelayanan_id: '', jumlah_pelayanan: 1 });
            this.syncLayananToLivewire();
        },
        removeLayanan(index) {
            this.layananItems.splice(index, 1);
            this.syncLayananToLivewire();
            this.cleanupLayananLivewire();
        },
        syncItemLayanan(i) {
            let item = this.layananItems[i];
            $wire.set(`rencana_layanan.pelayanan_id.${i}`, item.pelayanan_id);
            $wire.set(`rencana_layanan.jumlah_pelayanan.${i}`, item.jumlah_pelayanan);
        },
        syncLayananToLivewire() {
            this.layananItems.forEach((item, i) => this.syncItemLayanan(i));
        },
        cleanupLayananLivewire() {
            let length = this.layananItems.length;
            for (let i = length; i < 100; i++) {
                $wire.set(`rencana_layanan.pelayanan_id.${i}`, null);
                $wire.set(`rencana_layanan.jumlah_pelayanan.${i}`, null);
            }
        },
    }"
>
    @props([
        'rencanaLayanan' => [
            'pelayanan_id' => [],
            'jumlah_pelayanan' => [],
        ],
        'layanandanbundling' => [
            'layanan' => [],
            'bundling' => [],
        ]
    ])

    <div class="divider">Rencana Tindakan Medis</div>

    {{-- SECTION LAYANAN --}}
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <button type="button" class="btn btn-primary btn-sm mx-1" @click="addLayanan">
                + Tambah Layanan
            </button>
        </div>

        {{-- Section Layanan --}}
        <div class="mb-4 border p-4 rounded-lg bg-base-100">
            <!-- Header -->
            <div class="flex font-semibold mb-2">
                <div class="flex-1">Pilih Layanan</div>
                <div class="w-32 hidden">Jumlah</div>
                <div class="w-20"></div> {{-- kolom tombol hapus --}}
            </div>

            <!-- Rows -->
            <template x-for="(item, index) in layananItems" :key="item.uid">
                <div class="flex flex-col gap-1 mb-4 relative" 
                     x-data="{
                        results: [],
                        search: '',
                        show: false,
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
                            item.pelayanan_id = l.id;
                            this.search = l.nama;
                            this.show = false;
                            syncItemLayanan(index);
                        }
                    }">
                    
                    <!-- Input Search -->
                    <input type="text"
                        class="input input-bordered w-full"
                        placeholder="Cari Layanan..."
                        x-model="search"
                        @input.debounce.300ms="searchLayanan"
                    >

                    <!-- Hidden field simpan ID -->
                    <input type="hidden"
                        :name="`rencanaLayanan[pelayanan_id][${index}]`"
                        x-model="item.pelayanan_id"
                    >

                    <!-- Dropdown Hasil -->
                    <div class="absolute left-0 right-0 top-full mt-1 z-50 
                            bg-white border rounded w-full max-h-48 overflow-y-auto shadow-lg"
                        x-show="show && results.length"
                        @click.outside="show=false">
                        <template x-for="l in results" :key="l.id">
                            <div class="px-3 py-2 hover:bg-gray-200 cursor-pointer"
                                @click="selectLayanan(l)">
                                <span x-text="l.nama"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Input jumlah (opsional, hidden default) -->
                    <input type="number" min="1"
                        class="input input-bordered w-32 hidden"
                        :name="`rencanaLayanan[jumlah_pelayanan][${index}]`"
                        x-model.number="item.jumlah_pelayanan"
                        @input="syncItemLayanan(index)"
                    >

                    <!-- Tombol hapus -->
                    <button type="button"
                        class="btn btn-error btn-sm w-20 mt-2"
                        @click="removeLayanan(index)"
                        x-show="layananItems.length > 1">
                        Hapus
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>
