<dialog id="takeModalBarang" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closetakeModalBarang', () => {
        document.getElementById('takeModalBarang')?.close()
    })
">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Barang Keluar</h3>

        <form wire:submit.prevent="store" class="space-y-4">

            <div>
                <label class="label font-medium">Nama Barang</label>
                <select class="select select-bordered w-full" wire:model.lazy="barang_id">
                    <option value="">Pilih Satuan</option>
                    @foreach ($barang as $b)
                        <option value="{{ $b->id }}">{{ $b->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label font-medium">Jumlah</label>
                <input type="number" min="0" max="40" class="input input-bordered w-full" wire:model.lazy="jumlah">
            </div>

            <div>
                <label class="label font-medium">Catatan</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="catatan">
            </div>

            <div class="modal-action justify-end pt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-error" onclick="document.getElementById('takeModalBarang').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>
