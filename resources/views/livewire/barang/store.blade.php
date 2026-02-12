<dialog id="storeModalBarang" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestoreModalBarang', () => {
        document.getElementById('storeModalBarang')?.close()
    })
">
    <div class="modal-box w-full max-w-xl">
        <h3 class="text-xl font-semibold mb-4">Tambah Barang Baru</h3>

        <form wire:submit.prevent="store" class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <div>
                <label class="label font-medium">Nama Barang<span class="text-error">*</span></label>
                <input type="text" class="input input-bordered w-full @error('nama') input-error @enderror" wire:model.lazy="nama">
                @error('nama')
                    <span class="text-error text-sm">Mohon Mengisi Nama Dengan Benar</span>
                @enderror
            </div>

            <div>
                <label class="label font-medium">Satuan<span class="text-error">*</span></label>
                <select class="select select-bordered w-full @error('satuan') input-error @enderror" wire:model.lazy="satuan">
                    <option value="">Pilih Satuan</option>
                    <option value="pcs">Pcs</option>
                    <option value="unit">Unit</option>
                    <option value="lusin">Lusin</option>
                    <option value="box">Box</option>
                </select>
                @error('satuan')
                    <span class="text-error text-sm">Mohon Memilih Satuan Dengan Benar</span>
                @enderror
            </div>

            <div>
                <label class="label font-medium">Jumlah<span class="text-error">*</span></label>
                <input type="number" class="input input-bordered w-full @error('stok') input-error @enderror" wire:model.lazy="stok">
                @error('stok')
                    <span class="text-error text-sm">Mohon Mengisi Jumlah Stok Dengan Benar</span>
                @enderror
            </div>

            <div>
                <label class="label font-semibold">Harga Jual<span class="text-error">*</span></label>
                <input type="text" class="input input-bordered input-rupiah w-full @error('harga_dasar') input-error @enderror" placeholder="Rp 0">
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga_dasar">
                @error('harga_dasar')
                    <span class="text-error text-sm">Mohon Mengisi Harga Jual Dengan Benar</span>
                @enderror
            </div>
            
            <div>
                <label class="label font-semibold">Diskon (%)</label>
                <input type="number" min="0" max="100" class="input input-bordered w-full" wire:model.defer="diskon">
            </div>

            <div>
                <label class="label font-semibold">Potongan</label>
                <input type="text" class="input input-bordered input-rupiah w-full" placeholder="Rp 0">
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="potongan">
            </div>

            <div>
                <label class="label font-semibold">Harga Bersih (setelah diskon)</label>
                <input type="text" class="input input-bordered input-rupiah bg-base-200 w-full" placeholder="Otomatis terhitung" readonly>
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga_bersih">
            </div>

            <div>
                <label class="label font-medium">Kode</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="kode">
            </div>

            <div class="col-span-2">
                <label class="label font-medium">Lokasi Disimpan</label>
                <input type="text" class="input input-bordered w-full" wire:model.defer="lokasi">
            </div>

            <div class="col-span-2">
                <label class="label font-medium">Keterangan</label>
                <textarea wire:model.lazy="keterangan" class="textarea textarea-bordered w-full" rows="2"></textarea>
            </div>

            <div class="col-span-1 sm:col-span-2 flex justify-end gap-2 mt-4">
                @can('akses', 'Persediaan Barang Tambah')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('storeModalBarang').close()">Batal</button>
            </div>
        </form>
    </div>
    {{-- Script --}}
    <script>
        function hitungHargaBersihBarang() {
            const root = document.querySelector('#storeModalBarang');

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

            // Logika: hitung Diskon
            const diskonNominal = (harga * diskon) / 100;
            const hargaSetelahDiskon = Math.max(0, harga - diskonNominal);

            // Harga bersih akhir (Kurangi dengan nominal potongan)
            const hargaBersih = Math.max(0, Math.round(hargaSetelahDiskon - potongan));

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

        function reinitHargaBarangListeners() {
            const root = document.querySelector('#storeModalBarang');

            const hargaInput = root.querySelector('input[wire\\:model\\.defer="harga_dasar"]')?.previousElementSibling;
            const potonganInput = root.querySelector('input[wire\\:model\\.defer="potongan"]')?.previousElementSibling;
            const diskonInput = root.querySelector('input[wire\\:model\\.defer="diskon"]');

            [hargaInput, potonganInput, diskonInput].forEach(el => {
                if (el) {
                    el.removeEventListener('input', hitungHargaBersihBarang);
                    el.addEventListener('input', hitungHargaBersihBarang);
                }
            });

            // Jalankan awal
            hitungHargaBersihBarang();
        }

        function reinitBarangModalHelpers() {
            initCleaveRupiah(); // fungsi global kamu
            reinitHargaBarangListeners();
        }

        document.addEventListener('DOMContentLoaded', reinitBarangModalHelpers);
        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', reinitBarangModalHelpers);
        });
        document.addEventListener('livewire:navigated', reinitBarangModalHelpers);
    </script>
</dialog>