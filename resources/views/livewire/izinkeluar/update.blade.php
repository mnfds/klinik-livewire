<div>
    {{-- MODAL EDIT IZIN DISETUJUI --}}
    <dialog id="modaleditizindisetujui" class="modal" wire:ignore.self x-data x-init="
        Livewire.on('closemodaleditizindisetujui', () => {
            document.getElementById('modaleditizindisetujui')?.close()
        })
        ">
        <div class="modal-box w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Edit Pengajuan Keluar</h3>
    
            <form wire:submit.prevent="updateDisetujui" class="space-y-4">
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
                    <label class="label font-medium"><span class="label-text">Tanggal & Waktu Keluar<span class="text-error">*</span></span></label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <input type="date" class="input input-bordered w-full @error('tanggal_izin') input-error @enderror" wire:model.defer="tanggal_izin">
                            <span class="text-xs text-gray-500 ml-1">Tanggal keluar</span><br>
                            @error('tanggal_izin')
                                <span class="text-error text-sm">
                                    Mohon Mengisi Tanggal Izin Keluar Dengan Benar
                                </span>
                            @enderror
                        </div>
                        <div>
                            <input type="time" class="input input-bordered w-full @error('jam_keluar') input-error @enderror" wire:model.defer="jam_keluar">
                            <span class="text-xs text-gray-500 ml-1">Jam keluar</span><br>
                            @error('jam_keluar')
                                <span class="text-error text-sm">
                                    Mohon Mengisi Jam Keluar Dengan Benar
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label class="label font-medium">Keperluan<span class="text-error">*</span></label>
                    <textarea wire:model.defer="keperluan" class="textarea textarea-bordered w-full @error('keperluan') input-error @enderror" rows="3"></textarea>
                    @error('tanggal_izin')
                        <span class="text-error text-sm">
                            Mohon Mengisi Keperluan Yang Dilakukan Hingga Perlu Izin Keluar
                        </span>
                    @enderror
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
            <h3 class="text-xl font-semibold mb-4">Edit Pengajuan Keluar</h3>
    
            <form wire:submit.prevent="updateSelesai" class="space-y-4">
                <div>
                    <label class="label font-medium">Karyawan<span class="text-error">*</span></label>
                    @php
                        $users = \App\Models\User::with(['biodata', 'role'])->get();
                    @endphp
                    <select class="select select-bordered w-full @error('user_id') input-error @enderror" wire:model.defer="user_id">
                        <option value="">Pilih karyawan</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">
                                {{ $u->biodata->nama_lengkap ?? $u->dokter->nama_dokter }}
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
                    <label class="label font-medium">
                        <span class="label-text">Tanggal & Waktu<span class="text-error">*</span></span>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <input type="date" class="input input-bordered w-full @error('tanggal_izin') input-error @enderror" wire:model.defer="tanggal_izin">
                            <span class="text-xs text-gray-500 ml-1">Tanggal</span><br>
                            @error('tanggal_izin')
                                <span class="text-error text-sm">
                                    Mohon Mengisi Tanggal Izin Keluar Dengan Benar
                                </span>
                            @enderror
                        </div>
                        <div>
                            <input type="time" class="input input-bordered w-full @error('jam_keluar') input-error @enderror" wire:model.defer="jam_keluar">
                            <span class="text-xs text-gray-500 ml-1">Jam keluar</span><br>
                            @error('jam_keluar')
                                <span class="text-error text-sm">
                                    Mohon Mengisi Jam Keluar Dengan Benar
                                </span>
                            @enderror
                        </div>
                        <div>
                            <input type="time" class="input input-bordered w-full @error('jam_kembali') input-error @enderror" wire:model.defer="jam_kembali">
                            <span class="text-xs text-gray-500 ml-1">Jam kembali</span><br>
                            @error('jam_kembali')
                                <span class="text-error text-sm">
                                    Mohon Mengisi Jam Kembali Dengan Benar
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
                            Mohon Mengisi Keperluan Yang Dilakukan Hingga Perlu Izin Keluar
                        </span>
                    @enderror
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