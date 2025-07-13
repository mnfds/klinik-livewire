<dialog id="storeModalProdukDanObat" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeStoreModalProdukDanObat', () => {
        document.getElementById('storeModalProdukDanObat')?.close()
    })
">
    <div class="modal-box w-full max-w-4xl">
        <h3 class="text-xl font-semibold mb-4">Tambah Produk & Obat</h3>

        <form wire:submit.prevent="store" class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- Nama Dagang --}}
            <div>
                <label class="label font-semibold">Nama Dagang</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="nama_dagang" required>
            </div>

            {{-- Kode Produk --}}
            <div>
                <label class="label font-semibold">Kode Produk</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="kode" required>
            </div>

            {{-- Sediaan --}}
            <div>
                <label class="label font-semibold">Satuan</label>
                <select class="select select-bordered w-full" wire:model.lazy="sediaan" required>
                    <option>Pilih Sediaan</option>
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
                </select>
            </div>

            {{-- Harga Jual --}}
            <div>
                <label class="label font-semibold">Harga Jual</label>
                <input
                    type="text"
                    class="input input-bordered w-full"
                    inputmode="numeric"
                    wire:ignore
                    oninput="formatRupiahLive(this)"
                    id="harga_dasar_input"
                >
                <input type="hidden" wire:model="harga_dasar" id="harga_dasar_model">
            </div>

            {{-- Diskon --}}
            <div>
                <label class="label font-semibold">Diskon (%)</label>
                <input type="number" min="0" max="100" class="input input-bordered w-full" wire:model.lazy="diskon">
            </div>

            {{-- Harga Bersih --}}
            <div>
                <label class="label font-semibold">Harga Bersih (setelah diskon)</label>
                <input type="text" class="input input-bordered bg-base-200 w-full"
                    value="{{ number_format((float)$harga_dasar - ((float)$harga_dasar * (float)$diskon / 100), 0, ',', '.') }}"
                    readonly>
            </div>

            {{-- Stok --}}
            <div>
                <label class="label font-semibold">Stok</label>
                <input type="number" class="input input-bordered w-full" wire:model.lazy="stok" min="0" required>
            </div>

            {{-- Expired --}}
            <div>
                <label class="label font-semibold">Expired</label>
                <input type="date" class="input input-bordered w-full" wire:model.lazy="expired_at">
            </div>

            {{-- Batch --}}
            <div>
                <label class="label font-semibold">Batch</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="batch">
            </div>

            {{-- Lokasi --}}
            <div>
                <label class="label font-semibold">Lokasi</label>
                <select class="select select-bordered w-full" wire:model.lazy="lokasi" required>
                    <option>Pilih Lokasi Penyimpanan</option>
                    <option value="Gudang Utama">Gudang Utama</option>
                    <option value="Gudang Kecil">Gudang Kecil</option>
                </select>
            </div>

            {{-- Supplier --}}
            <div>
                <label class="label font-semibold">Supplier</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="supplier">
            </div>

            {{-- Tombol Submit --}}
            <div class="col-span-1 sm:col-span-2 flex justify-end gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-error" onclick="document.getElementById('storeModalProdukDanObat').close()">Batal</button>
            </div>
        </form>
    </div>

    <script>
        function formatRupiahLive(input) {
            let raw = input.value.replace(/[^\d]/g, '');
            let formatted = new Intl.NumberFormat('id-ID').format(raw);

            input.value = formatted;
            document.getElementById('harga_dasar_model').value = raw;
            document.getElementById('harga_dasar_model').dispatchEvent(new Event('input'));
        }
    </script>
</dialog>
