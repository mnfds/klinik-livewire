<dialog id="storeModalBundling" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeStoreModalBundling', () => {
        document.getElementById('storeModalBundling')?.close();
    })
">
    <div class="modal-box max-w-4xl w-full">
        <h3 class="text-xl font-semibold mb-4">Tambah Bundling</h3>

        <form wire:submit.prevent="store" class="space-y-5">

            {{-- Nama Bundling --}}
            <div class="form-control">
                <label class="label font-semibold">Nama Bundling</label>
                <input type="text" class="input input-bordered w-full" placeholder="Masukkan nama bundling" wire:model.defer="nama" required>
            </div>

            {{-- Deskripsi --}}
            <div class="form-control">
                <label class="label font-semibold">Deskripsi</label>
                <textarea class="textarea textarea-bordered w-full" placeholder="Deskripsi bundling" wire:model.defer="deskripsi"></textarea>
            </div>

            {{-- Harga & Diskon --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label font-semibold">Harga</label>
                    <input type="text" class="input input-bordered input-rupiah w-full" placeholder="Rp 0">
                    <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga">
                </div>

                <div class="form-control">
                    <label class="label font-semibold">Diskon (%)</label>
                    <input type="number" class="input input-bordered w-full" placeholder="0-100" min="0" max="100" wire:model.defer="diskon" required>
                </div>
            </div>

            {{-- Harga Bersih --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Bersih</label>
                <input type="text" class="input input-bordered input-rupiah bg-base-200 w-full" placeholder="Otomatis terhitung" readonly>
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga_bersih">
            </div>

            {{-- Pelayanan Dinamis --}}
            <div class="form-control">
                <label class="label font-semibold">Pelayanan</label>
                <div class="space-y-2">
                    @foreach ($pelayananInputs as $index => $row)
                        <div class="flex flex-col md:flex-row gap-2">
                            <select class="select select-bordered w-full md:flex-1" wire:model.defer="pelayananInputs.{{ $index }}.pelayanan_id">
                                <option value="">-- Pilih Pelayanan --</option>
                                @foreach ($pelayananList as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_pelayanan }}</option>
                                @endforeach
                            </select>
                            <input type="number" min="1" class="input input-bordered w-full md:w-28" placeholder="Jumlah"
                                wire:model.defer="pelayananInputs.{{ $index }}.jumlah">
                            <button type="button" class="btn btn-error btn-sm" wire:click="removePelayananRow({{ $index }})">✕</button>
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-outline btn-sm mt-1" wire:click="addPelayananRow">+ Tambah Pelayanan</button>
                </div>
            </div>

            {{-- treatment Dinamis --}}
            <div class="form-control">
                <label class="label font-semibold">Pelayanan Estetika</label>
                <div class="space-y-2">
                    @foreach ($treatmentInputs as $index => $row)
                        <div class="flex flex-col md:flex-row gap-2">
                            <select class="select select-bordered w-full md:flex-1" wire:model.defer="treatmentInputs.{{ $index }}.treatments_id">
                                <option value="">-- Pilih Pelayanan Estetika --</option>
                                @foreach ($treatmentList as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_treatment }}</option>
                                @endforeach
                            </select>
                            <input type="number" min="1" class="input input-bordered w-full md:w-28" placeholder="Jumlah"
                                wire:model.defer="treatmentInputs.{{ $index }}.jumlah">
                            <button type="button" class="btn btn-error btn-sm" wire:click="removeTreatmentRow({{ $index }})">✕</button>
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-outline btn-sm mt-1" wire:click="addTreatmentRow">+ Tambah Pelayanan Estetika</button>
                </div>
            </div>

            {{-- Produk & Obat Dinamis --}}
            <div class="form-control">
                <label class="label font-semibold">Produk & Obat</label>
                <div class="space-y-2">
                    @foreach ($produkInputs as $index => $row)
                        <div class="flex flex-col md:flex-row gap-2">
                            <select class="select select-bordered w-full md:flex-1" wire:model.defer="produkInputs.{{ $index }}.produk_id">
                                <option value="">-- Pilih Produk / Obat --</option>
                                @foreach ($produkObatList as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_dagang }}</option>
                                @endforeach
                            </select>
                            <input type="number" min="1" class="input input-bordered w-full md:w-28" placeholder="Jumlah"
                                wire:model.defer="produkInputs.{{ $index }}.jumlah">
                            <button type="button" class="btn btn-error btn-sm" wire:click="removeProdukRow({{ $index }})">✕</button>
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-outline btn-sm mt-1" wire:click="addProdukRow">+ Tambah Produk / Obat</button>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="modal-action flex justify-end gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-neutral" onclick="document.getElementById('storeModalBundling')?.close()">Batal</button>
            </div>
        </form>
    </div>

    {{-- Script tetap seperti sebelumnya --}}
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
