<dialog id="modalEditBundling" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('openModalEditBundling', () => {
        document.getElementById('modalEditBundling')?.showModal()
        reinitEditBundlingModalHelpers()
    })
    Livewire.on('closeModalEditBundling', () => {
        document.getElementById('modalEditBundling')?.close()
    })
">
    <div class="modal-box max-w-4xl w-full">
        <h3 class="text-xl font-semibold mb-4">Edit Bundling</h3>

        <form wire:submit.prevent="update" class="space-y-5">

            {{-- Nama --}}
            <div class="form-control">
                <label class="label font-semibold">Nama Bundling <span class="text-error">*</span></label>
                <input type="text" class="input input-bordered w-full @error('nama') input-error @enderror" wire:model.defer="nama">
                @error('nama')
                    <span class="text-error text-sm mt-1">
                        Mohon Mengisi Nama Bundling
                    </span>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="form-control">
                <label class="label font-semibold">Deskripsi</label>
                <textarea class="textarea textarea-bordered w-full" wire:model.defer="deskripsi" rows="2"></textarea>
            </div>

            {{-- Harga --}}
            <div class="form-control">
                <label class="label font-semibold">Harga <span class="text-error">*</span></label>
                <input type="text" id="display_harga_bundling" class="input input-bordered input-rupiah w-full @error('harga') input-error @enderror" wire:model.defer="harga_show" placeholder="Rp 0">
                <input type="hidden" wire:model.defer="harga" class="input-rupiah-hidden">
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
                    <input type="text" id="display_potongan" class="input input-bordered input-rupiah w-full" wire:model.defer="potongan_show" placeholder="Rp 0">
                    <input type="hidden" wire:model.defer="potongan" class="input-rupiah-hidden">
                </div>

                {{-- Diskon --}}
                <div class="form-control">
                    <label class="label font-semibold">Diskon (%)</label>
                    <input type="number" class="input input-bordered w-full" wire:model.defer="diskon" min="0" max="100">
                </div>
            </div>

            {{-- Harga Bersih --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Bersih (Setelah Diskon) <span class="text-error">*</span></label>
                <input type="text" id="display_harga_bersih_bundling" class="input input-bordered input-rupiah bg-base-200 w-full @error('harga_bersih') input-error @enderror" wire:model.defer="harga_bersih_show" readonly placeholder="Otomatis terhitung">
                <input type="hidden" wire:model.defer="harga_bersih" class="input-rupiah-hidden">
                @error('harga_bersih')
                    <span class="text-error text-sm mt-1">
                        Mohon Mengisi Harga Bundling
                    </span>
                @enderror
            </div>

            {{-- Pelayanan Medis --}}
            <div class="form-control">
                <label class="label font-semibold">Pelayanan Medis</label>
                <div class="space-y-2">
                    @foreach ($pelayananInputs as $index => $row)
                        <div class="flex flex-col md:flex-row gap-2">
                            <div
                                wire:key="edit-pelayanan-combobox-{{ $index }}"
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
                                x-effect="search = selectedLabel"
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

                            <input type="number" min="1" class="input input-bordered w-full md:w-28"
                                wire:model.defer="pelayananInputs.{{ $index }}.jumlah" placeholder="Jumlah">
                            <button type="button" class="btn btn-error btn-sm" wire:click="removePelayananRow({{ $index }})">✕</button>
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-outline btn-sm mt-1" wire:click="addPelayananRow">+ Tambah Pelayanan</button>
                </div>
            </div>

            {{-- Treatment / Pelayanan Estetika --}}
            <div class="form-control">
                <label class="label font-semibold">Pelayanan Estetika</label>
                <div class="space-y-2">
                    @foreach ($treatmentInputs as $index => $row)
                        <div class="flex flex-col md:flex-row gap-2">
                            <div
                                wire:key="edit-treatment-combobox-{{ $index }}"
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
                                x-effect="search = selectedLabel"
                                @click.outside="open = false; search = selectedLabel"
                                class="relative w-full md:flex-1"
                            >
                                <input type="text" x-model="search" @focus="open = true; search = ''"
                                    placeholder="Cari pelayanan estetika..." autocomplete="off" class="input input-bordered w-full">
                                <ul x-show="open" x-cloak class="absolute z-50 mt-1 w-full max-h-60 overflow-y-auto bg-base-100 border border-base-300 rounded-box shadow">
                                    <template x-for="item in filtered" :key="item.id">
                                        <li @click="choose(item)" class="px-4 py-2 cursor-pointer hover:bg-base-200" x-text="item.nama"></li>
                                    </template>
                                    <li x-show="filtered.length === 0" class="px-4 py-2 text-gray-400 text-sm">Tidak ditemukan</li>
                                </ul>
                            </div>
                            <input type="number" min="1" class="input input-bordered w-full md:w-28"
                                wire:model.defer="treatmentInputs.{{ $index }}.jumlah" placeholder="Jumlah">
                            <button type="button" class="btn btn-error btn-sm" wire:click="removeTreatmentRow({{ $index }})">✕</button>
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-outline btn-sm mt-1" wire:click="addTreatmentRow">+ Tambah Pelayanan Estetika</button>
                </div>
            </div>

            {{-- Produk & Obat --}}
            <div class="form-control">
                <label class="label font-semibold">Produk & Obat</label>
                <div class="space-y-2">
                    @foreach ($produkInputs as $index => $row)
                        <div class="flex flex-col md:flex-row gap-2">
                            <div
                                wire:key="edit-produk-combobox-{{ $index }}"
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
                                x-effect="search = selectedLabel"
                                @click.outside="open = false; search = selectedLabel"
                                class="relative w-full md:flex-1"
                            >
                                <input type="text" x-model="search" @focus="open = true; search = ''"
                                    placeholder="Cari produk / obat..." autocomplete="off" class="input input-bordered w-full">
                                <ul x-show="open" x-cloak class="absolute z-50 mt-1 w-full max-h-60 overflow-y-auto bg-base-100 border border-base-300 rounded-box shadow">
                                    <template x-for="item in filtered" :key="item.id">
                                        <li @click="choose(item)" class="px-4 py-2 cursor-pointer hover:bg-base-200" x-text="item.nama"></li>
                                    </template>
                                    <li x-show="filtered.length === 0" class="px-4 py-2 text-gray-400 text-sm">Tidak ditemukan</li>
                                </ul>
                            </div>
                            <input type="number" min="1" class="input input-bordered w-full md:w-28"
                                wire:model.defer="produkInputs.{{ $index }}.jumlah" placeholder="Jumlah">
                            <button type="button" class="btn btn-error btn-sm" wire:click="removeProdukRow({{ $index }})">✕</button>
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-outline btn-sm mt-1" wire:click="addProdukRow">+ Tambah Produk</button>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="modal-action flex justify-end gap-2">
                @can('akses', 'Paket Bundling Edit')
                <button type="submit" class="btn btn-primary">Update</button>
                @endcan
                <button type="button" class="btn btn-neutral" onclick="modalEditBundling?.close()">Batal</button>
            </div>
        </form>
    </div>

    {{-- Script: Cleave & Hitung --}}
<script>
    function hitungHargaBersihEditBundling() {
        const root = document.querySelector('#modalEditBundling');

        const hargaInput = root.querySelector('input[wire\\:model\\.defer="harga"]')?.previousElementSibling;
        const potonganInput = root.querySelector('input[wire\\:model\\.defer="potongan"]')?.previousElementSibling;
        const diskonInput = root.querySelector('input[wire\\:model\\.defer="diskon"]');
        const hargaHidden = root.querySelector('input[wire\\:model\\.defer="harga"]');
        const potonganHidden = root.querySelector('input[wire\\:model\\.defer="potongan"]');
        const hargaBersihHidden = root.querySelector('input[wire\\:model\\.defer="harga_bersih"]');
        const hargaBersihDisplay = hargaBersihHidden?.previousElementSibling;

        if (!hargaInput || !potonganInput || !diskonInput || !hargaHidden || !potonganHidden || !hargaBersihHidden || !hargaBersihDisplay) {
            return;
        }

        const harga = parseInt(hargaInput.value.replace(/\D/g, '') || 0);
        const potongan = parseInt(potonganInput.value.replace(/\D/g, '') || 0);
        const diskon = parseFloat(diskonInput.value || 0);

        const hargaSetelahPotongan = Math.max(0, harga - potongan);
        const diskonNominal = (hargaSetelahPotongan * diskon) / 100;
        const hargaBersih = Math.max(0, Math.round(hargaSetelahPotongan - diskonNominal));

        // Update hidden fields
        hargaHidden.value = harga;
        hargaHidden.dispatchEvent(new Event('input'));

        potonganHidden.value = potongan;
        potonganHidden.dispatchEvent(new Event('input'));

        hargaBersihHidden.value = hargaBersih;
        hargaBersihHidden.dispatchEvent(new Event('input'));

        // Update display harga bersih jika Cleave aktif
        if (hargaBersihDisplay._cleave) {
            hargaBersihDisplay._cleave.setRawValue(hargaBersih);
        } else {
            hargaBersihDisplay.value = hargaBersih;
        }
    }

    function isiAwalHargaBundlingEdit() {
        const root = document.querySelector('#modalEditBundling');

        const hargaDisplay = root.querySelector('#display_harga_bundling');
        const potonganDisplay = root.querySelector('#display_potongan');
        const hargaBersihDisplay = root.querySelector('#display_harga_bersih_bundling');

        const hargaHiddenValue = root.querySelector('input[wire\\:model\\.defer="harga"]')?.value || "0";
        const potonganHiddenValue = root.querySelector('input[wire\\:model\\.defer="potongan"]')?.value || "0";
        const hargaBersihHiddenValue = root.querySelector('input[wire\\:model\\.defer="harga_bersih"]')?.value || "0";

        if (hargaDisplay && hargaDisplay._cleave) {
            hargaDisplay._cleave.setRawValue(hargaHiddenValue);
        }

        if (potonganDisplay && potonganDisplay._cleave) {
            potonganDisplay._cleave.setRawValue(potonganHiddenValue);
        }

        if (hargaBersihDisplay && hargaBersihDisplay._cleave) {
            hargaBersihDisplay._cleave.setRawValue(hargaBersihHiddenValue);
        }
    }

    function reinitEditBundlingListeners() {
        const root = document.querySelector('#modalEditBundling');

        const hargaInput = root.querySelector('input[wire\\:model\\.defer="harga"]')?.previousElementSibling;
        const potonganInput = root.querySelector('input[wire\\:model\\.defer="potongan"]')?.previousElementSibling;
        const diskonInput = root.querySelector('input[wire\\:model\\.defer="diskon"]');

        [hargaInput, potonganInput, diskonInput].forEach(el => {
            if (el) {
                el.removeEventListener('input', hitungHargaBersihEditBundling);
                el.addEventListener('input', hitungHargaBersihEditBundling);
            }
        });

        hitungHargaBersihEditBundling();
    }

    function reinitEditBundlingModalHelpers() {
        initCleaveRupiah();
        isiAwalHargaBundlingEdit();
        reinitEditBundlingListeners();
    }

    document.addEventListener('DOMContentLoaded', reinitEditBundlingModalHelpers);
    document.addEventListener('livewire:load', () => {
        Livewire.hook('message.processed', reinitEditBundlingModalHelpers);
    });
    document.addEventListener('livewire:navigated', reinitEditBundlingModalHelpers);
</script>

</dialog>
