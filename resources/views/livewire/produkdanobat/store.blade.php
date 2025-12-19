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

            {{-- Golongan --}}
            <div>
                <label class="label font-semibold">Golongan Produk/Obat</label>
                <select class="select select-bordered w-full" wire:model.lazy="golongan" required>
                    <option>Pilih Golongan</option>
                    <option value="Skincare">Skincare</option>
                    <option value="Obat Bebas">Obat Bebas</option>
                    <option value="Obat Bebas Terbatas">Obat Bebas Terbatas</option>
                    <option value="Obat Keras">Obat Keras</option>
                    <option value="Obat Narkotika">Obat Narkotika</option>
                    <option value="Obat Psikotropika"> Obat Psikotropika</option>
                    <option value="Obat fitofarmaka">Obat fitofarmaka</option>
                    <option value="OHT (Obat Herbal Terstandar)">OHT (Obat Herbal Terstandar)</option>
                    <option value="Jamu">Jamu</option>
                    <option value="Lain - Lain">Lain - Lain</option>
                </select>
            </div>

            {{-- Harga Jual --}}
            <div>
                <label class="label font-semibold">Harga Jual</label>
                <input type="text" class="input input-bordered input-rupiah w-full" placeholder="Rp 0">
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga_dasar">
            </div>

            {{-- Potongan --}}
            <div>
                <label class="label font-semibold">Potongan</label>
                <input type="text" class="input input-bordered input-rupiah w-full" placeholder="Rp 0">
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="potongan">
            </div>

            {{-- Diskon --}}
            <div>
                <label class="label font-semibold">Diskon (%)</label>
                <input type="number" min="0" max="100" class="input input-bordered w-full" wire:model.defer="diskon">
            </div>

            {{-- Harga Bersih --}}
            <div>
                <label class="label font-semibold">Harga Bersih (setelah diskon)</label>
                <input type="text" class="input input-bordered input-rupiah bg-base-200 w-full" placeholder="Otomatis terhitung" readonly>
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga_bersih">
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

            {{-- Stok --}}
            <div>
                <label class="label font-semibold">Ingatkan berapa bulan sebelum barang expired?</label>
                <input type="number" class="input input-bordered w-full" wire:model.lazy="reminder" min="0">
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
                @can('akses', 'Produk & Obat Tambah')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-neutral" onclick="document.getElementById('storeModalProdukDanObat').close()">Batal</button>
            </div>
        </form>
    </div>

    {{-- Script --}}
    <script>
        function hitungHargaBersihProduk() {
            const root = document.querySelector('#storeModalProdukDanObat');

            const hargaInput = root.querySelector('input[wire\\:model\\.defer="harga_dasar"]')?.previousElementSibling;
            const potonganInput = root.querySelector('input[wire\\:model\\.defer="potongan"]')?.previousElementSibling;
            const diskonInput = root.querySelector('input[wire\\:model\\.defer="diskon"]');
            const hargaBersihInput = root.querySelector('input[wire\\:model\\.defer="harga_bersih"]');
            const hargaBersihDisplay = hargaBersihInput?.previousElementSibling;

            if (!hargaInput || !potonganInput || !diskonInput || !hargaBersihInput || !hargaBersihDisplay) return;

            // Ambil nilai dan ubah ke angka murni
            const harga = parseInt(hargaInput.value.replace(/\D/g, '') || 0);
            const potongan = parseInt(potonganInput.value.replace(/\D/g, '') || 0);
            const diskon = parseFloat(diskonInput.value || 0);

            // Logika: harga setelah potongan
            const hargaSetelahPotongan = Math.max(0, harga - potongan);

            // Diskon nominal
            const diskonNominal = (hargaSetelahPotongan * diskon) / 100;

            // Harga bersih akhir
            const hargaBersih = Math.max(0, Math.round(hargaSetelahPotongan - diskonNominal));

            // Update hidden input Livewire
            hargaBersihInput.value = hargaBersih;

            // Update tampilan format rupiah
            if (hargaBersihDisplay._cleave) {
                hargaBersihDisplay._cleave.setRawValue(hargaBersih);
            } else {
                hargaBersihDisplay.value = hargaBersih;
            }

            // Trigger Livewire update
            hargaBersihInput.dispatchEvent(new Event('input'));
        }

        function reinitHargaProdukListeners() {
            const root = document.querySelector('#storeModalProdukDanObat');

            const hargaInput = root.querySelector('input[wire\\:model\\.defer="harga_dasar"]')?.previousElementSibling;
            const potonganInput = root.querySelector('input[wire\\:model\\.defer="potongan"]')?.previousElementSibling;
            const diskonInput = root.querySelector('input[wire\\:model\\.defer="diskon"]');

            [hargaInput, potonganInput, diskonInput].forEach(el => {
                if (el) {
                    el.removeEventListener('input', hitungHargaBersihProduk);
                    el.addEventListener('input', hitungHargaBersihProduk);
                }
            });

            // Jalankan awal
            hitungHargaBersihProduk();
        }

        function reinitProdukModalHelpers() {
            initCleaveRupiah(); // fungsi global kamu
            reinitHargaProdukListeners();
        }

        document.addEventListener('DOMContentLoaded', reinitProdukModalHelpers);
        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', reinitProdukModalHelpers);
        });
        document.addEventListener('livewire:navigated', reinitProdukModalHelpers);
    </script>
</dialog>
