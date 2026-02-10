<dialog id="storeModalIzinKeluar" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestoreModalIzinKeluar', () => {
        document.getElementById('storeModalIzinKeluar')?.close()
    })
    ">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Pengajuan Keluar Lokasi</h3>

        <form wire:submit.prevent="store" class="space-y-4">
            <div>
                <label class="label font-medium">Karyawan<span class="text-error">*</span></label>
                @php
                    $users = \App\Models\User::with(['biodata', 'role'])->get();
                @endphp
                <select class="select select-bordered w-full @error('user_id') input-error @enderror" wire:model.lazy="user_id">
                    <option value="">Pilih Karyawan</option>
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

            {{-- <div>
                <label class="label font-medium">Tanggal Dan Waktu Keluar</label>
                <input type="date" class="input input-bordered w-full" wire:model.lazy="tanggal_izin" required>
                <input type="time" class="input input-bordered w-full" wire:model.lazy="jam_keluar" required>
            </div> --}}

            <div>
                <label class="label font-medium">Keperluan<span class="text-error">*</span></label>
                <textarea wire:model.lazy="keperluan" class="textarea textarea-bordered w-full @error('keperluan') input-error @enderror" rows="3"></textarea>
                @error('keperluan')
                    <span class="text-error text-sm">
                        Mohon Mengisi Keperluan Yang Dilakukan Hingga Perlu Izin Keluar
                    </span>
                @enderror
            </div>

            <div class="modal-action justify-end pt-4">
                @can('akses', 'Pengajuan Izin Keluar Tambah')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('storeModalIzinKeluar').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>