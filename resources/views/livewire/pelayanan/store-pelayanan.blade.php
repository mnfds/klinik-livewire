<dialog id="storeModalPelayanan" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeStoreModalPelayanan', () => {
        document.getElementById('storeModalPelayanan')?.close()
    })
">
    <div class="modal-box w-full max-w-2xl">
        <h3 class="text-xl font-semibold mb-4">Tambah Pelayanan</h3>

        <form wire:submit.prevent="store" class="space-y-4">

            {{-- Nama Pelayanan --}}
            <div class="form-control">
                <label class="label font-semibold">Nama Pelayanan</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="nama_pelayanan">
            </div>

            {{-- Harga & Diskon --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label font-semibold">Harga Dasar</label>
                    <input type="text" class="input input-bordered input-rupiah w-full" placeholder="Rp 0">
                    <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga_pelayanan">
                </div>

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
                <button type="button" class="btn" onclick="document.getElementById('storeModalPelayanan').close()">Batal</button>
            </div>
        </form>
    </div>

    {{-- Script sesuai bundling --}}
    <script>
        function hitungHargaBersih() {
            const root = document.querySelector('#storeModalPelayanan');

            const hargaInput = root.querySelector('input[wire\\:model\\.defer="harga_pelayanan"]')?.previousElementSibling;
            const diskonInput = root.querySelector('input[wire\\:model\\.defer="diskon"]');
            const hargaBersihInput = root.querySelector('input[wire\\:model\\.defer="harga_bersih"]');
            const hargaBersihDisplay = hargaBersihInput?.previousElementSibling;

            if (!hargaInput || !diskonInput || !hargaBersihInput || !hargaBersihDisplay) return;

            const harga = parseInt(hargaInput.value.replace(/\D/g, '') || 0);
            const diskon = parseFloat(diskonInput.value || 0);
            const hargaBersih = Math.max(0, Math.round(harga - (harga * (diskon / 100))));

            hargaBersihInput.value = hargaBersih;

            if (hargaBersihDisplay._cleave) {
                hargaBersihDisplay._cleave.setRawValue(hargaBersih);
            } else {
                hargaBersihDisplay.value = hargaBersih;
            }

            hargaBersihInput.dispatchEvent(new Event('input'));
        }

        function reinitHargaBersihListeners() {
            const root = document.querySelector('#storeModalPelayanan');

            const hargaInput = root.querySelector('input[wire\\:model\\.defer="harga_pelayanan"]')?.previousElementSibling;
            const diskonInput = root.querySelector('input[wire\\:model\\.defer="diskon"]');

            if (hargaInput) {
                hargaInput.removeEventListener('input', hitungHargaBersih);
                hargaInput.addEventListener('input', hitungHargaBersih);
            }

            if (diskonInput) {
                diskonInput.removeEventListener('input', hitungHargaBersih);
                diskonInput.addEventListener('input', hitungHargaBersih);
            }

            hitungHargaBersih();
        }

        function reinitPelayananModalHelpers() {
            initCleaveRupiah(); // kamu sudah punya global
            reinitHargaBersihListeners();
        }

        document.addEventListener('DOMContentLoaded', reinitPelayananModalHelpers);
        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', reinitPelayananModalHelpers);
        });
        document.addEventListener('livewire:navigated', reinitPelayananModalHelpers);
    </script>
</dialog>
