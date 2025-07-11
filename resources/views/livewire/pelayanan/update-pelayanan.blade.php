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
    <div class="modal-box">
        <h3 class="font-bold text-lg mb-2">Edit Pelayanan</h3>

        <form wire:submit.prevent="update">
            {{-- Nama Pelayanan --}}
            <div class="form-control mb-2">
                <label class="label">Nama Pelayanan</label>
                <input type="text" class="input input-bordered" wire:model="nama_pelayanan">
            </div>

            {{-- Harga Pelayanan --}}
            <div class="form-control mb-2">
                <label class="label">Harga Pelayanan</label>
                <input type="text" class="input input-bordered input-rupiah" inputmode="numeric">
                <input type="hidden" wire:model="harga_pelayanan" class="input-rupiah-hidden">
            </div>

            {{-- Diskon (%) --}}
            <div class="form-control mb-2">
                <label class="label">Diskon (%)</label>
                <input type="number" min="0" max="100" class="input input-bordered" wire:model="diskon">
            </div>

            {{-- Harga Bersih --}}
            <div class="form-control mb-2">
                <label class="label">Harga Bersih</label>
                <input type="text" class="input input-bordered bg-base-200" value="Rp {{ number_format($harga_bersih ?? 0, 0, ',', '.') }}" readonly>
            </div>

            {{-- Deskripsi --}}
            <div class="form-control mb-2">
                <label class="label">Deskripsi</label>
                <input type="text" class="input input-bordered" wire:model="deskripsi">
            </div>

            {{-- Tombol --}}
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</dialog>