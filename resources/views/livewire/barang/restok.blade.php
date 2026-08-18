<dialog id="restockModalBarang" class="modal" wire:ignore.self x-data x-init="Livewire.on('closerestockModalBarang', () => { document.getElementById('restockModalBarang')?.close() })">
    <div class="modal-box w-full max-w-lg">
        <h3 class="text-xl font-semibold mb-4">Barang Masuk</h3>
        <form wire:submit.prevent="store" class="space-y-4">
            {{-- Tambah Tab --}}
            <div>
                <button type="button" class="btn btn-primary btn-sm" wire:click="addTab">
                    + Tambah Form
                </button>
            </div>

            {{-- Tab Headers --}}
            <div role="tablist" class="tabs tabs-bordered flex-wrap">
                @foreach ($items as $i => $item)
                    <button type="button" role="tab" class="tab gap-1 {{ $activeTab === $i ? 'tab-active border-b-2 border-primary text-primary' : 'border-b-2 border-transparent' }}" wire:click="$set('activeTab', {{ $i }})">
                        <span>
                            Form {{ $i + 1 }}
                            @if ($errors->has("items.$i.barang_id") || $errors->has("items.$i.jumlah"))
                                <span class="inline-block w-2 h-2 rounded-full bg-error ml-1 align-middle"></span>
                            @endif
                        </span>
                        @if (count($items) > 1)
                            <span role="button" class="ml-1 text-base-content/40 hover:text-error transition-colors" wire:click.stop="removeTab({{ $i }})">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>

            {{-- Tab Content --}}
            @foreach ($items as $i => $item)
                <div @class(['hidden' => $activeTab !== $i, 'space-y-4' => true])>
                    {{-- Nama Barang --}}
                    <div
                        wire:key="barang-combobox-{{ $i }}"
                        x-data="{
                            open: false,
                            search: '',
                            barang: {{ \Illuminate\Support\Js::from($barang) }},
                            selectedId: @entangle("items.{$i}.barang_id"),
                            get filtered() {
                                return this.search === ''
                                    ? this.barang
                                    : this.barang.filter(b => b.nama.toLowerCase().includes(this.search.toLowerCase()))
                            },
                            get selectedLabel() {
                                let b = this.barang.find(b => b.id == this.selectedId)
                                return b ? b.nama : ''
                            },
                            choose(item) {
                                this.selectedId = item.id
                                this.search = item.nama
                                this.open = false
                            }
                        }"
                        x-init="search = selectedLabel"
                        @click.outside="open = false; search = selectedLabel"
                        class="relative"
                        >
                        <label class="label font-medium">
                            Nama Barang <span class="text-error">*</span>
                        </label>

                        <input
                            type="text"
                            x-model="search"
                            @focus="open = true; search = ''"
                            placeholder="Cari nama barang..."
                            autocomplete="off"
                            class="input input-bordered w-full @error("items.$i.barang_id") input-error @enderror"
                        >

                        <ul x-show="open" x-cloak
                            class="absolute z-50 mt-1 w-full max-h-60 overflow-y-auto bg-base-100 border border-base-300 rounded-box shadow">
                            <template x-for="item in filtered" :key="item.id">
                                <li @click="choose(item)"
                                    class="px-4 py-2 cursor-pointer hover:bg-base-200"
                                    x-text="item.nama">
                                </li>
                            </template>
                            <li x-show="filtered.length === 0" class="px-4 py-2 text-gray-400 text-sm">
                                Tidak ditemukan
                            </li>
                        </ul>

                        @error("items.$i.barang_id")
                            <span class="text-error text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Jumlah --}}
                    <div>
                        <label class="label font-medium">
                            Jumlah <span class="text-error">*</span>
                        </label>
                        <input type="number" min="1" class="input input-bordered w-full @error("items.$i.jumlah") input-error @enderror" wire:model.lazy="items.{{ $i }}.jumlah" placeholder="0">
                        @error("items.$i.jumlah")
                            <span class="text-error text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="label font-medium">Catatan</label>
                        <input type="text" class="input input-bordered w-full" wire:model.lazy="items.{{ $i }}.catatan" placeholder="Opsional...">
                    </div>
                </div>
            @endforeach

            {{-- Actions --}}
            <div class="modal-action justify-end pt-4">
                @can('akses', 'Persediaan Barang Masuk')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('restockModalBarang').close()">
                    Batal
                </button>
            </div>
        </form>
    </div>

    {{-- Backdrop --}}
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>