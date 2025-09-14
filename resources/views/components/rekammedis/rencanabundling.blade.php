<div class="bg-base-200 p-4 rounded border border-base-200"
    x-data="{
        bundlingItems: [{ bundling_id: '', jumlah_bundling: 1 }],

        addBundling() {
            this.bundlingItems.push({ bundling_id: '', jumlah_bundling: 1 });
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
            $wire.set(`rencana_bundling.bundling_id.${i}`, item.bundling_id);
            $wire.set(`rencana_bundling.jumlah_bundling.${i}`, item.jumlah_bundling);
        },
        syncBundlingToLivewire() {
            this.bundlingItems.forEach((item, i) => this.syncItemBundling(i));
        },
        cleanupBundlingLivewire() {
            let length = this.bundlingItems.length;
            for (let i = length; i < 100; i++) {
                $wire.set(`rencana_bundling.bundling_id.${i}`, null);
                $wire.set(`rencana_bundling.jumlah_bundling.${i}`, null);
            }
        },
    }"
    >
    @props([
        'rencanaBundling' => [
           'bundling_id' => [],
           'jumlah_bundling' => [],
        ],
        'layanandanbundling' => [
            'treatment' => [],
            'bundling' => [],
        ]
    ])

    <div class="divider">Paket Bundling</div>

    {{-- SECTION BUNDLING --}}
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <button type="button" class="btn btn-primary btn-sm mx-1" @click="addBundling">
                + Tambah Bundling
            </button>
        </div>

        {{-- Section Bundling --}}
        <div class="mb-4 border p-4 rounded-lg bg-base-100">
            <!-- Header -->
            <div class="flex font-semibold mb-2">
                <div class="flex-1">Pilih Bundling</div>
                <div class="w-32">Jumlah</div>
                <div class="w-20"></div>
            </div>

            <!-- Rows -->
            <template x-for="(item, index) in bundlingItems" :key="'bundling-' + index">
                <div class="w-full">
                    <div class="flex items-center gap-4 mb-2">
                        <select class="select select-bordered flex-1"
                            :name="`rencanaBundling[bundling_id][${index}]`"
                            x-model="item.bundling_id"
                            @change="syncItemBundling(index)"
                        >
                            <option value="">-- Pilih Bundling --</option>
                            @foreach($layanandanbundling['bundling'] as $bundle)
                                <option value="{{ $bundle['id'] }}">{{ $bundle['nama'] }}</option>
                            @endforeach
                        </select>

                        <input type="number" min="1"
                            class="input input-bordered w-32"
                            :name="`rencanaBundling[jumlah_bundling][${index}]`"
                            x-model.number="item.jumlah_bundling"
                            @input="syncItemBundling(index)"
                        >

                        <input type="number" min="1"
                            class="input input-bordered w-32"
                            :name="`rencanaBundling[harga][${index}]`"
                            x-model.number="item.harga"
                            value="{{ $bundle['harga'] }}"
                            @input="syncItemBundling(index)"
                        >

                        <button type="button"
                            class="btn btn-error btn-sm w-20"
                            @click="removeBundling(index)"
                            x-show="bundlingItems.length > 1">
                            Hapus
                        </button>
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
                                            <ul class="list-disc list-inside text-sm">
                                                @foreach($bundle['treatmentBundlings'] as $tb)
                                                    <li>
                                                        {{ $tb['treatment']['nama_treatment'] ?? '-' }},
                                                        Tersedia :
                                                        <span x-text="item.jumlah_bundling * {{ $tb->jumlah ?? 0 }}"></span>
                                                        <i class="fa-solid fa-xmark text-[10px]"></i>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <ul class="list-disc list-inside text-sm">
                                                <li>Tidak Tersedia</li>
                                            </ul>
                                        @endif

                                        {{-- Pelayanan --}}
                                        <p class="mt-2 text-sm font-medium">Pelayanan:</p>
                                        @if(isset($bundle['pelayananBundlings']) && count($bundle['pelayananBundlings']) > 0)
                                            <ul class="list-disc list-inside text-sm">
                                                @foreach($bundle['pelayananBundlings'] as $pb)
                                                    <li>{{ $pb['pelayanan']['nama_pelayanan'] . ', ' ?? '-' }}
                                                        Tersedia :
                                                        <span x-text="item.jumlah_bundling * {{ $pb->jumlah ?? 0 }}"></span>
                                                        <i class="fa-solid fa-xmark text-[10px]"></i>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <ul class="list-disc list-inside text-sm">
                                                <li>Tidak Tersedia</li>
                                            </ul>
                                        @endif

                                        {{-- Produk / Obat --}}
                                        <p class="mt-2 text-sm font-medium">Produk / Obat:</p>
                                        @if(isset($bundle['produkObatBundlings']) && count($bundle['produkObatBundlings']) > 0)
                                            <ul class="list-disc list-inside text-sm">
                                                @foreach($bundle['produkObatBundlings'] as $ob)
                                                    <li>{{ $ob['produk']['nama_dagang'] . ', ' ?? '-' }}
                                                        Tersedia :
                                                        <span x-text="item.jumlah_bundling * {{ $ob->jumlah ?? 0 }}"></span>
                                                        <i class="fa-solid fa-xmark text-[10px]"></i>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <ul class="list-disc list-inside text-sm">
                                                <li>Tidak Tersedia</li>
                                            </ul>
                                        @endif
                                    </div>
                                </template>
                            @endforeach
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

</div>
