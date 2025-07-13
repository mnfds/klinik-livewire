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

            {{-- Harga Dasar --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Dasar</label>
                <input
                    type="text"
                    class="input input-bordered w-full"
                    id="harga_pelayanan_input"
                    oninput="formatRupiahToHidden(this, 'harga_pelayanan_hidden')"
                    value="{{ number_format($harga_pelayanan ?? 0, 0, ',', '.') }}"
                    inputmode="numeric"
                >
                <input type="hidden" wire:model="harga_pelayanan" id="harga_pelayanan_hidden">
            </div>

            {{-- Diskon (%) --}}
            <div class="form-control">
                <label class="label font-semibold">Diskon (%)</label>
                <input type="number" min="0" max="100" class="input input-bordered w-full" wire:model="diskon">
            </div>

            {{-- Harga Bersih --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Bersih (setelah diskon)</label>
                <input
                    type="text"
                    class="input input-bordered bg-base-200 w-full"
                    value="Rp {{ number_format((float)$harga_pelayanan - ((float)$harga_pelayanan * (float)$diskon / 100), 0, ',', '.') }}"
                    readonly
                >
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

    <script>
        function formatRupiahToHidden(inputEl, hiddenId) {
            const raw = inputEl.value.replace(/[^\d]/g, '');
            const formatted = new Intl.NumberFormat('id-ID').format(raw);

            inputEl.value = formatted;
            const hidden = document.getElementById(hiddenId);
            hidden.value = raw;
            hidden.dispatchEvent(new Event('input')); // Trigger Livewire
        }
    </script>
</dialog>
