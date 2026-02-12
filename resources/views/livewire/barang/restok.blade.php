<dialog id="restockModalBarang" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closerestockModalBarang', () => {
        document.getElementById('restockModalBarang')?.close()
    })
">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Barang Masuk</h3>

        <form wire:submit.prevent="store" class="space-y-4">

            <div>
                <label class="label font-medium">Nama Barang<span class="text-error">*</span></label>
                <select class="select select-bordered w-full @error('barang_id') input-error @enderror" wire:model.lazy="barang_id">
                    <option value="">Pilih Barang</option>
                    @foreach ($barang as $b)
                        <option value="{{ $b->id }}">{{ $b->nama }}</option>
                    @endforeach
                </select>
                @error('barang_id')
                    <span class="text-error text-sm">Mohon Memilih Barang Dengan Benar</span>
                @enderror
            </div>

            <div>
                <label class="label font-medium">Jumlah<span class="text-error">*</span></label>
                <input type="number" min="0" class="input input-bordered w-full @error('jumlah') input-error @enderror" wire:model.lazy="jumlah">
                @error('jumlah')
                    <span class="text-error text-sm">Mohon Mengisi Jumlah Barang Dengan Benar</span>
                @enderror
            </div>

            <div>
                <label class="label font-medium">Catatan</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="catatan">
            </div>

            <div class="modal-action justify-end pt-4">
                @can('akses', 'Persediaan Barang Masuk')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('restockModalBarang').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>