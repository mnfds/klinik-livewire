<dialog id="modalEditBundling" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('openModalEditBundling', () => {
        document.getElementById('modalEditBundling')?.showModal()
    });
    Livewire.on('closeModalEditBundling', () => {
        document.getElementById('modalEditBundling')?.close()
    });
">
    <div class="modal-box max-w-3xl">
        <h3 class="font-bold text-lg mb-4">Edit Paket Bundling</h3>

        <form wire:submit.prevent="update" class="space-y-4">

            {{-- Nama Bundling --}}
            <div class="form-control">
                <label class="label font-semibold">Nama Bundling</label>
                <input type="text" class="input input-bordered" wire:model.defer="nama" required>
            </div>

            {{-- Deskripsi --}}
            <div class="form-control">
                <label class="label font-semibold">Deskripsi</label>
                <input type="text" class="input input-bordered" wire:model.defer="deskripsi">
            </div>

            {{-- Harga --}}
            <div class="form-control">
                <label class="label font-semibold">Harga</label>
                <input type="text" class="input input-bordered" id="harga_edit_input"
                       oninput="formatRupiahToHidden(this, 'harga_edit_hidden')"
                       value="{{ number_format($harga ?? 0, 0, ',', '.') }}"
                       inputmode="numeric">
                <input type="hidden" wire:model="harga" id="harga_edit_hidden">
            </div>

            {{-- Diskon --}}
            <div class="form-control">
                <label class="label font-semibold">Diskon (%)</label>
                <input type="number" class="input input-bordered" wire:model="diskon" min="0" max="100">
            </div>

            {{-- Harga Bersih --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Bersih</label>
                <input type="text" class="input input-bordered bg-base-200"
                       value="Rp {{ number_format(($harga ?? 0) - (($harga ?? 0) * ($diskon ?? 0) / 100), 0, ',', '.') }}"
                       readonly>
            </div>

            <div class="form-control">
                <label class="label font-semibold">Pelayanan</label>
                <select class="select select-bordered" multiple wire:model="selectedPelayananIds">
                    @foreach($listPelayanan as $pelayanan)
                        <option value="{{ $pelayanan->id }}">{{ $pelayanan->nama_pelayanan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-control">
                <label class="label font-semibold">Produk & Obat</label>
                <select class="select select-bordered" multiple wire:model="selectedProdukIds">
                    @foreach($listProduk as $produk)
                        <option value="{{ $produk->id }}">{{ $produk->nama_dagang }}</option>
                    @endforeach
                </select>
            </div>

            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn" onclick="modalEditBundling.close()">Batal</button>
            </div>
        </form>
    </div>

    {{-- Script Format Harga --}}
    <script>
        function formatRupiahToHidden(inputEl, hiddenId) {
            const raw = inputEl.value.replace(/[^\d]/g, '');
            const formatted = new Intl.NumberFormat('id-ID').format(raw);

            inputEl.value = formatted;
            const hidden = document.getElementById(hiddenId);
            hidden.value = raw;
            hidden.dispatchEvent(new Event('input')); // untuk Livewire update
        }
    </script>
</dialog>
