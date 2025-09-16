<div class="bg-base-200 p-4 rounded border border-base-200"
    x-data="{
        layananItems: [{ pelayanan_id: '', jumlah_pelayanan: 1 }],

        addLayanan() {
            this.layananItems.push({ pelayanan_id: '', jumlah_pelayanan: 1 });
            this.syncLayananToLivewire();
        },
        removeLayanan(index) {
            this.layananItems.splice(index, 1);
            this.reindexLayanan();
            this.syncLayananToLivewire();
            this.cleanupLayananLivewire();
        },
        reindexLayanan() {
            this.layananItems = this.layananItems.map(item => ({ ...item }));
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
                <div class="w-20"></div> {{-- untuk kolom tombol hapus --}}
            </div>

            <!-- Rows -->
            <template x-for="(item, index) in layananItems" :key="index">
                <div class="flex items-center gap-4 mb-2">
                    <select class="select select-bordered flex-1"
                        :name="`rencanaLayanan[pelayanan_id][${index}]`"
                        x-model="item.pelayanan_id"
                        @change="syncItemLayanan(index)"
                    >
                        <option value="">-- Pilih Layanan --</option>
                        @foreach($layanandanbundling['layanan'] as $layanan)
                            <option value="{{ $layanan['id'] }}">{{ $layanan['nama_pelayanan'] }}</option>
                        @endforeach
                    </select>

                    <input type="number" min="1"
                        class="input input-bordered w-32 hidden"
                        :name="`rencanaLayanan[jumlah_pelayanan][${index}]`"
                        x-model.number="item.jumlah_pelayanan"
                        @input="syncItemLayanan(index)"
                    >

                    <button type="button"
                        class="btn btn-error btn-sm w-20"
                        @click="removeLayanan(index)"
                        x-show="layananItems.length > 1">
                        Hapus
                    </button>
                </div>
            </template>
        </div>
    </div>

</div>
