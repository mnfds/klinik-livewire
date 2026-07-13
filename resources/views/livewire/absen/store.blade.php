<dialog id="storeAbsen" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestoreAbsen', () => {
        document.getElementById('storeAbsen')?.close()
    })
    ">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Tambah Absen</h3>
        <form wire:submit.prevent="store" class="space-y-4">

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="label font-medium">Staff<span class="text-error">*</span></label>
                    <select wire:model.defer="user_id" class="select select-bordered w-full @error('user_id') input-error @enderror">
                        <option value="">Pilih Staff</option>
                        @foreach ($staff as $user)
                            <option value="{{ $user->id }}">{{ $user->biodata?->nama_lengkap }}</option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <span class="text-error">Mohon Memilih Staff Dengan Benar</span>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label class="label font-medium">Tanggal Absen<span class="text-error">*</span></label>
                    <input type="date" class="input input-bordered w-full @error('tanggal_absen') input-error @enderror" wire:model.lazy="tanggal_absen">
                    @error('tanggal_absen')
                        <span class="text-error">Tanggal Invalid</span>
                    @enderror
                </div>
    
                <div>
                    <label class="label font-semibold">Absen Masuk <span class="text-error">*</span></label>
                    <input type="time" class="input input-bordered w-full @error('jam_masuk') input-error @enderror" wire:model.lazy="jam_masuk">
                    @error('jam_masuk')
                        <span class="text-error">Mohon Mengisi Absen Masuk</span>
                    @enderror
                </div>

                <div>
                    <label class="label font-semibold">Absen Pulang </label>
                    <input type="time" class="input input-bordered w-full" wire:model.lazy="jam_pulang">
                </div>
    
                <div class="col-span-2">
                    <label class="label font-medium">Keterangan</label>
                    <textarea wire:model.lazy="keterangan" class="textarea textarea-bordered w-full" rows="3"></textarea>
                </div>
    
            </div>

            <div class="modal-action justify-end pt-4">
                @can('akses', 'Jadwal')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('storeAbsen').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>