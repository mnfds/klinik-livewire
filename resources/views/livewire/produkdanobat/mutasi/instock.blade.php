<dialog id="instockModalProdukDanObat" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeinstockModalProdukDanObat', () => {
        document.getElementById('restockModalBahanbaku')?.close()
    })
">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Produk/Obat Masuk</h3>

        <form wire:submit.prevent="store" class="space-y-4">

            <div>
                <label class="label font-medium">Nama Produk/Obat</label>
                <select class="select select-bordered w-full" wire:model.lazy="produk_id">
                    <option value="">Pilih Produk/Obat</option>
                    @foreach ($produkobat as $b)
                        <option value="{{ $b->id }}">{{ $b->nama_dagang }}</option>
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
                @can('akses', 'Produk & Obat Masuk')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('instockModalProdukDanObat').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>