<dialog id="modaleditpelayanan" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeModal', () => {
        document.getElementById('modaleditpelayanan')?.close()
    })
">
    <div class="modal-box w-full max-w-2xl">
        <h3 class="text-xl font-semibold mb-4">Edit Pelayanan</h3>

        <form wire:submit.prevent="update" class="space-y-4">

            {{-- Nama Pelayanan --}}
            <div class="form-control">
                <label class="label font-semibold">Nama Pelayanan</label>
                <input type="text" class="input input-bordered w-full" wire:model="nama_pelayanan">
            </div>

            {{-- Harga Pelayanan --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Pelayanan</label>
                <input type="text" id="display_harga_pelayanan" class="input input-bordered input-rupiah w-full" wire:model.defer="harga_pelayanan_show" placeholder="Rp 0">
                <input type="hidden" wire:model.defer="harga_pelayanan" class="input-rupiah-hidden">
            </div>

            {{-- Potongan --}}
            <div class="form-control">
                <label class="label font-semibold">Potongan</label>
                <input type="text" id="display_potongan" class="input input-bordered input-rupiah w-full" wire:model.defer="potongan_show" placeholder="Rp 0">
                <input type="hidden" wire:model.defer="potongan" class="input-rupiah-hidden">
            </div>

            {{-- Diskon --}}
            <div class="form-control">
                <label class="label font-semibold">Diskon (%)</label>
                <input type="number" min="0" max="100" class="input input-bordered w-full" wire:model.defer="diskon">
            </div>

            {{-- Harga Bersih --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Bersih</label>
                <input type="text" id="display_harga_bersih" class="input input-bordered input-rupiah bg-base-200 w-full" wire:model.defer="harga_bersih_show" readonly placeholder="Otomatis terhitung">
                <input type="hidden" wire:model.defer="harga_bersih" class="input-rupiah-hidden">
            </div>

            {{-- Deskripsi --}}
            <div class="form-control">
                <label class="label font-semibold">Deskripsi</label>
                <input type="text" class="input input-bordered w-full" wire:model="deskripsi">
            </div>

            {{-- Tombol --}}
            <div class="modal-action flex justify-end gap-2 pt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-neutral" onclick="document.getElementById('modaleditpelayanan').close()">Batal</button>
            </div>
        </form>
    </div>

    {{-- Script --}}
    <script>
        function hitungHargaBersihPelayananEdit() {
            const hargaDisplay = document.querySelector('#modaleditpelayanan #display_harga_pelayanan');
            const diskonInput = document.querySelector('#modaleditpelayanan input[wire\\:model\\.defer="diskon"]');
            const hargaHidden = document.querySelector('#modaleditpelayanan input[wire\\:model\\.defer="harga_pelayanan"]');
            const potonganHidden = document.querySelector('#modaleditpelayanan input[wire\\:model\\.defer="potongan"]');
            const hargaBersihInput = document.querySelector('#modaleditpelayanan input[wire\\:model\\.defer="harga_bersih"]');
            const hargaBersihDisplay = document.querySelector('#modaleditpelayanan #display_harga_bersih');

            if (!hargaDisplay || !potonganDisplay || !diskonInput || !hargaHidden || !potonganHidden || !hargaBersihInput) return;

            const harga = parseInt(hargaDisplay.value.replace(/\D/g, '') || 0);
            const potongan = parseInt(potonganDisplay.value.replace(/\D/g, '') || 0);
            const diskon = parseFloat(diskonInput.value || 0);

            // set value ke hidden (Livewire expect angka asli di hidden)
            hargaHidden.value = harga;
            potonganHidden.value = potongan;

            const hargaSetelahPotongan = Math.max(0, harga - potongan);
            const diskonNominal = (hargaSetelahPotongan * diskon) / 100;
            const hargaBersih = Math.max(0, Math.round(hargaSetelahPotongan - diskonNominal));

            hargaBersihInput.value = hargaBersih;
            hargaBersihInput.dispatchEvent(new Event('input'));

            if (hargaBersihDisplay._cleave) {
                hargaBersihDisplay._cleave.setRawValue(hargaBersih);
            }
        }

        function isiAwalHargaPelayananEdit() {
            const hargaDisplay = document.querySelector('#modaleditpelayanan #display_harga_pelayanan');
            const hargaBersihDisplay = document.querySelector('#modaleditpelayanan #display_harga_bersih');
            const potonganDisplay = document.querySelector('#modaleditpelayanan #display_potongan');


            const hargaHiddenValue = document.querySelector('#modaleditpelayanan input[wire\\:model\\.defer="harga_pelayanan"]')?.value || "0";
            const potonganHiddenValue = document.querySelector('#modaleditpelayanan input[wire\\:model\\.defer="potongan"]')?.value || "0";
            const hargaBersihHiddenValue = document.querySelector('#modaleditpelayanan input[wire\\:model\\.defer="harga_bersih"]')?.value || "0";

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

        function reinitUpdatePelayananListeners() {
            const hargaDisplay = document.querySelector('#modaleditpelayanan #display_harga_pelayanan');
            const potonganDisplay = document.querySelector('#modaleditpelayanan #display_potongan');
            const diskonInput = document.querySelector('#modaleditpelayanan input[wire\\:model\\.defer="diskon"]');

            [hargaDisplay, potonganDisplay, diskonInput].forEach(el => {
                if (el) {
                    el.removeEventListener('input', hitungHargaBersihPelayananEdit);
                    el.addEventListener('input', hitungHargaBersihPelayananEdit);
                }
            });

            hitungHargaBersihPelayananEdit();
        }

        function reinitUpdatePelayananModalHelpers() {
            initCleaveRupiah();
            isiAwalHargaPelayananEdit();
            reinitUpdatePelayananListeners();
        }

        document.addEventListener('DOMContentLoaded', reinitUpdatePelayananModalHelpers);
        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', reinitUpdatePelayananModalHelpers);
        });
        document.addEventListener('livewire:navigated', reinitUpdatePelayananModalHelpers);
    </script>
</dialog>
