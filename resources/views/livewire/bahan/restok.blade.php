<dialog id="restockModalBahanbaku" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closerestockModalBahanbaku', () => {
        document.getElementById('restockModalBahanbaku')?.close()
    })
">
    <div class="modal-box w-full max-w-xl">
        <h3 class="text-xl font-semibold mb-4">Bahan Baku Masuk</h3>

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

            <div class="flex gap-4">
                <label class="flex items-center gap-2">
                    <input type="radio" name="jenis_keluar" wire:model="jenis_keluar" value="besar" class="radio">
                    <span>Tambah Stok Besar</span>
                </label>

                <label class="flex items-center gap-2">
                    <input type="radio" name="jenis_keluar" wire:model="jenis_keluar" value="kecil" class="radio">
                    <span>Tambah Stok Kecil</span>
                </label>

                <label class="flex items-center gap-2">
                    <input type="radio" name="jenis_keluar" wire:model="jenis_keluar" value="besarkecil" class="radio">
                    <span>Tambah Stok Kecil Dari Stok Besar</span>
                </label>
            </div>

            <div>
                <label id="label-jumlah" class="label font-medium">
                    Jumlah Masuk <span class="text-error">*</span>
                </label>
                <input type="number" wire:model.lazy="jumlah" class="input input-bordered w-full @error('jumlah') input-error @enderror">
                @error('jumlah')
                    <span class="text-error text-sm">
                            Mohon Mengisi Jumlah Bahan Keluar Dengan Benar
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
                <button type="button" class="btn btn-error" onclick="document.getElementById('restockModalBahanbaku').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const label = document.getElementById('label-jumlah');

        document.querySelectorAll('input[name="jenis_keluar"]').forEach(radio => {
            radio.addEventListener('change', function () {
                switch (this.value) {
                    case 'besar':
                        label.innerHTML = 'Jumlah Stok Besar Masuk <span class="text-error">*</span>';
                        break;
                    case 'kecil':
                        label.innerHTML = 'Jumlah Stok Kecil Masuk <span class="text-error">*</span>';
                        break;
                    case 'besarkecil':
                        label.innerHTML = 'Jumlah Stok Besar Diambil Untuk Menambah Stok Kecil<span class="text-error">*</span>';
                        break;
                    default:
                        label.innerHTML = 'Jumlah Masuk <span class="text-error">*</span>';
                }
            });
        });
    });
</script>