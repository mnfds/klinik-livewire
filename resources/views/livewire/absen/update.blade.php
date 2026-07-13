<dialog id="modalUpdateAbsen" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closemodalUpdateAbsen', () => {
        document.getElementById('modalUpdateAbsen')?.close()
    })
">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Edit Data Absen "{{ $nama }}"</h3>

        <form wire:submit.prevent="update" class="space-y-4">

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="label font-medium">Tanggal Absen<span class="text-error">*</span></label>
                    <input type="date" class="input input-bordered w-full @error('tanggal_absen') input-error @enderror" wire:model.defer="tanggal_absen">
                    @error('tanggal_absen')
                        <span class="text-error">Tanggal Invalid</span>
                    @enderror
                </div>
    
                <div>
                    <label class="label font-semibold">Absen Masuk <span class="text-error">*</span></label>
                    <input type="time" class="input input-bordered w-full @error('jam_masuk') input-error @enderror" wire:model.defer="jam_masuk">
                    @error('jam_masuk')
                        <span class="text-error">Mohon Mengisi Absen Masuk</span>
                    @enderror
                </div>

                <div>
                    <label class="label font-semibold">Absen Pulang </label>
                    <input type="time" class="input input-bordered w-full" wire:model.defer="jam_pulang">
                </div>
    
                <div class="col-span-2">
                    <label class="label font-medium">Keterangan</label>
                    <textarea wire:model.defer="keterangan" class="textarea textarea-bordered w-full" rows="3"></textarea>
                </div>
    
            </div>

            <div class="modal-action justify-end pt-4">
                @can('akses', 'Jadwal')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('modalUpdateAbsen').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>