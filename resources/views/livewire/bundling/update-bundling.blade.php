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
                <label class="label font-semibold">Nama Bundling</label>
                <input type="text" class="input input-bordered w-full" wire:model.defer="nama" required>
            </div>

            {{-- Deskripsi --}}
            <div class="form-control">
                <label class="label font-semibold">Deskripsi</label>
                <textarea class="textarea textarea-bordered w-full" wire:model.defer="deskripsi" rows="2"></textarea>
            </div>

            {{-- Harga & Diskon --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label font-semibold">Harga (Sebelum Diskon)</label>
                    <input type="text" id="display_harga_bundling" class="input input-bordered input-rupiah w-full" wire:model.defer="harga_show" placeholder="Rp 0">
                    <input type="hidden" wire:model.defer="harga" class="input-rupiah-hidden">
                </div>

                <div class="form-control">
                    <label class="label font-semibold">Diskon (%)</label>
                    <input type="number" class="input input-bordered w-full" wire:model.defer="diskon" min="0" max="100">
                </div>
            </div>

            {{-- Harga Bersih --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Bersih (Setelah Diskon)</label>
                <input type="text" id="display_harga_bersih_bundling" class="input input-bordered input-rupiah bg-base-200 w-full" wire:model.defer="harga_bersih_show" readonly placeholder="Otomatis terhitung">
                <input type="hidden" wire:model.defer="harga_bersih" class="input-rupiah-hidden">
            </div>

            {{-- Pelayanan Medis --}}
            <div class="form-control">
                <label class="label font-semibold">Pelayanan Medis</label>
                <div class="space-y-2">
                    @foreach ($pelayananInputs as $index => $row)
                        <div class="flex flex-col md:flex-row gap-2">
                            <select class="select select-bordered w-full md:flex-1" wire:model.defer="pelayananInputs.{{ $index }}.pelayanan_id">
                                <option value="">Pilih Pelayanan</option>
                                @foreach ($pelayananList as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_pelayanan }}</option>
                                @endforeach
                            </select>
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
                            <select class="select select-bordered w-full md:flex-1" wire:model.defer="treatmentInputs.{{ $index }}.treatments_id">
                                <option value="">Pilih Pelayanan Estetika</option>
                                @foreach ($treatmentList as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_treatment }}</option>
                                @endforeach
                            </select>
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
                            <select class="select select-bordered w-full md:flex-1" wire:model.defer="produkInputs.{{ $index }}.produk_id">
                                <option value="">Pilih Produk / Obat</option>
                                @foreach ($produkObatList as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_dagang }}</option>
                                @endforeach
                            </select>
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
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-neutral" onclick="modalEditBundling?.close()">Batal</button>
            </div>
        </form>
    </div>

    {{-- Script: Cleave & Hitung --}}
    <script>
        function hitungHargaBersihEdit() {
            const hargaInput = document.querySelector('#modalEditBundling input[wire\\:model\\.defer="harga"]');
            const diskonInput = document.querySelector('#modalEditBundling input[wire\\:model\\.defer="diskon"]');
            const hargaBersihInput = document.querySelector('#modalEditBundling input[wire\\:model\\.defer="harga_bersih"]');
            const hargaBersihDisplay = hargaBersihInput?.previousElementSibling;

            if (!hargaInput || !diskonInput || !hargaBersihInput || !hargaBersihDisplay) return;

            const harga = parseInt(hargaInput.value.replace(/\D/g, '') || 0);
            const diskon = parseFloat(diskonInput.value || 0);
            const hargaBersih = Math.max(0, Math.round(harga - (harga * (diskon / 100))));

            hargaBersihInput.value = hargaBersih;
            hargaBersihDisplay._cleave?.setRawValue(hargaBersih);
            hargaBersihInput.dispatchEvent(new Event('input'));
        }

        function reinitEditBundlingModalHelpers() {
            initCleaveRupiah();
            isiAwalHargaBundlingEdit();
            hitungHargaBersihEdit();

            document.querySelector('#modalEditBundling input[wire\\:model\\.defer="harga"]')?.addEventListener('input', hitungHargaBersihEdit);
            document.querySelector('#modalEditBundling input[wire\\:model\\.defer="diskon"]')?.addEventListener('input', hitungHargaBersihEdit);
        }

        function isiAwalHargaBundlingEdit() {
            const hargaDisplay = document.querySelector('#display_harga_bundling');
            const hargaBersihDisplay = document.querySelector('#display_harga_bersih_bundling');

            const hargaHiddenValue = document.querySelector('input[wire\\:model\\.defer="harga"]')?.value || "0";
            const hargaBersihHiddenValue = document.querySelector('input[wire\\:model\\.defer="harga_bersih"]')?.value || "0";

            if (hargaDisplay && hargaDisplay._cleave) {
                hargaDisplay._cleave.setRawValue(hargaHiddenValue);
            }

            if (hargaBersihDisplay && hargaBersihDisplay._cleave) {
                hargaBersihDisplay._cleave.setRawValue(hargaBersihHiddenValue);
            }
        }

        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', reinitEditBundlingModalHelpers);
        });

        document.addEventListener('livewire:navigated', reinitEditBundlingModalHelpers);
    </script>
</dialog>
