<dialog id="modaleditpelayanan" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeModal', () => {
        document.getElementById('modaleditpelayanan')?.close()
    })
">
    <div class="modal-box">
        <h3 class="font-bold text-lg mb-2">Edit Pelayanan</h3>

        <form wire:submit.prevent="update">
            <div class="form-control mb-2">
                <label class="label">Nama Pelayanan</label>
                <input type="text" class="input input-bordered" wire:model="nama_pelayanan">
            </div>
            <div class="form-control mb-2">
                <label class="label">Harga Pelayanan</label>
                <input type="number" class="input input-bordered" wire:model="harga_pelayanan">
            </div>
            <div class="form-control mb-2">
                <label class="label">Deskripsi</label>
                <input type="text" class="input input-bordered" wire:model="deskripsi">
            </div>
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</dialog>