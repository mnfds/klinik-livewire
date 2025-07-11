<dialog id="storeModalBundling" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeStoreModalBundling', () => {
        document.getElementById('storeModalBundling')?.close();
    })
">
    <div class="modal-box max-w-4xl">
        <h3 class="text-lg font-bold mb-4">Tambah Bundling</h3>

        <form wire:submit.prevent="store" class="space-y-4">

            {{-- Nama Bundling --}}
            <div class="form-control">
                <label class="label font-semibold">Nama Bundling</label>
                <input type="text" class="input input-bordered" placeholder="Masukkan nama bundling" wire:model.defer="nama" required>
            </div>

            {{-- Deskripsi --}}
            <div class="form-control">
                <label class="label font-semibold">Deskripsi</label>
                <textarea class="textarea textarea-bordered" placeholder="Deskripsi bundling" wire:model.defer="deskripsi"></textarea>
            </div>

            {{-- Harga --}}
            <div class="form-control">
                <label class="label font-semibold">Harga</label>
                <input type="text" class="input input-bordered input-rupiah" placeholder="Rp 0">
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga">
            </div>

            {{-- Diskon --}}
            <div class="form-control">
                <label class="label font-semibold">Diskon (%)</label>
                <input type="number" class="input input-bordered" placeholder="0-100" min="0" max="100" wire:model.defer="diskon" required>
            </div>

            {{-- Harga Bersih --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Bersih</label>
                <input type="text" class="input input-bordered input-rupiah bg-gray-100" placeholder="Otomatis terhitung" readonly>
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga_bersih">
            </div>

            {{-- Pelayanan Dinamis --}}
            <div class="form-control">
                <label class="label font-semibold">Pelayanan</label>
                @foreach ($pelayananInputs as $index => $row)
                    <div class="flex gap-2 mb-2">
                        <select class="select select-bordered w-full" wire:model.defer="pelayananInputs.{{ $index }}.pelayanan_id">
                            <option value="">-- Pilih Pelayanan --</option>
                            @foreach ($pelayananList as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_pelayanan }}</option>
                            @endforeach
                        </select>
                        <input type="number" min="1" class="input input-bordered w-24" placeholder="Jumlah"
                            wire:model.defer="pelayananInputs.{{ $index }}.jumlah">
                        <button type="button" class="btn btn-error btn-sm" wire:click="removePelayananRow({{ $index }})">✕</button>
                    </div>
                @endforeach
                <button type="button" class="btn btn-outline btn-sm" wire:click="addPelayananRow">+ Tambah Pelayanan</button>
            </div>

            {{-- Produk & Obat Dinamis --}}
            <div class="form-control">
                <label class="label font-semibold">Produk & Obat</label>
                @foreach ($produkInputs as $index => $row)
                    <div class="flex gap-2 mb-2">
                        <select class="select select-bordered w-full" wire:model.defer="produkInputs.{{ $index }}.produk_id">
                            <option value="">-- Pilih Produk / Obat --</option>
                            @foreach ($produkObatList as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_dagang }}</option>
                            @endforeach
                        </select>
                        <input type="number" min="1" class="input input-bordered w-24" placeholder="Jumlah"
                            wire:model.defer="produkInputs.{{ $index }}.jumlah">
                        <button type="button" class="btn btn-error btn-sm" wire:click="removeProdukRow({{ $index }})">✕</button>
                    </div>
                @endforeach
                <button type="button" class="btn btn-outline btn-sm" wire:click="addProdukRow">+ Tambah Produk / Obat</button>
            </div>

            {{-- Tombol Aksi --}}
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn" onclick="document.getElementById('storeModalBundling')?.close()">Batal</button>
            </div>
        </form>
    </div>

    {{-- Script Harga Otomatis --}}
    <script>
        function hitungHargaBersih() {
            const hargaInput = document.querySelector('input[wire\\:model\\.defer="harga"]');
            const diskonInput = document.querySelector('input[wire\\:model\\.defer="diskon"]');
            const hargaBersihInput = document.querySelector('input[wire\\:model\\.defer="harga_bersih"]');
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
            const hargaInput = document.querySelector('input[wire\\:model\\.defer="harga"]');
            const diskonInput = document.querySelector('input[wire\\:model\\.defer="diskon"]');

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

        function reinitBundlingModalHelpers() {
            initCleaveRupiah();
            reinitHargaBersihListeners();
        }

        document.addEventListener('DOMContentLoaded', reinitBundlingModalHelpers);
        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', reinitBundlingModalHelpers);
        });
        document.addEventListener('livewire:navigated', reinitBundlingModalHelpers);
    </script>
</dialog>
