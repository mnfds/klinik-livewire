<dialog id="storeModalPelayananEstetika" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeStoreModalPelayananEstetika', () => {
        document.getElementById('storeModalPelayananEstetika')?.close()
    })
">
    <div class="modal-box w-full max-w-2xl">
        <h3 class="text-xl font-semibold mb-4">Tambah Pelayanan Estetika</h3>

        <form wire:submit.prevent="store" class="space-y-4">

            {{-- Nama Pelayanan --}}
            <div class="form-control">
                <label class="label font-semibold">Nama Pelayanan</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="nama_treatment">
            </div>

            {{-- Harga --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Dasar</label>
                <input type="text" class="input input-bordered input-rupiah w-full" placeholder="Rp 0">
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga_treatment">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Potongan --}}
                <div class="form-control">
                    <label class="label font-semibold">Potongan</label>
                    <input type="text" class="input input-bordered input-rupiah w-full" placeholder="Rp 0">
                    <input type="hidden" class="input-rupiah-hidden" wire:model.defer="potongan">
                </div>
                {{-- Diskon --}}
                <div class="form-control">
                    <label class="label font-semibold">Diskon (%)</label>
                    <input type="number" class="input input-bordered w-full" placeholder="0-100" min="0" max="100" wire:model.defer="diskon">
                </div>
            </div>

            {{-- Harga Bersih --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Bersih (setelah diskon)</label>
                <input type="text" class="input input-bordered input-rupiah bg-base-200 w-full" placeholder="Otomatis terhitung" readonly>
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga_bersih">
            </div>

            {{-- Deskripsi --}}
            <div class="form-control">
                <label class="label font-semibold">Deskripsi</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="deskripsi">
            </div>

            {{-- Tombol --}}
            <div class="modal-action flex justify-end gap-2 pt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-neutral" onclick="document.getElementById('storeModalPelayananEstetika').close()">Batal</button>
            </div>
        </form>
    </div>

    {{-- Script sesuai bundling --}}
    <script>
        function hitungHargaBersihEstetika() {
            const root = document.querySelector('#storeModalPelayananEstetika');

            const hargaInput = root.querySelector('input[wire\\:model\\.defer="harga_treatment"]')?.previousElementSibling;
            const potonganInput = root.querySelector('input[wire\\:model\\.defer="potongan"]')?.previousElementSibling;
            const diskonInput = root.querySelector('input[wire\\:model\\.defer="diskon"]');
            const hargaBersihInput = root.querySelector('input[wire\\:model\\.defer="harga_bersih"]');
            const hargaBersihDisplay = hargaBersihInput?.previousElementSibling;

            if (!hargaInput || !potonganInput || !diskonInput || !hargaBersihInput || !hargaBersihDisplay) return;

            const harga = parseInt(hargaInput.value.replace(/\D/g, '') || 0);
            const potongan = parseInt(potonganInput.value.replace(/\D/g, '') || 0);
            const diskon = parseFloat(diskonInput.value || 0);
            
            const hargaSetelahPotongan = Math.max(0, harga - potongan);
            const diskonNominal = (hargaSetelahPotongan * diskon) / 100;
            const hargaBersih = Math.max(0, Math.round(hargaSetelahPotongan - diskonNominal));

            hargaBersihInput.value = hargaBersih;

            if (hargaBersihDisplay._cleave) {
                hargaBersihDisplay._cleave.setRawValue(hargaBersih);
            } else {
                hargaBersihDisplay.value = hargaBersih;
            }

            hargaBersihInput.dispatchEvent(new Event('input'));
        }

        function reinitHargaBersihListenersEstetika() {
            const root = document.querySelector('#storeModalPelayananEstetika');

            const hargaInput = root.querySelector('input[wire\\:model\\.defer="harga_treatment"]')?.previousElementSibling;
            const potonganInput = root.querySelector('input[wire\\:model\\.defer="potongan"]')?.previousElementSibling; 
            const diskonInput = root.querySelector('input[wire\\:model\\.defer="diskon"]');

            [hargaInput, potonganInput, diskonInput].forEach(el => {
                if (el) {
                    el.removeEventListener('input', hitungHargaBersihEstetika);
                    el.addEventListener('input', hitungHargaBersihEstetika);
                }
            });

            hitungHargaBersihEstetika();
        }

        function reinitPelayananModalHelpersEstetika() {
            initCleaveRupiah(); // kamu sudah punya global
            reinitHargaBersihListenersEstetika();
        }

        document.addEventListener('DOMContentLoaded', reinitPelayananModalHelpersEstetika);
        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', reinitPelayananModalHelpersEstetika);
        });
        document.addEventListener('livewire:navigated', reinitPelayananModalHelpersEstetika);
    </script>
</dialog>
