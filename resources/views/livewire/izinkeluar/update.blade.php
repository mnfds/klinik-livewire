<div>
    {{-- MODAL EDIT IZIN DISETUJUI --}}
    <dialog id="modaleditizindisetujui" class="modal" wire:ignore.self x-data x-init="
        Livewire.on('closemodaleditizindisetujui', () => {
            document.getElementById('modaleditizindisetujui')?.close()
        })
        ">
        <div class="modal-box w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Edit Izin Keluar</h3>
    
            <form wire:submit.prevent="updateDisetujui" class="space-y-4">
                <div>
                    <label class="label font-medium">Staff Yang Izin Keluar</label>
                    @php
                        $users = \App\Models\User::with(['biodata', 'role'])->get();
                    @endphp
                    <select class="select select-bordered w-full" wire:model.defer="user_id">
                        <option value="">Pilih Staff</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">
                                {{ $u->biodata->nama_lengkap ?? $u->dokter->nama_dokter }}
                                {{ $u->role->nama_role ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label font-medium">Tanggal Dan Waktu Keluar</label>
                    <input type="date" class="input input-bordered w-full" wire:model.defer="tanggal_izin" required>
                    <input type="time" class="input input-bordered w-full" wire:model.defer="jam_keluar" required>
                </div>

                <div>
                    <label class="label font-medium">Keperluan</label>
                    <textarea wire:model.defer="keperluan" class="textarea textarea-bordered w-full" rows="3"></textarea>
                </div>
    
                <div class="modal-action justify-end pt-4">
                    @can('akses', 'Pengajuan Izin Keluar Edit')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    @endcan
                    <button type="button" class="btn btn-error" onclick="document.getElementById('modaleditizindisetujui').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>
    {{-- MODAL EDIT IZIN DISETUJUI --}}

    {{-- MODAL EDIT SELESAI --}}
    <dialog id="modaleditizinselesai" class="modal" wire:ignore.self x-data x-init="
        Livewire.on('closemodaleditizinselesai', () => {
            document.getElementById('modaleditizinselesai')?.close()
        })
        ">
        <div class="modal-box w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Edit Riwayat Izin Keluar</h3>
    
            <form wire:submit.prevent="updateSelesai" class="space-y-4">
                <div>
                    <label class="label font-medium">Staff Yang Izin Keluar</label>
                    @php
                        $users = \App\Models\User::with(['biodata', 'role'])->get();
                    @endphp
                    <select class="select select-bordered w-full" wire:model.defer="user_id">
                        <option value="">Pilih Staff</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">
                                {{ $u->biodata->nama_lengkap ?? $u->dokter->nama_dokter }}
                                ({{ $u->role->nama_role ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label font-medium">Tanggal Dan Waktu</label>
                    <input type="date" class="input input-bordered w-full" wire:model.defer="tanggal_izin" required>
                    <input type="time" class="input input-bordered w-full" wire:model.defer="jam_keluar" required>
                    <input type="time" class="input input-bordered w-full" wire:model.defer="jam_kembali" required>
                </div>

                <div>
                    <label class="label font-medium">Keperluan</label>
                    <textarea wire:model.defer="keperluan" class="textarea textarea-bordered w-full" rows="3"></textarea>
                </div>

                <div class="modal-action justify-end pt-4">
                    @can('akses', 'Pengajuan Riwayat Izin Keluar Edit')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    @endcan
                    <button type="button" class="btn btn-error" onclick="document.getElementById('modaleditizinselesai').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>
    {{-- MODAL EDIT SELESAI --}}
</div>