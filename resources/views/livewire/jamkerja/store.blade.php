<dialog id="storeModal" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeStoreModal', () => {
        document.getElementById('storeModal')?.close()
    })
">
    <div class="modal-box w-full max-w-2xl">
        <h3 class="text-xl font-semibold mb-4">Tambah Jam Kerja</h3>

        <form wire:submit.prevent="store" class="space-y-4">

            <div>
                <label class="label font-medium">Nama Shift <span class="text-error">*</span></label>
                <input type="text" class="input input-bordered w-full @error('nama_shift') input-error @enderror" wire:model.lazy="nama_shift">
                @error('nama_shift')
                    <span class="text-error text-sm mt-1">
                        Mohon Mengisi Nama Shift
                    </span>
                @enderror
            </div>

            <div>
                <label class="label font-medium">Tipe Shift</label>
                <select class="select select-bordered w-full" wire:model.lazy="tipe_shift">
                    <option value="">Pilih Tipe</option>
                    <option value="full">Full</option>
                    <option value="pagi">Pagi</option>
                    <option value="siang">Siang</option>
                    <option value="malam">Malam</option>
                    <option value="libur">Libur</option>
                    <option value="mp">MP</option>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label font-medium">Jam Mulai <span class="text-error">*</span></label>
                    <input type="time" class="input input-bordered w-full @error('jam_mulai') input-error @enderror" wire:model.lazy="jam_mulai">
                    @error('jam_mulai')
                        <span class="text-error text-sm mt-1">
                            Mohon Mengisi Jam Mulai
                        </span>
                    @enderror
                </div>
                <div>
                    <label class="label font-medium">Jam Selesai <span class="text-error">*</span></label>
                    <input type="time" class="input input-bordered w-full @error('jam_selesai') input-error @enderror" wire:model.lazy="jam_selesai">
                    @error('jam_selesai')
                        <span class="text-error text-sm mt-1">
                            Mohon Mengisi Jam Selesai
                        </span>
                    @enderror
                </div>
            </div>

            <div>
                <label class="label cursor-pointer justify-between">
                    <span class="label-text font-medium">Lewat Hari?</span>
                    <input type="checkbox" class="toggle toggle-primary" wire:model.lazy="lewat_hari" />
                </label>
            </div>

            <div class="modal-action justify-end mt-6">
                @can('akses', 'Jam Kerja Tambah')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-neutral" onclick="document.getElementById('storeModal').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>