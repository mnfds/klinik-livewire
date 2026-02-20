<dialog id="transferModal" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closetransferModal', () => {
        document.getElementById('transferModal')?.close()
    })
">
    <div class="modal-box w-full max-w-xl">
        <h3 class="text-xl font-semibold mb-4">Produk/Obat Transfer</h3>

        <form wire:submit.prevent="store" class="space-y-4">

            <div>
                <label class="label font-medium">Nama Produk/Obat<span class="text-error">*</span></label>
                <select class="select select-bordered w-full @error('produk_id') input-error @enderror" wire:model.lazy="produk_id">
                    <option value="">Pilih Produk/Obat</option>
                    @foreach ($produk as $b)
                        <option value="{{ $b->id }}">{{ $b->nama_dagang }}</option>
                    @endforeach
                </select>
                @error('produk_id')
                    <span class="text-error text-sm">Mohon Memilih Produk Dengan Benar</span>
                @enderror
            </div>

            <div>
                <label id="label-jumlah" class="label font-medium">Jumlah Ditransfer <span class="text-error">*</span></label>
                <input type="number" wire:model.lazy="jumlah" class="input input-bordered w-full @error('jumlah') input-error @enderror">
                @error('jumlah')
                    <span class="text-error text-sm">
                            Mohon Mengisi Jumlah Produk Ditransfer Dengan Benar
                    </span>
                @enderror
            </div>

            <div>
                <label class="label font-medium">Catatan</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="catatan">
            </div>

            <div class="modal-action justify-end pt-4">
                @can('akses', 'Persediaan Bahan Baku Masuk')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('transferModal').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>