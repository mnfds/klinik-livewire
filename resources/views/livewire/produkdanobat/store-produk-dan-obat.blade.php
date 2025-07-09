<dialog id="storeModalProdukDanObat" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeStoreModalProdukDanObat', () => {
        document.getElementById('storeModalProdukDanObat')?.close()
    })
">
    <div class="modal-box">
        <h3 class="text-lg font-bold">Tambah Produk & Obat</h3>
        <form wire:submit.prevent="store" class="space-y-4">

            {{-- Nama Dagang --}}
            <div class="form-control">
                <label class="label font-semibold">Nama Dagang</label>
                <input type="text" class="input input-bordered" wire:model.lazy="nama_dagang" required>
            </div>

            {{-- Kode Produk --}}
            <div class="form-control">
                <label class="label font-semibold">Kode Produk</label>
                <input type="text" class="input input-bordered" wire:model.lazy="kode" required>
            </div>

            {{-- Sediaan --}}
            <div class="form-control">
                <label class="label font-semibold">Satuan</label>
                <select class="select select-bordered" wire:model.lazy="sediaan" required>
                    <option value="" disabled selected>Pilih Sediaan</option>
                    <option value="pcs">Pcs</option>
                    <option value="pot">Pot</option>
                    <option value="tablet">Tablet</option>
                    <option value="botol">Botol</option>
                    <option value="sachet">Sachet</option>
                    <option value="strip">Strip</option>
                    <option value="box">Box</option>
                    <option value="paket">Paket</option>
                    <option value="kapsul">Kapsul</option>
                    <option value="sirup">Sirup</option>
                    <option value="salep">Salep</option>
                    <option value="injeksi">Injeksi</option>
                    <option value="tube">Tube</option>
                    {{-- Tambahkan satuan lain sesuai kebutuhan --}}
                </select>
            </div>

            {{-- Harga Jual --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Jual</label>
                <input type="number" class="input input-bordered" wire:model.lazy="harga_jual" min="0" required>
            </div>

            {{-- Stok --}}
            <div class="form-control">
                <label class="label font-semibold">Stok</label>
                <input type="number" class="input input-bordered" wire:model.lazy="stok" min="0" required>
            </div>

            {{-- Tanggal Expired --}}
            <div class="form-control">
                <label class="label font-semibold">Expired</label>
                <input type="date" class="input input-bordered" wire:model.lazy="expired_at">
            </div>

            {{-- Batch --}}
            <div class="form-control">
                <label class="label font-semibold">Batch</label>
                <input type="text" class="input input-bordered" wire:model.lazy="batch">
            </div>

            {{-- Lokasi --}}
            <div class="form-control">
                <label class="label font-semibold">Satuan</label>
                <select class="select select-bordered" wire:model.lazy="lokasi" required>
                    <option value="" disabled selected>Pilih Lokasi Penyimpanan</option>
                    <option value="Gudang Utama">Gudang Utama</option>
                    <option value="Gudang Kecil">Gudang Kecil</option>
                </select>
            </div>

            {{-- Supplier --}}
            <div class="form-control">
                <label class="label font-semibold">Supplier</label>
                <input type="text" class="input input-bordered" wire:model.lazy="supplier">
            </div>

            {{-- Tombol --}}
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn" onclick="document.getElementById('storeModalProdukDanObat').close()">Batal</button>
            </div>

        </form>
    </div>
</dialog>