<dialog id="modaleditpelayanan" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeModal', () => {
        document.getElementById('modaleditpelayanan')?.close()
    });
    Livewire.on('setHargaPelayanan', (value) => {
        document.querySelectorAll('.input-rupiah').forEach((input) => {
            if (input._cleave) input._cleave.setRawValue(value);
        });
    });
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
                <input type="text" class="input input-bordered input-rupiah w-full" inputmode="numeric">
                <input type="hidden" wire:model="harga_pelayanan" class="input-rupiah-hidden">
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
                    value="Rp {{ number_format($harga_bersih ?? 0, 0, ',', '.') }}"
                    readonly
                >
            </div>

            {{-- Deskripsi --}}
            <div class="form-control">
                <label class="label font-semibold">Deskripsi</label>
                <input type="text" class="input input-bordered w-full" wire:model="deskripsi">
            </div>

            {{-- Tombol --}}
            <div class="modal-action flex justify-end gap-2 pt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</dialog>
