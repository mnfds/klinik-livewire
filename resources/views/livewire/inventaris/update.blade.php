<div>
    <dialog id="modaleditinventaris" class="modal" wire:ignore.self x-data x-init="
        Livewire.on('closemodaleditinventaris', () => {
            document.getElementById('modaleditinventaris')?.close()
        })
        ">
        <div class="modal-box w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Edit Inventaris</h3>
    
            <form wire:submit.prevent="update" class="space-y-4">
                <div>
                    <label class="label font-medium">Nama Inventaris<span class="text-error">*</span></label>
                    <input type="text" class="input input-bordered w-full @error('nama_barang') input-error @enderror" wire:model.defer="nama_barang">
                    @error('nama_barang')
                        <span class="text-error text-sm">Mohon Mengisi Nama Inventaris Dengan Benar</span>
                    @enderror
                </div>

                <div>
                    <label class="label font-medium">Jumlah Barang<span class="text-error">*</span></label>
                    <input type="text" class="input input-bordered w-full @error('jumlah') input-error @enderror" wire:model.defer="jumlah">
                    @error('jumlah')
                        <span class="text-error text-sm">Mohon Mengisi Jumlah Barang Inventaris Dengan Benar</span>
                    @enderror
                </div>

                <div>
                    <label class="label font-medium">Lokasi Barang<span class="text-error">*</span></label>
                    <input type="text" class="input input-bordered w-full @error('lokasi') input-error @enderror" wire:model.defer="lokasi">
                    @error('lokasi')
                        <span class="text-error text-sm">Mohon Mengisi Lokasi Barang Inventaris Dengan Benar</span>
                    @enderror
                </div>

                <div>
                    <label class="label font-medium">Tanggal Barang Datang</label>
                    <input type="date" class="input input-bordered w-full" wire:model.defer="tanggal_perolehan">
                </div>

                <div>
                    <label class="label font-medium">Kondisi Barang<span class="text-error">*</span></label>
                    <select class="select select-bordered w-full @error('kondisi') input-error @enderror" wire:model.defer="kondisi">
                        <option value="">Pilih Kondisi</option>
                        <option value="baik">Baik</option>
                        <option value="rusak_ringan">Rusak Ringan</option>
                        <option value="rusak_berat">Rusak Berat</option>
                    </select>
                    @error('kondisi')
                        <span class="text-error text-sm">Mohon Memilih Kondisi Barang Dengan Benar</span>
                    @enderror
                </div>

                <div>
                    <label class="label font-medium">Keterangan</label>
                    <textarea wire:model.defer="keterangan" class="textarea textarea-bordered w-full" rows="3"></textarea>
                </div>

                <div class="modal-action justify-end pt-4">
                    @can('akses', 'Dokumen Edit')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    @endcan
                    <button type="button" class="btn btn-error" onclick="document.getElementById('modaleditinventaris').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>
</div>