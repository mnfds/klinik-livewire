<div>
    <dialog id="modaleditdokumen" class="modal" wire:ignore.self x-data x-init="
        Livewire.on('closemodaleditdokumen', () => {
            document.getElementById('modaleditdokumen')?.close()
        })
        ">
        <div class="modal-box w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Edit Dokumen</h3>
    
            <form wire:submit.prevent="update" class="space-y-4">
                <div>
                    <label class="label font-medium">Nama Dokumen<span class="text-error">*</span></label>
                    <input type="text" class="input input-bordered w-full @error('nama') input-error @enderror" wire:model.defer="nama">
                    @error('nama')
                        <span class="text-error text-sm">Mohon Mengisi Nama Dokumen Dengan Benar</span>
                    @enderror
                </div>

                <div>
                    <label class="label font-medium">Lembaga Terkait</label>
                    <input type="text" class="input input-bordered w-full" wire:model.defer="lembaga">
                </div>

                <div class="space-y-2">
                    <label class="label font-medium">Tanggal Kadaluarsa<span class="text-error">*</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <input type="date" class="input input-bordered w-full @error('tanggal_berlaku') input-error @enderror" wire:model.defer="tanggal_berlaku" >
                            <span class="text-xs text-gray-500 ml-1">Tanggal Aktif</span><br>
                            @error('tanggal_berlaku')
                                <span class="text-error text-sm">
                                    Mohon Mengisi Tanggal Berlaku/Aktif Dengan Benar
                                </span>
                            @enderror
                        </div>
                        <div>
                            <input type="date" class="input input-bordered w-full @error('tanggal_tidak_berlaku') input-error @enderror" wire:model.defer="tanggal_tidak_berlaku" >
                            <span class="text-xs text-gray-500 ml-1">Tanggal Nonaktif</span><br>
                            @error('tanggal_tidak_berlaku')
                                <span class="text-error text-sm">
                                    Mohon Mengisi Tanggal Tidak Aktif/Nonaktif Dengan Benar
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label class="label font-semibold">Reminder (bulan)</label>
                    <input type="number" class="input input-bordered w-full" wire:model.lazy="reminder" min="0">
                </div>

                <div>
                    <label class="label font-medium">Keterangan</label>
                    <textarea wire:model.defer="keterangan" class="textarea textarea-bordered w-full" rows="3"></textarea>
                </div>

                <div class="modal-action justify-end pt-4">
                    @can('akses', 'Dokumen Edit')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    @endcan
                    <button type="button" class="btn btn-error" onclick="document.getElementById('modaleditdokumen').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>
</div>