<div class="bg-base-200 p-4 rounded border border-base-200"
    x-data="{
        layananItems: [{ pelayanan_id: '', jumlah_pelayanan: 1 }],
        bundlingItems: [{ bundling_id: '', jumlah_bundling: 1 }],

        addLayanan() {
            this.layananItems.push({ pelayanan_id: '', jumlah_pelayanan: 1 });
        },
        removeLayanan(index) {
            this.layananItems.splice(index, 1);
        },

        addBundling() {
            this.bundlingItems.push({ bundling_id: '', jumlah_bundling: 1 });
        },
        removeBundling(index) {
            this.bundlingItems.splice(index, 1);
        }
    }"
>
    @props([
        'rencanaLayanan' => [
            'pelayanan_id' => [],
            'bundling_id' => [],
            'jumlah_pelayanan' => [],
            'jumlah_bundling' => [],
            'jumlah_harga'=> [],
            'total_keseluruhan'=> null,
        ],
        'layanandanbundling' => [
            'layanan' => [],
            'bundling' => [],
        ]
    ])

    <div class="divider">Rencana Layanan Atau Tindakan</div>

    {{-- SECTION LAYANAN --}}
    <div class="mb-6">
        <div class="flex justify-between items-center mb-2">
            <h3 class="font-bold">Layanan</h3>
            <button type="button" class="btn btn-primary btn-sm" @click="addLayanan">
                + Tambah Layanan
            </button>
        </div>

        <template x-for="(item, index) in layananItems" :key="'layanan-' + index">
            <div class="mb-4 border p-4 rounded-lg bg-base-100 relative">

                {{-- Tombol Hapus --}}
                <button type="button" class="btn btn-error btn-xs absolute top-2 right-2"
                    @click="removeLayanan(index)"
                    x-show="layananItems.length > 1">
                    Hapus
                </button>

                {{-- Select Layanan --}}
                <label class="form-control">
                    <span class="label-text font-semibold">Pilih Layanan</span>
                    <select class="select select-bordered w-full"
                        :name="`rencanaLayanan[pelayanan_id][]`"
                        x-model="item.pelayanan_id">
                        <option value="">-- Pilih Layanan --</option>
                        @foreach($layanandanbundling['layanan'] as $layanan)
                            <option value="{{ $layanan['id'] }}">
                                {{ $layanan['nama_pelayanan'] }}
                            </option>
                        @endforeach
                    </select>
                </label>

                {{-- Jumlah Layanan --}}
                <label class="form-control mt-2">
                    <span class="label-text">Jumlah</span>
                    <input type="number" min="1"
                        class="input input-bordered w-full"
                        :name="`rencanaLayanan[jumlah_pelayanan][]`"
                        x-model="item.jumlah_pelayanan">
                </label>
            </div>
        </template>
    </div>

    {{-- SECTION BUNDLING --}}
    <div>
        <div class="flex justify-between items-center mb-2">
            <h3 class="font-bold">Bundling</h3>
            <button type="button" class="btn btn-primary btn-sm" @click="addBundling">
                + Tambah Bundling
            </button>
        </div>

        <template x-for="(item, index) in bundlingItems" :key="'bundling-' + index">
            <div class="mb-4 border p-4 rounded-lg bg-base-100 relative">

                {{-- Tombol Hapus --}}
                <button type="button" class="btn btn-error btn-xs absolute top-2 right-2"
                    @click="removeBundling(index)"
                    x-show="bundlingItems.length > 1">
                    Hapus
                </button>

                {{-- Select Bundling --}}
                <label class="form-control">
                    <span class="label-text font-semibold">Pilih Bundling</span>
                    <select class="select select-bordered w-full"
                        :name="`rencanaLayanan[bundling_id][]`"
                        x-model="item.bundling_id">
                        <option value="">-- Pilih Bundling --</option>
                        @foreach($layanandanbundling['bundling'] as $bundle)
                            <option value="{{ $bundle['id'] }}">
                                {{ $bundle['nama'] }}
                            </option>
                        @endforeach
                    </select>
                </label>

                {{-- Jumlah Bundling --}}
                <label class="form-control mt-2">
                    <span class="label-text">Jumlah</span>
                    <input type="number" min="1"
                        class="input input-bordered w-full"
                        :name="`rencanaLayanan[jumlah_bundling][]`"
                        x-model="item.jumlah_bundling">
                </label>
            </div>
        </template>
    </div>
</div>
