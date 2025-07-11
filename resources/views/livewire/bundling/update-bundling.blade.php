<dialog id="modalEditBundling" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('openModalEditBundling', () => {
        document.getElementById('modalEditBundling')?.showModal()
        reinitEditBundlingModalHelpers()
    })
    Livewire.on('closeModalEditBundling', () => {
        document.getElementById('modalEditBundling')?.close()
    })
">
    <div class="modal-box max-w-4xl">
        <h3 class="font-bold text-lg mb-4">Edit Bundling</h3>
        <form wire:submit.prevent="update" class="space-y-4">

            {{-- Nama --}}
            <div class="form-control">
                <label class="label font-semibold">Nama Bundling</label>
                <input type="text" class="input input-bordered" wire:model.defer="nama" required>
            </div>

            {{-- Deskripsi --}}
            <div class="form-control">
                <label class="label font-semibold">Deskripsi</label>
                <textarea class="textarea textarea-bordered" wire:model.defer="deskripsi" rows="2"></textarea>
            </div>

            {{-- Harga --}}
            <div class="form-control">
                <label class="label font-semibold">Harga (Sebelum Diskon)</label>
                <input type="text" class="input input-bordered input-rupiah" placeholder="Rp 0">
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga">
            </div>

            {{-- Diskon --}}
            <div class="form-control">
                <label class="label font-semibold">Diskon (%)</label>
                <input type="number" class="input input-bordered" wire:model.defer="diskon" min="0" max="100">
            </div>

            {{-- Harga Bersih --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Bersih (Setelah Diskon)</label>
                <input type="text" class="input input-bordered input-rupiah bg-gray-100" readonly>
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga_bersih">
            </div>

            {{-- Pelayanan --}}
            <div class="form-control">
                <label class="label font-semibold">Pelayanan</label>
                @foreach ($pelayananInputs as $index => $row)
                    <div class="flex gap-2 mb-2">
                        <select class="select select-bordered w-full" wire:model.defer="pelayananInputs.{{ $index }}.pelayanan_id">
                            <option value="">Pilih Pelayanan</option>
                            @foreach ($pelayananList as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_pelayanan }}</option>
                            @endforeach
                        </select>
                        <input type="number" class="input input-bordered w-24" min="1"
                               wire:model.defer="pelayananInputs.{{ $index }}.jumlah" placeholder="Jumlah">
                        <button type="button" class="btn btn-error" wire:click="removePelayananRow({{ $index }})">
                            ✕
                        </button>
                    </div>
                @endforeach
                <button type="button" class="btn btn-sm btn-outline" wire:click="addPelayananRow">+ Tambah Pelayanan</button>
            </div>

            {{-- Produk & Obat --}}
            <div class="form-control">
                <label class="label font-semibold">Produk & Obat</label>
                @foreach ($produkInputs as $index => $row)
                    <div class="flex gap-2 mb-2">
                        <select class="select select-bordered w-full" wire:model.defer="produkInputs.{{ $index }}.produk_id">
                            <option value="">Pilih Produk / Obat</option>
                            @foreach ($produkObatList as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_dagang }}</option>
                            @endforeach
                        </select>
                        <input type="number" class="input input-bordered w-24" min="1"
                               wire:model.defer="produkInputs.{{ $index }}.jumlah" placeholder="Jumlah">
                        <button type="button" class="btn btn-error" wire:click="removeProdukRow({{ $index }})">
                            ✕
                        </button>
                    </div>
                @endforeach
                <button type="button" class="btn btn-sm btn-outline" wire:click="addProdukRow">+ Tambah Produk</button>
            </div>

            {{-- Tombol Aksi --}}
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn" onclick="modalEditBundling?.close()">Batal</button>
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
            hitungHargaBersihEdit();

            document.querySelector('#modalEditBundling input[wire\\:model\\.defer="harga"]')?.addEventListener('input', hitungHargaBersihEdit);
            document.querySelector('#modalEditBundling input[wire\\:model\\.defer="diskon"]')?.addEventListener('input', hitungHargaBersihEdit);
        }

        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', reinitEditBundlingModalHelpers);
        });

        document.addEventListener('livewire:navigated', reinitEditBundlingModalHelpers);
    </script>
</dialog>
