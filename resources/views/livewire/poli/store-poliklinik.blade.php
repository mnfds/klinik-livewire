<dialog id="storeModalPoli" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestoreModalPoli', () => {
        document.getElementById('storeModalPoli')?.close()
    })
">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Tambah Poliklinik</h3>

        <form wire:submit.prevent="store" class="space-y-4">

            <div>
                <label class="label font-medium">Nama Poli <span class="text-error">*</span></label>
                <input type="text" class="input input-bordered w-full @error('nama_poli') input-error @enderror" wire:model.lazy="nama_poli">
                @error('nama_poli')
                    <span class="text-error text-sm mt-1">
                        Mohon Mengisi Nama Poliklinik
                    </span>
                @enderror
            </div>

            <div>
                <label class="label font-medium">Kode Poli <span class="text-error">*</span></label>
                <input type="text" class="input input-bordered w-full @error('kode') input-error @enderror" wire:model.lazy="kode">
                @error('kode')
                    <span class="text-error text-sm mt-1">
                        Mohon Mengisi Kode Poliklinik
                    </span>
                @enderror
            </div>

            <div class="modal-action justify-end pt-4">
                @can('akses', 'Poliklinik Tambah')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-neutral" onclick="document.getElementById('storeModalPoli').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>
