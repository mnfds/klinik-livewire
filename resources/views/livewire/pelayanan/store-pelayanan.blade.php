<dialog id="storeModalPelayanan" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeStoreModalPelayanan', () => {
        document.getElementById('storeModalPelayanan')?.close()
    })
">
    <div class="modal-box">
        <h3 class="text-lg font-bold">Tambah Pelayanan</h3>

        <form wire:submit.prevent="store">

            <div class="form-control mb-2">
                <label class="label">Nama Pelayanan</label>
                <input type="text" class="input input-bordered" wire:model.lazy="nama_pelayanan">
            </div>
            <div class="form-control mb-2">
                <label class="label">Harga Pelayanan</label>
                <input type="number" class="input input-bordered" wire:model.lazy="harga_pelayanan">
            </div>
            <div class="form-control mb-2">
                <label class="label">Deskripsi</label>
                <input type="text" class="input input-bordered" wire:model.lazy="deskripsi">
            </div>

            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn" onclick="document.getElementById('storeModalPelayanan').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>