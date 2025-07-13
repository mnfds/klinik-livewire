<dialog id="storeModalPoli" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestoreModalPoli', () => {
        document.getElementById('storeModalPoli')?.close()
    })
">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Tambah Poliklinik</h3>

        <form wire:submit.prevent="store" class="space-y-4">

            <div>
                <label class="label font-medium">Nama Poli</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="nama_poli" required>
            </div>

            <div>
                <label class="label font-medium">Kode Poli</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="kode">
            </div>

            <div class="modal-action justify-end pt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-error" onclick="document.getElementById('storeModalPoli').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>
