<dialog id="modaleditbahanbaku" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closemodaleditbahanbaku', () => {
        document.getElementById('modaleditbahanbaku')?.close()
    })
">
    <div class="modal-box w-full max-w-2xl">
        <h3 class="text-xl font-semibold mb-4">Edit Data "{{ $nama }}"</h3>

        <form wire:submit.prevent="update" class="space-y-4">

            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="label font-medium">Nama Bahan Baku<span class="text-error">*</span></label>
                    <input type="text" class="input input-bordered w-full @error('nama') input-error @enderror" wire:model.defer="nama">
                    @error('nama')
                        <span>Mohon Mengisi Nama Bahan Dengan Benar</span>
                    @enderror
                </div>

                <div>
                    <label class="label font-medium">Lokasi Disimpan</label>
                    <input type="text" class="input input-bordered w-full" wire:model.defer="lokasi">
                </div>

                <div>
                    <label class="label font-medium">Jumlah Stok Besar<span class="text-error">*</span></label>
                    <input type="number" class="input input-bordered w-full @error('stok_besar') input-error @enderror" wire:model.live="stok_besar">
                    @error('stok_besar')
                        <span class="text-error text-sm">Mohon Mengisi Nominal Stok Besar Dengan Benar</span>
                    @enderror
                </div>

                <div>
                    <label class="label font-medium">Satuan Besar<span class="text-error">*</span></label>
                    <select class="select select-bordered w-full @error('satuan_besar') input-error @enderror" wire:model.defer="satuan_besar">
                        <option value="">Pilih Satuan</option>
                        <option value="pcs">Pcs</option>
                        <option value="unit">Unit</option>
                        <option value="lusin">Lusin</option>
                        <option value="box">Box</option>
                    </select>
                    @error('satuan_besar')
                        <span class="text-error text-sm">Mohon Memilih Satuan Stok Besar Dengan Benar</span>
                    @enderror
                </div>

                <div class="w-full">
                    <label class="label font-medium">Nominal Pengali<span class="text-error">*</span></label>
                    <input type="number" class="input input-bordered w-full @error('pengali') input-error @enderror" wire:model.live="pengali">
                    @error('pengali')
                        <span class="text-error text-sm">Mohon Mengisi Nominal Pengali Stok Dengan Benar</span>
                    @enderror
                </div>

                <div>
                    <label class="label font-medium">Jumlah Stok Kecil<span class="text-error">*</span></label>
                    <input type="number" class="input input-bordered w-full bg-gray-100 @error('stok_kecil') input-error @enderror" wire:model.defer="stok_kecil">
                    @error('stok_kecil')
                        <span class="text-error text-sm">Mohon Mengisi Nominal Stok kecil Dengan Benar</span>
                    @enderror
                </div>

                <div>
                    <label class="label font-medium">Satuan kecil<span class="text-error">*</span></label>
                    <select class="select select-bordered w-full @error('satuan_kecil') input-error @enderror" wire:model.defer="satuan_kecil">
                        <option value="">Pilih Satuan</option>
                        <option value="pcs">Pcs</option>
                        <option value="unit">Unit</option>
                        <option value="lusin">Lusin</option>
                        <option value="box">Box</option>
                    </select>
                    @error('satuan_kecil')
                        <span class="text-error text-sm">Mohon Memilih Satuan Stok Besar Dengan Benar</span>
                    @enderror
                </div>
    
                {{-- <div>
                    <label class="label font-medium">Kode</label>
                    <input type="text" class="input input-bordered w-full" wire:model.defer="kode">
                </div> --}}
    
                <div>
                    <label class="label font-semibold">Expired</label>
                    <input type="date" class="input input-bordered w-full" wire:model.defer="expired_at">
                </div>
                           
                <div>
                    <label class="label font-semibold">Reminder (bulan)</label>
                    <input type="number" class="input input-bordered w-full" wire:model.defer="reminder" min="0">
                </div>
    
                <div class="col-span-2">
                    <label class="label font-medium">Keterangan</label>
                    <textarea wire:model.defer="keterangan" class="textarea textarea-bordered w-full" rows="3"></textarea>
                </div>
    
            </div>
            
            <div class="modal-action justify-end pt-4">
                @can('akses', 'Persediaan Bahan Baku Edit')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('modaleditbahanbaku').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>