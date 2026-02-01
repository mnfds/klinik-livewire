<dialog id="storeModalRole" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestoreModalRole', () => {
        document.getElementById('storeModalRole')?.close()
    })
">
    <div class="modal-box max-w-6xl w-full">
        <h3 class="text-lg font-bold">Tambah Role</h3>
        <form wire:submit.prevent="store">
            {{-- Nama Role --}}
            <div class="form-control mb-2">
                <label class="label">Nama Role <span class="text-error">*</span></label>
                <input type="text" class="input input-bordered w-full @error('nama_role') input-error @enderror" wire:model.lazy="nama_role">
                    @error('nama_role')
                        <span class="text-error text-sm mt-1">
                            Mohon Mengisi Nama Role
                        </span>
                    @enderror
            </div>

            {{-- Daftar Akses --}}
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold">Daftar Akses</span>
                </label>

                <div class="space-y-4 max-h-90 overflow-y-auto border p-2 rounded-box">
                    @foreach ($this->groupedAkses as $group => $aksesGroup)
                        <div>
                            <h3 class="font-bold text-sm text-gray-700 mb-1">
                                {{ $aksesGroup->first()->nama_akses ?? 'Group ' . ($group ?? 'Lainnya') }}
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach ($aksesGroup as $akses)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox"
                                            value="{{ $akses->id }}"
                                            wire:model.defer="selectedAkses"
                                            class="checkbox checkbox-primary">
                                        <span>{{ $akses->nama_akses }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Aksi --}}
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn" onclick="document.getElementById('storeModalRole').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>