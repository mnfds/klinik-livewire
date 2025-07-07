<dialog id="storeModalPoli" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestoreModalPoli', () => {
        document.getElementById('storeModalPoli')?.close()
    })
">
    <div class="modal-box">
        <h3 class="text-lg font-bold">Tambah Poliklinik</h3>

        <form wire:submit.prevent="store">

            <div class="form-control mb-2">
                <label class="label">Nama Poli</label>
                <input type="text" class="input input-bordered" wire:model.lazy="nama_poli" required>
            </div>
            <div class="form-control mb-2">
                <label class="label">Kode Poli</label>
                <input type="text" class="input input-bordered" wire:model.lazy="kode">
            </div>
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn" onclick="document.getElementById('storeModalPoli').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>
