<dialog id="storeModalLembur" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestoreModalLembur', () => {
        document.getElementById('storeModalLembur')?.close()
    })
    ">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Pengajuan Lembur</h3>

        <form wire:submit.prevent="store" class="space-y-4">
            <div>
                <label class="label font-medium">Karyawan<span class="text-error">*</span></label>
                <select class="select select-bordered w-full @error('user_id') input-error @enderror" wire:model.lazy="user_id">
                    <option value="">Pilih Karyawan</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}">
                            {{ $u->biodata->nama_lengkap ?? $u->dokter->nama_dokter ?? '-' }}
                            ({{ $u->role->nama_role ?? '-' }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <span class="text-error text-sm">
                        Mohon Memilih Karyawan Dengan Benar
                    </span>
                @enderror
            </div>

            <div class="space-y-2">
                <label class="label font-medium"><span class="label-text">Tanggal & Waktu Lembur<span class="text-error">*</span></span></label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <input type="date" class="input input-bordered w-full @error('tanggal_lembur') input-error @enderror" wire:model.lazy="tanggal_lembur" >
                        <span class="text-xs text-gray-500 ml-1">Tanggal Lembur</span><br>
                        @error('tanggal_lembur')
                            <span class="text-error text-sm">
                                Mohon Mengisi Tanggal Lembur Dengan Benar
                            </span>
                        @enderror
                    </div>
                    <div>
                        <input type="time" class="input input-bordered w-full @error('jam_mulai') input-error @enderror" wire:model.lazy="jam_mulai" >
                        <span class="text-xs text-gray-500 ml-1">Jam Mulai Lembur</span><br>
                        @error('jam_mulai')
                            <span class="text-error text-sm">
                                Mohon Mengisi Jam Mulai Lembur Dengan Benar
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div>
                <label class="label font-medium">Keperluan<span class="text-error">*</span></label>
                <textarea wire:model.lazy="keperluan" class="textarea textarea-bordered w-full @error('keperluan') input-error @enderror" rows="3"></textarea>
                @error('keperluan')
                    <span class="text-error text-sm">
                        Mohon Mengisi Keperluan Yang Dilakukan Hingga Lembur
                    </span>
                @enderror
            </div>

            <div class="modal-action justify-end pt-4">
                @can('akses', 'Pengajuan Lembur Tambah')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('storeModalLembur').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>