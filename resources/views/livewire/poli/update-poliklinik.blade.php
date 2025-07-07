<dialog id="modaleditpoli" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeModal', () => {
        document.getElementById('modaleditpoli')?.close()
    })
">
    <div class="modal-box">
        <h3 class="font-bold text-lg mb-2">Edit Poliklinik</h3>

        <form wire:submit.prevent="update">
            <div class="form-control mb-2">
                <label class="label">Nama Poli</label>
                <input type="text" class="input input-bordered" wire:model="nama_poli">
            </div>
            <div class="form-control mb-2">
                <label class="label">Kode Poli</label>
                <input type="text" class="input input-bordered" wire:model="kode">
            </div>
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</dialog>