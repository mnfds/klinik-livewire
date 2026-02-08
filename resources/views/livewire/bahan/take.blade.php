<dialog id="takeModalBahanbaku" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closetakeModalBahanbaku', () => {
        document.getElementById('takeModalBahanbaku')?.close()
    })
">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Bahan Baku Keluar</h3>

        <form wire:submit.prevent="store" class="space-y-4">

            <div>
                <label class="label font-medium">Nama Bahan Baku<span class="text-error">*</span></label>
                <select class="select select-bordered w-full @error('bahan_baku_id') input-error @enderror" wire:model.lazy="bahan_baku_id">
                    <option value="">Pilih Bahan Baku</option>
                    @foreach ($bahan as $b)
                        <option value="{{ $b->id }}">{{ $b->nama }}</option>
                    @endforeach
                </select>
                @error('bahan_baku_id')
                    <span class="text-error text-sm">Mohon Memilih Bahan Dengan Benar</span>
                @enderror
            </div>

            <div>
                <label class="label font-medium">Jumlah Keluar<span class="text-error">*</span></label>
                <input type="number" min="0" class="input input-bordered w-full @error('jumlah') input-error @enderror" wire:model.lazy="jumlah">
                @error('jumlah')
                    <span class="text-error text-sm">Mohon Mengisi Jumlah Bahan Keluar Dengan Benar</span>
                @enderror
            </div>

            <div>
                <label class="label font-medium">Catatan</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="catatan">
            </div>

            <div class="modal-action justify-end pt-4">
                @can('akses', 'Persediaan Bahan Baku Keluar')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('takeModalBahanbaku').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>
