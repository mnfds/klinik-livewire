<dialog id="modaleditbahanbaku" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closemodaleditbahanbaku', () => {
        document.getElementById('modaleditbahanbaku')?.close()
    })
">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Edit Data "{{ $nama }}"</h3>

        <form wire:submit.prevent="update" class="space-y-4">

            <div>
                <label class="label font-medium">Nama Bahan Baku</label>
                <input type="text" class="input input-bordered w-full" wire:model.defer="nama" required>
            </div>

            <div>
                <label class="label font-medium">Satuan</label>
                <select class="select select-bordered w-full" wire:model.defer="satuan">
                    <option value="">Pilih Satuan</option>
                    <option value="pcs">Pcs</option>
                    <option value="unit">Unit</option>
                    <option value="lusin">Lusin</option>
                    <option value="box">Box</option>
                </select>
            </div>

            <div>
                <label class="label font-medium">Kode</label>
                <input type="text" class="input input-bordered w-full" wire:model.defer="kode">
            </div>

            <div>
                <label class="label font-medium">Lokasi Disimpan</label>
                <select class="select select-bordered w-full" wire:model.defer="lokasi">
                    <option value="">Pilih Lokasi</option>
                    <option value="gudang besar">Gudang Besar</option>
                    <option value="gudang kecil">Gudang Kecil</option>
                </select>
            </div>

            <div>
                <label class="label font-medium">Keterangan</label>
                <textarea wire:model.defer="keterangan" class="textarea textarea-bordered w-full" rows="3"></textarea>
            </div>

            <div class="modal-action justify-end pt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-error" onclick="document.getElementById('modaleditbahanbaku').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>