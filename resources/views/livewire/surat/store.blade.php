<dialog id="storeModal" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeStoreModal', () => {
        document.getElementById('storeModal')?.close()
    })
">
    <div class="modal-box w-full max-w-2xl">
        <h3 class="text-xl font-semibold mb-4">Tambah Surat Keterangan</h3>

        <form wire:submit.prevent="store" class="space-y-4">

            <div>
                <label class="label font-medium">Nama Pasien <span class="text-error">*</span></label>
                <input type="text" class="input input-bordered w-full @error('pasien_id') input-error @enderror" wire:model.lazy="pasien_id">
                @error('pasien_id')
                    <span class="text-error text-sm mt-1">
                        Mohon Mengisi Nama Pasien
                    </span>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label font-medium">Mulai Berlaku Pada <span class="text-error">*</span></label>
                    <input type="date" class="input input-bordered w-full @error('mulai_berlaku') input-error @enderror" wire:model.lazy="mulai_berlaku">
                    @error('mulai_berlaku')
                        <span class="text-error text-sm mt-1">
                            Mohon Mengisi Tanggal
                        </span>
                    @enderror
                </div>
                <div>
                    <label class="label font-medium">Selesai Berlaku Pada<span class="text-error">*</span></label>
                    <input type="date" class="input input-bordered w-full @error('selesai_berlaku') input-error @enderror" wire:model.lazy="selesai_berlaku">
                    @error('selesai_berlaku')
                        <span class="text-error text-sm mt-1">
                            Mohon Mengisi Tanggal
                        </span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label font-medium">Jenis Tanda Tangan</label>
                    <select class="select select-bordered w-full" wire:model.lazy="tipe_ttd">
                        <option value="">Pilih Tanda Tangan</option>
                        <option value="digital">Digital</option>
                        <option value="basah">Basah</option>
                    </select>
                </div>
                <div>
                    <label class="label font-medium">Jenis Surat</label>
                    <select class="select select-bordered w-full" wire:model.lazy="jenis_surat">
                        <option value="">Pilih Jenis Surat</option>
                        <option value="standar">Surat Sehat Standar</option>
                        <option value="lengkap">Surat Sehat Lengkap</option>
                        <option value="sakit">Surat Sakit</option>
                    </select>
                </div>
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