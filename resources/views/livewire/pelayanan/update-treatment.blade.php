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

            {{-- Harga Pelayanan --}}
            <div class="form-control">
                <label class="label font-semibold">Potongan</label>
                <input type="text" id="display_potongan" class="input input-bordered input-rupiah w-full" wire:model.defer="display_potongan" placeholder="Rp 0">
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
        // Hitung ulang harga bersih berdasarkan harga treatment dan diskon
        function hitungHargaBersihPelayananEditEstetika() {
            const modal = document.querySelector('#modaleditpelayananEstetika');
            if (!modal) return;

            const hargaInput = modal.querySelector('input[wire\\:model\\.defer="harga_treatment"]');
            const diskonInput = modal.querySelector('input[wire\\:model\\.defer="diskon"]');
            const hargaBersihInput = modal.querySelector('input[wire\\:model\\.defer="harga_bersih"]');
            const hargaBersihDisplay = modal.querySelector('#display_harga_bersih_estetika');

            if (!hargaInput || !diskonInput || !hargaBersihInput || !hargaBersihDisplay) return;

            const harga = parseInt(hargaInput.value.replace(/\D/g, '') || 0);
            const diskon = parseFloat(diskonInput.value || 0);

            // Rumus: harga bersih = harga - (harga * diskon / 100)
            const hargaBersih = Math.max(0, Math.round(harga - (harga * (diskon / 100))));

            // Set nilai input hidden agar Livewire menangkap perubahan
            hargaBersihInput.value = hargaBersih;
            hargaBersihInput.dispatchEvent(new Event('input'));

            // Update tampilan yang sudah diformat Cleave
            if (hargaBersihDisplay._cleave) {
                hargaBersihDisplay._cleave.setRawValue(hargaBersih);
            }
        }

        // Isi ulang nilai awal harga dan harga bersih saat modal dibuka kembali
        function isiAwalHargaPelayananEditEstetika() {
            const modal = document.querySelector('#modaleditpelayananEstetika');
            if (!modal) return;

            const hargaDisplay = modal.querySelector('#display_harga_treatment');
            const hargaBersihDisplay = modal.querySelector('#display_harga_bersih_estetika');
            const hargaHiddenValue = modal.querySelector('input[wire\\:model\\.defer="harga_treatment"]')?.value || "0";
            const hargaBersihHiddenValue = modal.querySelector('input[wire\\:model\\.defer="harga_bersih"]')?.value || "0";

            if (hargaDisplay && hargaDisplay._cleave) {
                hargaDisplay._cleave.setRawValue(hargaHiddenValue);
            }

            if (hargaBersihDisplay && hargaBersihDisplay._cleave) {
                hargaBersihDisplay._cleave.setRawValue(hargaBersihHiddenValue);
            }
        }

        // Tambahkan ulang event listener pada input harga dan diskon
        function reinitUpdatePelayananListenersEstetika() {
            const modal = document.querySelector('#modaleditpelayananEstetika');
            if (!modal) return;

            const hargaInput = modal.querySelector('input[wire\\:model\\.defer="harga_treatment"]');
            const diskonInput = modal.querySelector('input[wire\\:model\\.defer="diskon"]');

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

        // Fungsi utama untuk reinitialize saat modal dibuka atau Livewire merender ulang
        function reinitUpdatePelayananModalHelpersEstetika() {
            initCleaveRupiah(); // Fungsi global Cleave (sudah dipakai di produk/obat)
            isiAwalHargaPelayananEditEstetika();
            reinitUpdatePelayananListenersEstetika();
        }

        // Listener utama
        document.addEventListener('DOMContentLoaded', reinitUpdatePelayananModalHelpersEstetika);
        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', reinitUpdatePelayananModalHelpersEstetika);
        });
        document.addEventListener('livewire:navigated', reinitUpdatePelayananModalHelpersEstetika);
    </script>
</dialog>
