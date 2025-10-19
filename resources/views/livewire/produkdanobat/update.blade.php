<dialog id="modaleditprodukdanobat" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeModal', () => {
        document.getElementById('modaleditprodukdanobat')?.close()
    })
">
    <div class="modal-box w-full max-w-4xl">
        <h3 class="text-xl font-semibold mb-4">Edit Produk & Obat</h3>

        <form wire:submit.prevent="update" class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- Nama Dagang --}}
            <div>
                <label class="label font-semibold">Nama Dagang</label>
                <input type="text" class="input input-bordered w-full" wire:model="nama_dagang" required>
            </div>

            {{-- Kode Produk --}}
            <div>
                <label class="label font-semibold">Kode Produk</label>
                <input type="text" class="input input-bordered w-full" wire:model="kode" required>
            </div>

            {{-- Sediaan --}}
            <div>
                <label class="label font-semibold">Satuan</label>
                <select class="select select-bordered w-full" wire:model="sediaan" required>
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

            {{-- Harga & Diskon --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Jual</label>
                <input type="text" id="display_harga_dasar" class="input input-bordered input-rupiah w-full" wire:model.defer='harga_dasar_show' placeholder="Rp 0">
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga_dasar">
            </div>

            {{-- Potongan --}}
            <div>
                <label class="label font-semibold">Potongan</label>
                <input type="text" id="display_potongan" class="input input-bordered input-rupiah w-full" wire:model.defer="potongan_show" placeholder="Rp 0">
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="potongan">
            </div>
            
            {{-- Diskon --}}
            <div class="form-control">
                <label class="label font-semibold">Diskon (%)</label>
                <input type="number" min="0" max="100" class="input input-bordered w-full" wire:model.defer="diskon">
            </div>

            {{-- Harga Bersih --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Bersih</label>
                <input type="text" id="display_harga_bersih" class="input input-bordered input-rupiah bg-base-200 w-full" wire:model.defer="harga_bersih_show" placeholder="Otomatis terhitung" readonly>
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga_bersih">
            </div>

            {{-- Stok --}}
            <div>
                <label class="label font-semibold">Stok</label>
                <input type="number" class="input input-bordered w-full" wire:model="stok" min="0" required>
            </div>

            {{-- Expired --}}
            <div>
                <label class="label font-semibold">Expired</label>
                <input type="date" class="input input-bordered w-full" wire:model="expired_at">
            </div>
                       
            {{-- Reminder --}}
            <div>
                <label class="label font-semibold">Ingatkan berapa bulan sebelum barang expired?</label>
                <input type="number" class="input input-bordered w-full" wire:model="reminder" min="0">
            </div>

            {{-- Batch --}}
            <div>
                <label class="label font-semibold">Batch</label>
                <input type="text" class="input input-bordered w-full" wire:model="batch">
            </div>

            {{-- Lokasi --}}
            <div>
                <label class="label font-semibold">Lokasi</label>
                <select class="select select-bordered w-full" wire:model="lokasi" required>
                    <option>Pilih Lokasi Penyimpanan</option>
                    <option value="Gudang Utama">Gudang Utama</option>
                    <option value="Gudang Kecil">Gudang Kecil</option>
                </select>
            </div>

            {{-- Supplier --}}
            <div>
                <label class="label font-semibold">Supplier</label>
                <input type="text" class="input input-bordered w-full" wire:model="supplier">
            </div>

            {{-- Tombol --}}
            <div class="col-span-1 sm:col-span-2 flex justify-end gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-neutral" onclick="document.getElementById('modaleditprodukdanobat').close()">Batal</button>
            </div>
        </form>
    </div>

<script>
    function hitungHargaBersihProdukEdit() {
        const root = document.querySelector('#modaleditprodukdanobat');

        // Ambil input Cleave (tampilan), bukan hidden Livewire
        const hargaInput = root.querySelector('input[wire\\:model\\.defer="harga_dasar"]')?.previousElementSibling;
        const potonganInput = root.querySelector('input[wire\\:model\\.defer="potongan"]')?.previousElementSibling;
        const diskonInput = root.querySelector('input[wire\\:model\\.defer="diskon"]');
        
        // Hidden Livewire & tampilan harga bersih
        const hargaBersihInput = root.querySelector('input[wire\\:model\\.defer="harga_bersih"]');
        const hargaBersihDisplay = hargaBersihInput?.previousElementSibling;

        if (!hargaInput || !potonganInput || !diskonInput || !hargaBersihInput || !hargaBersihDisplay) return;

        // Ambil nilai dan ubah ke angka murni
        const harga = parseInt(hargaInput.value.replace(/\D/g, '') || 0);
        const potongan = parseInt(potonganInput.value.replace(/\D/g, '') || 0);
        const diskon = parseFloat(diskonInput.value || 0);

        // Harga setelah potongan nominal
        const hargaSetelahPotongan = Math.max(0, harga - potongan);

        // Diskon nominal dari harga setelah potongan
        const diskonNominal = (hargaSetelahPotongan * diskon) / 100;

        // Harga bersih akhir
        const hargaBersih = Math.max(0, Math.round(hargaSetelahPotongan - diskonNominal));

        // Update hidden input Livewire
        hargaBersihInput.value = hargaBersih;
        hargaBersihInput.dispatchEvent(new Event('input'));

        // Update tampilan format rupiah
        if (hargaBersihDisplay._cleave) {
            hargaBersihDisplay._cleave.setRawValue(hargaBersih);
        } else {
            hargaBersihDisplay.value = hargaBersih;
        }
    }

    function isiAwalHargaDanBersihProdukEdit() {
        const root = document.querySelector('#modaleditprodukdanobat');

        const hargaDisplay = root.querySelector('input[wire\\:model\\.defer="harga_dasar"]')?.previousElementSibling;
        const potonganDisplay = root.querySelector('input[wire\\:model\\.defer="potongan"]')?.previousElementSibling;
        const hargaBersihDisplay = root.querySelector('input[wire\\:model\\.defer="harga_bersih"]')?.previousElementSibling;

        const hargaHiddenValue = root.querySelector('input[wire\\:model\\.defer="harga_dasar"]')?.value || "0";
        const potonganHiddenValue = root.querySelector('input[wire\\:model\\.defer="potongan"]')?.value || "0";
        const hargaBersihHiddenValue = root.querySelector('input[wire\\:model\\.defer="harga_bersih"]')?.value || "0";

        if (hargaDisplay && hargaDisplay._cleave) {
            hargaDisplay._cleave.setRawValue(hargaHiddenValue);
        }
        if (potonganDisplay && potonganDisplay._cleave) {
            potonganDisplay._cleave.setRawValue(potonganHiddenValue);
        }
        if (hargaBersihDisplay && hargaBersihDisplay._cleave) {
            hargaBersihDisplay._cleave.setRawValue(hargaBersihHiddenValue);
        }
    }

    function reinitUpdateProdukObatListeners() {
        const root = document.querySelector('#modaleditprodukdanobat');

        const hargaInput = root.querySelector('input[wire\\:model\\.defer="harga_dasar"]')?.previousElementSibling;
        const potonganInput = root.querySelector('input[wire\\:model\\.defer="potongan"]')?.previousElementSibling;
        const diskonInput = root.querySelector('input[wire\\:model\\.defer="diskon"]');

        [hargaInput, potonganInput, diskonInput].forEach(input => {
            if (input) {
                input.removeEventListener('input', hitungHargaBersihProdukEdit);
                input.addEventListener('input', hitungHargaBersihProdukEdit);
            }
        });

        hitungHargaBersihProdukEdit();
    }

    function reinitUpdateProdukObatModalHelpers() {
        initCleaveRupiah(); // inisialisasi semua input-rupiah
        isiAwalHargaDanBersihProdukEdit(); // isi input harga awal dari Livewire
        reinitUpdateProdukObatListeners(); // set event listener
    }

    document.addEventListener('DOMContentLoaded', reinitUpdateProdukObatModalHelpers);
    document.addEventListener('livewire:load', () => {
        Livewire.hook('message.processed', reinitUpdateProdukObatModalHelpers);
    });
    document.addEventListener('livewire:navigated', reinitUpdateProdukObatModalHelpers);
</script>


</dialog>
