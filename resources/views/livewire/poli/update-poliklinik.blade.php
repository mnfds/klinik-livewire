<dialog id="modaleditpoli" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeModal', () => {
        document.getElementById('modaleditpoli')?.close()
    })
">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Edit Poliklinik</h3>

        <form wire:submit.prevent="update" class="space-y-4">
            <div>
                <label class="label font-medium">Nama Poli</label>
                <input type="text" class="input input-bordered w-full" wire:model="nama_poli">
            </div>

            <div>
                <label class="label font-medium">Kode Poli</label>
                <input type="text" class="input input-bordered w-full" wire:model="kode">
            </div>

            <div class="modal-action justify-end pt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-neutral" onclick="document.getElementById('modaleditpoli').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>
