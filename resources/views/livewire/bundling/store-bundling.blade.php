<dialog id="storeModalBundling" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeStoreModalBundling', () => {
        document.getElementById('storeModalBundling')?.close()

        // Reset multiselect & hidden input
        document.querySelectorAll('.multiple-pelayanan, .multiple-produk').forEach(selectEl => {
            if (selectEl._choices) {
                selectEl._choices.clearStore();
                selectEl._choices.setValue([]);
            }
            const hiddenInput = selectEl.parentElement.querySelector('input[type=hidden][wire\\:model\\.defer]');
            if (hiddenInput) {
                hiddenInput.value = '[]';
                hiddenInput.dispatchEvent(new Event('input'));
            }
        });
    })
">
    <div class="modal-box">
        <h3 class="text-lg font-bold">Tambah Bundling</h3>

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
                <input type="text" class="input input-bordered" placeholder="0-100" wire:model.defer="diskon" required>
            </div>

            {{-- Harga Bersih --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Bersih</label>
                <input type="text" class="input input-bordered input-rupiah bg-gray-100" placeholder="Otomatis terhitung" readonly>
                <input type="hidden" class="input-rupiah-hidden" wire:model.defer="harga_bersih">
            </div>

            {{-- Pelayanan --}}
            <div class="form-control">
                <label class="label font-semibold">Pelayanan</label>
                <select class="select select-bordered multiple-pelayanan" multiple>
                    @foreach ($pelayananList as $item)
                        <option value="{{ $item->id }}">{{ $item->nama_pelayanan }}</option>
                    @endforeach
                </select>
                <input type="hidden" wire:model.defer="pelayanan">
            </div>

            {{-- Produk & Obat --}}
            <div class="form-control">
                <label class="label font-semibold">Produk & Obat</label>
                <select class="select select-bordered multiple-produk" multiple>
                    @foreach ($produkObatList as $item)
                        <option value="{{ $item->id }}">{{ $item->nama_dagang }}</option>
                    @endforeach
                </select>
                <input type="hidden" wire:model.defer="produk_obat">
            </div>

            {{-- Tombol Aksi --}}
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn" onclick="document.getElementById('storeModalBundling')?.close()">Batal</button>
            </div>
        </form>
    </div>

    {{-- Harga Bersih Calculator --}}
    <script>
        function hitungHargaBersih() {
            const hargaInput = document.querySelector('input[wire\\:model\\.defer="harga"]');
            const diskonInput = document.querySelector('input[wire\\:model\\.defer="diskon"]');
            const hargaBersihInput = document.querySelector('input[wire\\:model\\.defer="harga_bersih"]');
            const hargaBersihDisplay = hargaBersihInput?.previousElementSibling; // .input-rupiah (readonly)

            if (!hargaInput || !diskonInput || !hargaBersihInput || !hargaBersihDisplay) return;

            const harga = parseInt(hargaInput.value.replace(/\D/g, '') || 0);
            const diskon = parseFloat(diskonInput.value || 0);
            const hargaBersih = Math.max(0, Math.round(harga - (harga * (diskon / 100))));

            hargaBersihInput.value = hargaBersih;

            // Jangan inisialisasi Cleave, cukup gunakan instance global yang sudah ada
            if (hargaBersihDisplay._cleave) {
                hargaBersihDisplay._cleave.setRawValue(hargaBersih);
            } else {
                hargaBersihDisplay.value = hargaBersih; // fallback (kasus langka)
            }

            hargaBersihInput.dispatchEvent(new Event('input'));
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelector('input[wire\\:model\\.defer="harga"]')?.addEventListener('input', hitungHargaBersih);
            document.querySelector('input[wire\\:model\\.defer="diskon"]')?.addEventListener('input', hitungHargaBersih);
        });

        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', () => {
                initCleaveRupiah();
                hitungHargaBersih();
            });
        });
    </script>

    {{-- Init Choices.js --}}
    <script>
        function initChoicesMultiSelect() {
            document.querySelectorAll('.multiple-pelayanan, .multiple-produk').forEach((selectEl) => {
                if (selectEl._choices) return;

                const hiddenInput = selectEl.parentElement.querySelector('input[type="hidden"][wire\\:model\\.defer]');
                const instance = new Choices(selectEl, {
                    removeItemButton: true,
                    placeholderValue: 'Pilih item',
                    searchPlaceholderValue: 'Cari...',
                    shouldSort: false,
                    noResultsText: 'Tidak ditemukan',
                    noChoicesText: 'Tidak ada pilihan',
                    itemSelectText: '',
                });

                selectEl._choices = instance;

                function syncToHidden() {
                    const selectedValues = Array.from(selectEl.selectedOptions).map(opt => opt.value);
                    hiddenInput.value = JSON.stringify(selectedValues.length ? selectedValues : []);
                    hiddenInput.dispatchEvent(new Event('input'));
                }

                selectEl.addEventListener('change', syncToHidden);
                selectEl.addEventListener('removeItem', () => setTimeout(syncToHidden, 0));

                syncToHidden();
            });
        }

        document.addEventListener('DOMContentLoaded', initChoicesMultiSelect);

        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', initChoicesMultiSelect);
        });
    </script>

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
            hargaInput.removeEventListener('input', hitungHargaBersih); // pastikan tidak ganda
            hargaInput.addEventListener('input', hitungHargaBersih);
        }

        if (diskonInput) {
            diskonInput.removeEventListener('input', hitungHargaBersih);
            diskonInput.addEventListener('input', hitungHargaBersih);
        }

        // Hitung awal
        hitungHargaBersih();
    }

    function reinitBundlingModalHelpers() {
        initChoicesMultiSelect();
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