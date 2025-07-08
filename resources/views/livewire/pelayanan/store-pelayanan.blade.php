<dialog id="storeModalPelayanan" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeStoreModalPelayanan', () => {
        document.getElementById('storeModalPelayanan')?.close()
    })
">
    <div class="modal-box">
        <h3 class="text-lg font-bold">Tambah Pelayanan</h3>
        <form wire:submit.prevent="store">

            {{-- Nama Pelayanan --}}
            <div class="form-control mb-2">
                <label class="label">Nama Pelayanan</label>
                <input type="text" class="input input-bordered" wire:model.lazy="nama_pelayanan">
            </div>

            {{-- Harga Pelayanan --}}
            <div class="form-control mb-2">
                <label class="label">Harga Dasar</label>
                <input type="number" class="input input-bordered" wire:model.lazy="harga_pelayanan">
            </div>

            {{-- Diskon (Persen) --}}
            <div class="form-control mb-2">
                <label class="label">Diskon (%)</label>
                <input type="number" min="0" max="100" class="input input-bordered" wire:model.lazy="diskon">
            </div>

            {{-- Harga Bersih (Tampilan saja) --}}
            <div class="form-control mb-2">
                <label class="label">Harga Bersih (setelah diskon)</label>
                <input type="text" class="input input-bordered bg-base-200" value="Rp {{ number_format((float)$harga_pelayanan - ((float)$harga_pelayanan * (float)$diskon / 100), 0, ',', '.') }}" readonly>
            </div>

            {{-- Deskripsi --}}
            <div class="form-control mb-2">
                <label class="label">Deskripsi</label>
                <input type="text" class="input input-bordered" wire:model.lazy="deskripsi">
            </div>

            {{-- Tombol --}}
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn" onclick="document.getElementById('storeModalPelayanan').close()">Batal</button>
            </div>

        </form>
    </div>
</dialog>