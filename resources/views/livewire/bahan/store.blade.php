<dialog id="storeModalBahan" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestoreModalBahan', () => {
        document.getElementById('storeModalBahan')?.close()
    })
">
    <div class="modal-box w-full max-w-2xl">
        <h3 class="text-xl font-semibold mb-4">Tambah Bahan Baku Baru</h3>

        <form wire:submit.prevent="store" class="space-y-4">

            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="label font-medium">Nama Bahan Baku<span class="text-error">*</span></label>
                    <input type="text" class="input input-bordered w-full @error('nama') input-error @enderror" wire:model.lazy="nama">
                    @error('nama')
                        <span class="text-error text-sm">
                            Mohon Mengisi Nama Bahan Dengan Benar
                        </span>    
                    @enderror
                </div>

                <div>
                    <label class="label font-medium">Lokasi Disimpan</label>
                    <input type="text" class="input input-bordered w-full" wire:model.lazy="lokasi">
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
                    <select class="select select-bordered w-full @error('satuan_besar') input-error @enderror" wire:model.lazy="satuan_besar">
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
                    <input type="number" class="input input-bordered w-full bg-gray-100 @error('stok_kecil') input-error @enderror" readonly wire:model.lazy="stok_kecil">
                    @error('stok_kecil')
                        <span class="text-error text-sm">Mohon Mengisi Nominal Stok kecil Dengan Benar</span>
                    @enderror
                </div>

                <div>
                    <label class="label font-medium">Satuan Kecil<span class="text-error">*</span></label>
                    <select class="select select-bordered w-full @error('satuan_kecil') input-error @enderror" wire:model.lazy="satuan_kecil">
                        <option value="">Pilih Satuan</option>
                        <option value="pcs">Pcs</option>
                        <option value="unit">Unit</option>
                        <option value="lusin">Lusin</option>
                        <option value="box">Box</option>
                    </select>
                    @error('satuan_kecil')
                        <span class="text-error text-sm">Mohon Memilih Satuan Stok Kecil Dengan Benar</span>
                    @enderror
                </div>

                {{-- <div>
                    <label class="label font-medium">Kode</label>
                    <input type="text" class="input input-bordered w-full" wire:model.lazy="kode">
                </div> --}}

                <div>
                    <label class="label font-semibold">Expired</label>
                    <input type="date" class="input input-bordered w-full" wire:model.lazy="expired_at">
                </div>

                <div>
                    <label class="label font-semibold">Reminder (bulan)</label>
                    <input type="number" class="input input-bordered w-full" wire:model.lazy="reminder" min="0">
                </div>

                <!-- Full width -->
                <div class="col-span-2">
                    <label class="label font-medium">Keterangan</label>
                    <textarea wire:model.lazy="keterangan" class="textarea textarea-bordered w-full" rows="3"></textarea>
                </div>

            </div>

            <!-- Action -->
            <div class="modal-action justify-end pt-4">
                @can('akses', 'Persediaan Bahan Baku Tambah')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error"
                    onclick="document.getElementById('storeModalBahan').close()">
                    Batal
                </button>
            </div>

        </form>
    </div>

</dialog>