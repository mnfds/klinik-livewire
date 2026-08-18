<dialog id="storeModalBundling" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeStoreModalBundling', () => {
        document.getElementById('storeModalBundling')?.close();
    })
">
    <div class="modal-box max-w-4xl w-full">
        <h3 class="text-xl font-semibold mb-4">Tambah Bundling</h3>

        <form wire:submit.prevent="store" class="space-y-5">

            {{-- Nama Bundling --}}
            <div class="form-control">
                <label class="label font-semibold">Nama Bundling <span class="text-error">*</span></label>
                <input type="text" class="input input-bordered w-full @error('nama') input-error @enderror" placeholder="Masukkan nama bundling" wire:model.defer="nama">
                @error('nama')
                    <span class="text-error text-sm mt-1">
                        Mohon Mengisi Nama Bundling
                    </span>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="form-control">
                <label class="label font-semibold">Deskripsi</label>
                <textarea class="textarea textarea-bordered w-full" placeholder="Deskripsi bundling" wire:model.defer="deskripsi"></textarea>
            </div>

            {{-- Harga --}}
            <div class="form-control">
                <label class="label font-semibold">Harga <span class="text-error">*</span></label>
                <input type="text" class="input input-bordered input-rupiah w-full @error('harga') input-error @enderror" placeholder="Rp 0">
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga">
                @error('harga')
                    <span class="text-error text-sm mt-1">
                        Mohon Mengisi Harga Bundling
                    </span>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Potongan --}}
                <div class="form-control">
                    <label class="label font-semibold">Potongan</label>
                    <input type="text" class="input input-bordered input-rupiah w-full" placeholder="Rp 0">
                    <input type="hidden" class="input-rupiah-hidden" wire:model.defer="potongan">
                </div>

                {{-- Diskon --}}
                <div class="form-control">
                    <label class="label font-semibold">Diskon (%)</label>
                    <input type="number" class="input input-bordered w-full" placeholder="0-100" min="0" max="100" wire:model.defer="diskon">
                </div>
            </div>

            {{-- Harga Bersih --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Bersih <span class="text-error">*</span></label>
                <input type="text" class="input input-bordered input-rupiah bg-base-200 w-full @error('harga_bersih') input-error @enderror" placeholder="Otomatis terhitung" readonly>
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga_bersih">
                @error('harga_bersih')
                    <span class="text-error text-sm mt-1">
                        Mohon Mengisi Harga Bundling
                    </span>
                @enderror
            </div>

            {{-- Pelayanan Dinamis --}}
            <div class="form-control">
                <label class="label font-semibold">Pelayanan Medis</label>
                <div class="space-y-2">
                    @foreach ($pelayananInputs as $index => $row)
                        <div class="flex flex-col md:flex-row gap-2">
                            <div
                                wire:key="pelayanan-combobox-{{ $index }}"
                                x-data="{
                                    open: false,
                                    search: '',
                                    list: {{ \Illuminate\Support\Js::from($pelayananList) }},
                                    selectedId: @entangle("pelayananInputs.{$index}.pelayanan_id"),
                                    get filtered() {
                                        return this.search === ''
                                            ? this.list
                                            : this.list.filter(x => x.nama.toLowerCase().includes(this.search.toLowerCase()))
                                    },
                                    get selectedLabel() {
                                        let x = this.list.find(x => x.id == this.selectedId)
                                        return x ? x.nama : ''
                                    },
                                    choose(item) {
                                        this.selectedId = item.id
                                        this.search = item.nama
                                        this.open = false
                                    }
                                }"
                                x-init="search = selectedLabel"
                                @click.outside="open = false; search = selectedLabel"
                                class="relative w-full md:flex-1"
                            >
                                <input
                                    type="text"
                                    x-model="search"
                                    @focus="open = true; search = ''"
                                    placeholder="Cari pelayanan..."
                                    autocomplete="off"
                                    class="input input-bordered w-full"
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
                            </div>

                            <input type="number" min="1" class="input input-bordered w-full md:w-28" placeholder="Jumlah"
                                wire:model.defer="pelayananInputs.{{ $index }}.jumlah">
                            <button type="button" class="btn btn-error btn-sm" wire:click="removePelayananRow({{ $index }})">✕</button>
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-outline btn-sm mt-1" wire:click="addPelayananRow">+ Tambah Pelayanan</button>
                </div>
            </div>

            {{-- treatment Dinamis --}}
            <div class="form-control">
                <label class="label font-semibold">Pelayanan Estetika</label>
                <div class="space-y-2">
                    @foreach ($treatmentInputs as $index => $row)
                        <div class="flex flex-col md:flex-row gap-2">
                            <div
                                wire:key="treatment-combobox-{{ $index }}"
                                x-data="{
                                    open: false,
                                    search: '',
                                    list: {{ \Illuminate\Support\Js::from($treatmentList) }},
                                    selectedId: @entangle("treatmentInputs.{$index}.treatments_id"),
                                    get filtered() {
                                        return this.search === ''
                                            ? this.list
                                            : this.list.filter(x => x.nama.toLowerCase().includes(this.search.toLowerCase()))
                                    },
                                    get selectedLabel() {
                                        let x = this.list.find(x => x.id == this.selectedId)
                                        return x ? x.nama : ''
                                    },
                                    choose(item) {
                                        this.selectedId = item.id
                                        this.search = item.nama
                                        this.open = false
                                    }
                                }"
                                x-init="search = selectedLabel"
                                @click.outside="open = false; search = selectedLabel"
                                class="relative w-full md:flex-1"
                            >
                                <input
                                    type="text"
                                    x-model="search"
                                    @focus="open = true; search = ''"
                                    placeholder="Cari pelayanan estetika..."
                                    autocomplete="off"
                                    class="input input-bordered w-full"
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
                            </div>

                            <input type="number" min="1" class="input input-bordered w-full md:w-28" placeholder="Jumlah"
                                wire:model.defer="treatmentInputs.{{ $index }}.jumlah">
                            <button type="button" class="btn btn-error btn-sm" wire:click="removeTreatmentRow({{ $index }})">✕</button>
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-outline btn-sm mt-1" wire:click="addTreatmentRow">+ Tambah Pelayanan Estetika</button>
                </div>
            </div>

            {{-- Produk & Obat Dinamis --}}
            <div class="form-control">
                <label class="label font-semibold">Produk & Obat</label>
                <div class="space-y-2">
                    @foreach ($produkInputs as $index => $row)
                        <div class="flex flex-col md:flex-row gap-2">
                            <div
                                wire:key="produk-bundling-combobox-{{ $index }}"
                                x-data="{
                                    open: false,
                                    search: '',
                                    list: {{ \Illuminate\Support\Js::from($produkObatList) }},
                                    selectedId: @entangle("produkInputs.{$index}.produk_id"),
                                    get filtered() {
                                        return this.search === ''
                                            ? this.list
                                            : this.list.filter(x => x.nama.toLowerCase().includes(this.search.toLowerCase()))
                                    },
                                    get selectedLabel() {
                                        let x = this.list.find(x => x.id == this.selectedId)
                                        return x ? x.nama : ''
                                    },
                                    choose(item) {
                                        this.selectedId = item.id
                                        this.search = item.nama
                                        this.open = false
                                    }
                                }"
                                x-init="search = selectedLabel"
                                @click.outside="open = false; search = selectedLabel"
                                class="relative w-full md:flex-1"
                            >
                                <input
                                    type="text"
                                    x-model="search"
                                    @focus="open = true; search = ''"
                                    placeholder="Cari produk / obat..."
                                    autocomplete="off"
                                    class="input input-bordered w-full"
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
                            </div>

                            <input type="number" min="1" class="input input-bordered w-full md:w-28" placeholder="Jumlah"
                                wire:model.defer="produkInputs.{{ $index }}.jumlah">
                            <button type="button" class="btn btn-error btn-sm" wire:click="removeProdukRow({{ $index }})">✕</button>
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-outline btn-sm mt-1" wire:click="addProdukRow">+ Tambah Produk / Obat</button>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="modal-action flex justify-end gap-2">
                @can('akses', 'Paket Bundling Tambah')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-neutral" onclick="document.getElementById('storeModalBundling')?.close()">Batal</button>
            </div>
        </form>
    </div>

    {{-- Script tetap seperti sebelumnya --}}
    <script>
        function hitungHargaBersih() {
            const hargaInput = document.querySelector('input[wire\\:model\\.defer="harga"]');
            const potonganInput = document.querySelector('input[wire\\:model\\.defer="potongan"]');
            const diskonInput = document.querySelector('input[wire\\:model\\.defer="diskon"]');
            const hargaBersihInput = document.querySelector('input[wire\\:model\\.defer="harga_bersih"]');
            const hargaBersihDisplay = hargaBersihInput?.previousElementSibling;

            if (!hargaInput || !potonganInput || !diskonInput || !hargaBersihInput || !hargaBersihDisplay) return;

            const harga = parseInt(hargaInput.value.replace(/\D/g, '') || 0);
            const potongan = parseInt(potonganInput.value.replace(/\D/g, '') || 0);
            const diskon = parseFloat(diskonInput.value || 0);

            const hargaSetelahPotongan = Math.max(0, harga - potongan);
            const diskonNominal = (hargaSetelahPotongan * diskon) / 100;
            const hargaBersih = Math.max(0, Math.round(hargaSetelahPotongan - diskonNominal));

            hargaBersihInput.value = hargaBersih;

            if (hargaBersihDisplay._cleave) {
                hargaBersihDisplay._cleave.setRawValue(hargaBersih);
            } else {
                hargaBersihDisplay.value = hargaBersih;
            }

            hargaBersihInput.dispatchEvent(new Event('input'));
        }

        function reinitHargaBersihListeners() {
            const hargaInput = document.querySelector('input[wire\\:model\\.defer="harga"]');
            const potonganInput = document.querySelector('input[wire\\:model\\.defer="potongan"]');
            const diskonInput = document.querySelector('input[wire\\:model\\.defer="diskon"]');
            
            [hargaInput, potonganInput, diskonInput].forEach(el => {
                if (el) {
                    el.removeEventListener('input', hitungHargaBersih);
                    el.addEventListener('input', hitungHargaBersih);
                }
            });

            hitungHargaBersih();
        }

        function reinitBundlingModalHelpers() {
            initCleaveRupiah();
            reinitHargaBersihListeners();
        }

        document.addEventListener('DOMContentLoaded', reinitBundlingModalHelpers);
        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', reinitBundlingModalHelpers);
        });
        document.addEventListener('livewire:navigated', reinitBundlingModalHelpers);
    </script>
</dialog>
