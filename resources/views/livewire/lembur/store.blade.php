<dialog id="storeModalLembur" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestoreModalLembur', () => {
        document.getElementById('storeModalLembur')?.close()
    })
    ">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Pengajuan Lembur</h3>

        <form wire:submit.prevent="store" class="space-y-4">
            <div>
                <label class="label font-medium">Karyawan</label>
                @php
                    $users = \App\Models\User::with(['biodata', 'role'])->get();
                @endphp
                <select class="select select-bordered w-full" wire:model.lazy="user_id">
                    <option value="">Pilih Karyawan</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}">
                            {{ $u->biodata->nama_lengkap ?? $u->dokter->nama_dokter }}
                            ({{ $u->role->nama_role ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label class="label font-medium"><span class="label-text">Tanggal & Waktu Keluar</span></label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <input type="date" class="input input-bordered w-full" wire:model.lazy="tanggal_lembur" required >
                        <span class="text-xs text-gray-500 ml-1">Tanggal keluar</span>
                    </div>
                    <div>
                        <input type="time" class="input input-bordered w-full" wire:model.lazy="jam_mulai" required >
                        <span class="text-xs text-gray-500 ml-1">Jam keluar</span>
                    </div>
                </div>
            </div>
            
            <div>
                <label class="label font-medium">Keperluan</label>
                <textarea wire:model.lazy="keperluan" class="textarea textarea-bordered w-full" rows="3"></textarea>
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