<div>
    {{-- MODAL PENDING --}}
    <dialog id="modalPendingUpdate" class="modal" wire:ignore.self x-data x-init="
        Livewire.on('closemodalPendingUpdate', () => {
            document.getElementById('modalPendingUpdate')?.close()
        })
        ">
        <div class="modal-box w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Edit Pengajuan Lembur</h3>
    
            <form wire:submit.prevent="updatePending" class="space-y-4">
                <div>
                    <label class="label font-medium">Karyawan</label>
                    @php
                        $users = \App\Models\User::with(['biodata', 'role'])->get();
                    @endphp
                    <select class="select select-bordered w-full" wire:model.defer="user_id">
                        <option value="">Pilih Karyawan</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">
                                {{ $u->biodata->nama_lengkap ?? $u->dokter->nama_dokter }}
                                {{ $u->role->nama_role ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label font-medium">Tanggal Dan Waktu Lembur</label>
                    <input type="date" class="input input-bordered w-full" wire:model.defer="tanggal_lembur" required>
                    <input type="time" class="input input-bordered w-full" wire:model.defer="jam_mulai" required>
                </div>

                <div>
                    <label class="label font-medium">Keperluan</label>
                    <textarea wire:model.defer="keperluan" class="textarea textarea-bordered w-full" rows="3"></textarea>
                </div>
    
                <div class="modal-action justify-end pt-4">
                    @can('akses', 'Pengajuan Lembur Edit')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    @endcan
                    <button type="button" class="btn btn-error" onclick="document.getElementById('modalPendingUpdate').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>
    {{-- MODAL EDIT PENDING --}}

    {{-- MODAL EDIT APPROVE --}}
    <dialog id="modalApproveUpdate" class="modal" wire:ignore.self x-data x-init="
        Livewire.on('closemodalApproveUpdate', () => {
            document.getElementById('modalApproveUpdate')?.close()
        })
        ">
        <div class="modal-box w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Edit Pengajuan Lembur</h3>
    
            <form wire:submit.prevent="updateApprove" class="space-y-4">
                <div>
                    <label class="label font-medium">Karyawan</label>
                    @php
                        $users = \App\Models\User::with(['biodata', 'role'])->get();
                    @endphp
                    <select class="select select-bordered w-full" wire:model.defer="user_id">
                        <option value="">Pilih Karyawan</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">
                                {{ $u->biodata->nama_lengkap ?? $u->dokter->nama_dokter }}
                                {{ $u->role->nama_role ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label font-medium">Tanggal Dan Waktu Lembur</label>
                    <input type="date" class="input input-bordered w-full" wire:model.defer="tanggal_lembur" required>
                    <input type="time" class="input input-bordered w-full" wire:model.defer="jam_mulai" required>
                </div>

                <div>
                    <label class="label font-medium">Keperluan</label>
                    <textarea wire:model.defer="keperluan" class="textarea textarea-bordered w-full" rows="3"></textarea>
                </div>
    
                <div class="modal-action justify-end pt-4">
                    @can('akses', 'Pengajuan Lembur Edit')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    @endcan
                    <button type="button" class="btn btn-error" onclick="document.getElementById('modalApproveUpdate').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>
    {{-- MODAL EDIT APPROVE --}}

    {{-- MODAL EDIT HISTORY --}}
    <dialog id="modalHistoryUpdate" class="modal" wire:ignore.self x-data x-init="
        Livewire.on('closemodalHistoryUpdate', () => {
            document.getElementById('modalHistoryUpdate')?.close()
        })
        ">
        <div class="modal-box w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Edit Pengajuan Lembur</h3>
    
            <form wire:submit.prevent="updateHistory" class="space-y-4">
                <div>
                    <label class="label font-medium">Karyawan</label>
                    @php
                        $users = \App\Models\User::with(['biodata', 'role'])->get();
                    @endphp
                    <select class="select select-bordered w-full" wire:model.defer="user_id">
                        <option value="">Pilih Karyawan</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">
                                {{ $u->biodata->nama_lengkap ?? $u->dokter->nama_dokter }}
                                {{ $u->role->nama_role ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label font-medium">Tanggal Dan Waktu Lembur</label>
                    <input type="date" class="input input-bordered w-full" wire:model.defer="tanggal_lembur" required>
                    <input type="time" class="input input-bordered w-full" wire:model.defer="jam_mulai" required>
                    <input type="time" class="input input-bordered w-full" wire:model.defer="jam_selesai" required>
                </div>

                <div>
                    <label class="label font-medium">Keperluan</label>
                    <textarea wire:model.defer="keperluan" class="textarea textarea-bordered w-full" rows="3"></textarea>
                </div>
    
                <div class="modal-action justify-end pt-4">
                    @can('akses', 'Pengajuan Lembur Edit')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    @endcan
                    <button type="button" class="btn btn-error" onclick="document.getElementById('modalHistoryUpdate').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>
    {{-- MODAL EDIT HISTORY --}}
</div>
