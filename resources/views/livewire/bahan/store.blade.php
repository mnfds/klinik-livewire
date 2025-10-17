<dialog id="storeModalBahan" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestoreModalBahan', () => {
        document.getElementById('storeModalBahan')?.close()
    })
">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Tambah Bahan Baku Baru</h3>

        <form wire:submit.prevent="store" class="space-y-4">

            <div>
                <label class="label font-medium">Nama Bahan Baku</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="nama" required>
            </div>

            <div>
                <label class="label font-medium">Jumlah</label>
                <input type="number" class="input input-bordered w-full" wire:model.lazy="stok">
            </div>

            <div>
                <label class="label font-medium">Satuan</label>
                <select class="select select-bordered w-full" wire:model.lazy="satuan">
                    <option value="">Pilih Satuan</option>
                    <option value="pcs">Pcs</option>
                    <option value="unit">Unit</option>
                    <option value="lusin">Lusin</option>
                    <option value="box">Box</option>
                </select>
            </div>

            <div>
                <label class="label font-medium">Kode</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="kode">
            </div>

            <div>
                <label class="label font-medium">Lokasi Disimpan</label>
                <select class="select select-bordered w-full" wire:model.lazy="lokasi">
                    <option value="">Pilih Lokasi</option>
                    <option value="Gudang Besar">Gudang Besar</option>
                    <option value="Gudang Kecil">Gudang Kecil</option>
                </select>
            </div>

            <div>
                <label class="label font-semibold">Expired</label>
                <input type="date" class="input input-bordered w-full" wire:model.lazy="expired_at">
            </div>
                       
            <div>
                <label class="label font-semibold">Ingatkan berapa bulan sebelum barang expired?</label>
                <input type="number" class="input input-bordered w-full" wire:model.lazy="reminder" min="0">
            </div>

            <div>
                <label class="label font-medium">Keterangan</label>
                <textarea wire:model.lazy="keterangan" class="textarea textarea-bordered w-full" rows="3"></textarea>
            </div>

            <div class="modal-action justify-end pt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-error" onclick="document.getElementById('storeModalBahan').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>