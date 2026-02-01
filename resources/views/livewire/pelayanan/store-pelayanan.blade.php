<dialog id="storeModalPelayanan" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeStoreModalPelayanan', () => {
        document.getElementById('storeModalPelayanan')?.close()
    })
">
    <div class="modal-box w-full max-w-2xl">
        <h3 class="text-xl font-semibold mb-4">Tambah Pelayanan Medis</h3>

        <form wire:submit.prevent="store" class="space-y-4">

            {{-- Nama Pelayanan --}}
            <div class="form-control">
                <label class="label font-semibold">Nama Pelayanan <span class="text-error">*</span></label>
                <input type="text" class="input input-bordered w-full @error('nama_pelayanan') input-error @enderror" wire:model.lazy="nama_pelayanan">
                @error('nama_pelayanan')
                    <span class="text-error text-sm mt-1">
                        Mohon Mengisi Nama Layanan Medis
                    </span>
                @enderror
            </div>

            {{-- Harga --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Dasar <span class="text-error">*</span></label>
                <input type="text" class="input input-bordered input-rupiah w-full @error('harga_pelayanan') input-error @enderror" placeholder="Rp 0">
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga_pelayanan">
                @error('harga_pelayanan')
                    <span class="text-error text-sm mt-1">
                        Mohon Mengisi Harga Layanan Medis
                    </span>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Potongan --}}
                <div>
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
                @can('akses', 'Pelayanan Medis Tambah')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-neutral" onclick="document.getElementById('storeModalPelayanan').close()">Batal</button>
            </div>
        </form>
    </div>

    {{-- Script --}}
<script>
    function hitungHargaBersihPelayanan() {
        const root = document.querySelector('#storeModalPelayanan');

        const hargaInput = root.querySelector('input[wire\\:model\\.defer="harga_pelayanan"]')?.previousElementSibling;
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

    function reinitHargaBersihListenersPelayanan() {
        const root = document.querySelector('#storeModalPelayanan');

        const hargaInput = root.querySelector('input[wire\\:model\\.defer="harga_pelayanan"]')?.previousElementSibling;
        const potonganInput = root.querySelector('input[wire\\:model\\.defer="potongan"]')?.previousElementSibling;
        const diskonInput = root.querySelector('input[wire\\:model\\.defer="diskon"]');

        [hargaInput, potonganInput, diskonInput].forEach(el => {
            if (el) {
                el.removeEventListener('input', hitungHargaBersihPelayanan);
                el.addEventListener('input', hitungHargaBersihPelayanan);
            }
        });

        hitungHargaBersihPelayanan();
    }

    function reinitPelayananModalHelpers() {
        initCleaveRupiah(); 
        reinitHargaBersihListenersPelayanan();
    }

    document.addEventListener('DOMContentLoaded', reinitPelayananModalHelpers);
    document.addEventListener('livewire:load', () => {
        Livewire.hook('message.processed', reinitPelayananModalHelpers);
    });
    document.addEventListener('livewire:navigated', reinitPelayananModalHelpers);
</script>

</dialog>
