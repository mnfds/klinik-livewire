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
                    <label class="label font-medium">Karyawan<span class="text-error">*</span></label>
                    @php
                        $users = \App\Models\User::with(['biodata', 'role'])->get();
                    @endphp
                    <select class="select select-bordered w-full @error('user_id') input-error @enderror" wire:model.defer="user_id">
                        <option value="">Pilih Karyawan</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">
                                {{ $u->biodata->nama_lengkap ?? $u->dokter->nama_dokter ?? '-'}}
                                {{ $u->role->nama_role ?? '-' }})
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
                            <input type="date" class="input input-bordered w-full @error('tanggal_lembur') input-error @enderror" wire:model.defer="tanggal_lembur" >
                            <span class="text-xs text-gray-500 ml-1">Tanggal Lembur</span><br>
                            @error('tanggal_lembur')
                                <span class="text-error text-sm">
                                    Mohon Mengisi Tanggal Lembur Dengan Benar
                                </span>
                            @enderror
                        </div>
                        <div>
                            <input type="time" class="input input-bordered w-full @error('jam_mulai') input-error @enderror" wire:model.defer="jam_mulai" >
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
                    <textarea wire:model.defer="keperluan" class="textarea textarea-bordered w-full @error('keperluan') input-error @enderror" rows="3"></textarea>
                    @error('keperluan')
                        <span class="text-error text-sm">
                            Mohon Mengisi Keperluan Yang Dilakukan Hingga Lembur
                        </span>
                    @enderror
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
                    <label class="label font-medium">Karyawan<span class="text-error">*</span></label>
                    @php
                        $users = \App\Models\User::with(['biodata', 'role'])->get();
                    @endphp
                    <select class="select select-bordered w-full @error('user_id') input-error @enderror" wire:model.defer="user_id">
                        <option value="">Pilih Karyawan</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">
                                {{ $u->biodata->nama_lengkap ?? $u->dokter->nama_dokter }}
                                {{ $u->role->nama_role ?? '-' }})
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
                            <input type="date" class="input input-bordered w-full @error('tanggal_lembur') input-error @enderror" wire:model.defer="tanggal_lembur" >
                            <span class="text-xs text-gray-500 ml-1">Tanggal Lembur</span><br>
                            @error('tanggal_lembur')
                                <span class="text-error text-sm">
                                    Mohon Mengisi Tanggal Lembur Dengan Benar
                                </span>
                            @enderror
                        </div>
                        <div>
                            <input type="time" class="input input-bordered w-full @error('jam_mulai') input-error @enderror" wire:model.defer="jam_mulai" >
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
                    <textarea wire:model.defer="keperluan" class="textarea textarea-bordered w-full @error('keperluan') input-error @enderror" rows="3"></textarea>
                    @error('keperluan')
                        <span class="text-error text-sm">
                            Mohon Mengisi Keperluan Yang Dilakukan Hingga Lembur
                        </span>
                    @enderror
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
                    <label class="label font-medium">Karyawan<span class="text-error">*</span></label>
                    @php
                        $users = \App\Models\User::with(['biodata', 'role'])->get();
                    @endphp
                    <select class="select select-bordered w-full @error('user_id') input-error @enderror" wire:model.defer="user_id">
                        <option value="">Pilih Karyawan</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">
                                {{ $u->biodata->nama_lengkap ?? $u->dokter->nama_dokter }}
                                {{ $u->role->nama_role ?? '-' }})
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
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <input type="date" class="input input-bordered w-full @error('tanggal_lembur') input-error @enderror" wire:model.defer="tanggal_lembur">
                            <span class="text-xs text-gray-500 ml-1">Tanggal Lembur</span><br>
                            @error('tanggal_lembur')
                                <span class="text-error text-sm">
                                    Mohon Mengisi Tanggal Lembur Dengan Benar
                                </span>
                            @enderror
                        </div>
                        <div>
                            <input type="time" class="input input-bordered w-full @error('jam_mulai') input-error @enderror" wire:model.defer="jam_mulai">
                            <span class="text-xs text-gray-500 ml-1">Jam Mulai Lembur</span><br>
                            @error('jam_mulai')
                                <span class="text-error text-sm">
                                    Mohon Mengisi Jam Mulai Lembur Dengan Benar
                                </span>
                            @enderror
                        </div>
                        <div>
                            <input type="time" class="input input-bordered w-full @error('jam_selesai') input-error @enderror" wire:model.defer="jam_selesai">
                            <span class="text-xs text-gray-500 ml-1">Jam Selesai Lembur</span><br>
                            @error('jam_selesai')
                                <span class="text-error text-sm">
                                    Mohon Mengisi Jam Selesai Lembur Dengan Benar
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label class="label font-medium">Keperluan<span class="text-error">*</span></label>
                    <textarea wire:model.defer="keperluan" class="textarea textarea-bordered w-full @error('keperluan') input-error @enderror" rows="3"></textarea>
                    @error('keperluan')
                        <span class="text-error text-sm">
                            Mohon Mengisi Keperluan Yang Dilakukan Hingga Lembur
                        </span>
                    @enderror
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
