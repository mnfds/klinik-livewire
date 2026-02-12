<dialog id="modaleditbarang" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closemodaleditbarang', () => {
        document.getElementById('modaleditbarang')?.close()
    })
">
    <div class="modal-box w-full max-w-xl">
        <h3 class="text-xl font-semibold mb-4">Edit Data "{{ $nama }}"</h3>

        <form wire:submit.prevent="update" class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <div>
                <label class="label font-medium">Nama Barang<span class="text-error">*</span></label>
                <input type="text" class="input input-bordered w-full @error('nama') input-error @enderror" wire:model.defer="nama">
                @error('nama')
                    <span class="text-error text-sm">Mohon Mengisi Nama Dengan Benar</span>
                @enderror
            </div>

            <div>
                <label class="label font-medium">Satuan<span class="text-error">*</span></label>
                <select class="select select-bordered w-full @error('satuan') input-error @enderror" wire:model.defer="satuan">
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

            <div class="form-control">
                <label class="label font-semibold">Harga Jual<span class="text-error">*</span></label>
                <input type="text" id="display_harga_dasar" class="input input-bordered input-rupiah w-full @error('harga_dasar') input-error @enderror" wire:model.defer='harga_dasar_show' placeholder="Rp 0">
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga_dasar">
                @error('harga_dasar')
                    <span class="text-error text-sm">Mohon Mengisi Harga Jual Dengan Benar</span>
                @enderror
            </div>
            
            <div class="form-control">
                <label class="label font-semibold">Diskon (%)</label>
                <input type="number" min="0" max="100" class="input input-bordered w-full" wire:model.defer="diskon">
            </div>

            <div>
                <label class="label font-semibold">Potongan</label>
                <input type="text" id="display_potongan" class="input input-bordered input-rupiah w-full" wire:model.defer="potongan_show" placeholder="Rp 0">
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="potongan">
            </div>

            <div class="form-control">
                <label class="label font-semibold">Harga Bersih</label>
                <input type="text" id="display_harga_bersih" class="input input-bordered input-rupiah bg-base-200 w-full" wire:model.defer="harga_bersih_show" placeholder="Otomatis terhitung" readonly>
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga_bersih">
            </div>

            <div>
                <label class="label font-medium">Kode</label>
                <input type="text" class="input input-bordered w-full" wire:model.defer="kode">
            </div>

            <div>
                <label class="label font-medium">Lokasi Disimpan</label>
                <input type="text" class="input input-bordered w-full" wire:model.defer="lokasi">
            </div>

            <div class="col-span-2">
                <label class="label font-medium">Keterangan</label>
                <textarea wire:model.defer="keterangan" class="textarea textarea-bordered w-full" rows="3"></textarea>
            </div>

            <div class="col-span-1 sm:col-span-2 flex justify-end gap-2 mt-4">
                @can('akses', 'Persediaan Barang Edit')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('modaleditbarang').close()">Batal</button>
            </div>
        </form>
    </div>

    <script>
        function hitungHargaBersihBarangEdit() {
            const root = document.querySelector('#modaleditbarang');

            const hargaInput = root.querySelector('input[wire\\:model\\.defer="harga_dasar"]')?.previousElementSibling;
            const potonganInput = root.querySelector('input[wire\\:model\\.defer="potongan"]')?.previousElementSibling;
            const diskonInput = root.querySelector('input[wire\\:model\\.defer="diskon"]');
            
            const hargaBersihInput = root.querySelector('input[wire\\:model\\.defer="harga_bersih"]');
            const hargaBersihDisplay = hargaBersihInput?.previousElementSibling;

            if (!hargaInput || !potonganInput || !diskonInput || !hargaBersihInput || !hargaBersihDisplay) return;

            const harga = parseInt(hargaInput.value.replace(/\D/g, '') || 0);
            const potongan = parseInt(potonganInput.value.replace(/\D/g, '') || 0);
            const diskon = parseFloat(diskonInput.value || 0);

            // 1️⃣ Hitung diskon dulu dari harga dasar
            const diskonNominal = (harga * diskon) / 100;
            const hargaSetelahDiskon = Math.max(0, harga - diskonNominal);

            // 2️⃣ Baru kurangi potongan nominal
            const hargaBersih = Math.max(0, Math.round(hargaSetelahDiskon - potongan));

            // Update hidden Livewire
            hargaBersihInput.value = hargaBersih;
            hargaBersihInput.dispatchEvent(new Event('input'));

            // Update tampilan rupiah
            if (hargaBersihDisplay._cleave) {
                hargaBersihDisplay._cleave.setRawValue(hargaBersih);
            } else {
                hargaBersihDisplay.value = hargaBersih;
            }
        }

        function isiAwalHargaDanBersihBarangEdit() {
            const root = document.querySelector('#modaleditbarang');

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

        function reinitUpdateBarangListeners() {
            const root = document.querySelector('#modaleditbarang');

            const hargaInput = root.querySelector('input[wire\\:model\\.defer="harga_dasar"]')?.previousElementSibling;
            const potonganInput = root.querySelector('input[wire\\:model\\.defer="potongan"]')?.previousElementSibling;
            const diskonInput = root.querySelector('input[wire\\:model\\.defer="diskon"]');

            [hargaInput, potonganInput, diskonInput].forEach(input => {
                if (input) {
                    input.removeEventListener('input', hitungHargaBersihBarangEdit);
                    input.addEventListener('input', hitungHargaBersihBarangEdit);
                }
            });

            hitungHargaBersihBarangEdit();
        }

        function reinitUpdateBarangModalHelpers() {
            initCleaveRupiah(); // inisialisasi semua input-rupiah
            isiAwalHargaDanBersihBarangEdit(); // isi input harga awal dari Livewire
            reinitUpdateBarangListeners(); // set event listener
        }

        document.addEventListener('DOMContentLoaded', reinitUpdateBarangModalHelpers);
        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', reinitUpdateBarangModalHelpers);
        });
        document.addEventListener('livewire:navigated', reinitUpdateBarangModalHelpers);
    </script>
</dialog>