<dialog id="modaleditpelayananEstetika" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeModalEstetika', () => {
        document.getElementById('modaleditpelayananEstetika')?.close()
    })
">
    <div class="modal-box w-full max-w-2xl">
        <h3 class="text-xl font-semibold mb-4">Edit Pelayanan Estetika</h3>

        <form wire:submit.prevent="update" class="space-y-4">

            {{-- Nama Pelayanan --}}
            <div class="form-control">
                <label class="label font-semibold">Nama Pelayanan</label>
                <input type="text" class="input input-bordered w-full" wire:model="nama_treatment">
            </div>

            {{-- Harga Pelayanan --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Pelayanan</label>
                <input type="text" id="display_harga_treatment" class="input input-bordered input-rupiah w-full" wire:model.defer="harga_treatment_show" placeholder="Rp 0">
                <input type="hidden" wire:model.defer="harga_treatment" class="input-rupiah-hidden">
            </div>

            {{-- Diskon --}}
            <div class="form-control">
                <label class="label font-semibold">Diskon (%)</label>
                <input type="number" min="0" max="100" class="input input-bordered w-full" wire:model.defer="diskon">
            </div>

            {{-- Harga Bersih --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Bersih</label>
                <input type="text" id="display_harga_bersih_estetika" class="input input-bordered input-rupiah bg-base-200 w-full" wire:model.defer="harga_bersih_show" readonly placeholder="Otomatis terhitung">
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
                <button type="button" class="btn btn-neutral" onclick="document.getElementById('modaleditpelayananEstetika').close()">Batal</button>
            </div>
        </form>
    </div>

    {{-- Script --}}
    <script>
        function hitungHargaBersihPelayananEditEstetika() {
            const hargaInput = document.querySelector('#modaleditpelayananEstetika input[wire\\:model\\.defer="harga_treatment"]');
            const diskonInput = document.querySelector('#modaleditpelayananEstetika input[wire\\:model\\.defer="diskon"]');
            const hargaBersihInput = document.querySelector('#modaleditpelayananEstetika input[wire\\:model\\.defer="harga_bersih"]');
            const hargaBersihDisplay = document.querySelector('#modaleditpelayananEstetika #display_harga_bersih_estetika');

            if (!hargaInput || !diskonInput || !hargaBersihInput || !hargaBersihDisplay) return;

            const harga = parseInt(hargaInput.value.replace(/\D/g, '') || 0);
            const diskon = parseFloat(diskonInput.value || 0);
            const hargaBersih = Math.max(0, Math.round(harga - (harga * (diskon / 100))));

            hargaBersihInput.value = hargaBersih;
            hargaBersihInput.dispatchEvent(new Event('input'));

            if (hargaBersihDisplay._cleave) {
                hargaBersihDisplay._cleave.setRawValue(hargaBersih);
            }
        }

        function isiAwalHargaPelayananEditEstetika() {
            const hargaDisplay = document.querySelector('#modaleditpelayananEstetika #display_harga_treatment');
            const hargaBersihDisplay = document.querySelector('#modaleditpelayananEstetika #display_harga_bersih_estetika');

            const hargaHiddenValue = document.querySelector('#modaleditpelayananEstetika input[wire\\:model\\.defer="harga_treatment"]')?.value || "0";
            const hargaBersihHiddenValue = document.querySelector('#modaleditpelayananEstetika input[wire\\:model\\.defer="harga_bersih"]')?.value || "0";

            if (hargaDisplay && hargaDisplay._cleave) {
                hargaDisplay._cleave.setRawValue(hargaHiddenValue);
            }

            if (hargaBersihDisplay && hargaBersihDisplay._cleave) {
                hargaBersihDisplay._cleave.setRawValue(hargaBersihHiddenValue);
            }
        }

        function reinitUpdatePelayananListenersEstetika() {
            const hargaInput = document.querySelector('#modaleditpelayananEstetika input[wire\\:model\\.defer="harga_treatment"]');
            const diskonInput = document.querySelector('#modaleditpelayananEstetika input[wire\\:model\\.defer="diskon"]');

            if (hargaInput) {
                hargaInput.removeEventListener('input', hitungHargaBersihPelayananEditEstetika);
                hargaInput.addEventListener('input', hitungHargaBersihPelayananEditEstetika);
            }

            if (diskonInput) {
                diskonInput.removeEventListener('input', hitungHargaBersihPelayananEditEstetika);
                diskonInput.addEventListener('input', hitungHargaBersihPelayananEditEstetika);
            }

            hitungHargaBersihPelayananEditEstetika();
        }

        function reinitUpdatePelayananModalHelpersEstetika() {
            initCleaveRupiah();
            isiAwalHargaPelayananEditEstetika();
            reinitUpdatePelayananListenersEstetika();
        }

        document.addEventListener('DOMContentLoaded', reinitUpdatePelayananModalHelpersEstetika);
        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', reinitUpdatePelayananModalHelpersEstetika);
        });
        document.addEventListener('livewire:navigated', reinitUpdatePelayananModalHelpersEstetika);
    </script>
</dialog>
