<dialog id="modaleditmutasibahan" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closemodaleditmutasibahan', () => {
        document.getElementById('modaleditmutasibahan')?.close()
    });
    Livewire.on('openModal', () => {
        document.getElementById('modaleditmutasibahan')?.showModal()
    });
">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Edit Data Bahan {{ $tipe }}</h3>

        <form wire:submit.prevent="update" class="space-y-4">

            <div>
                <label class="label font-medium">Nama Bahan Baku</label>
                <select class="select select-bordered w-full" wire:model.defer="bahan_baku_id" required>
                    <option value="">Pilih Barang</option>
                    @foreach ($listBahan as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label font-medium">Tipe</label>
                <select class="select select-bordered w-full" wire:model.defer="tipe" required>
                    <option value="">Pilih tipe</option>
                    <option value="masuk">Masuk</option>
                    <option value="keluar">Keluar</option>
                </select>
            </div>

            <div>
                <label class="label font-medium">Jumlah</label>
                <input type="number" class="input input-bordered w-full" wire:model.defer="jumlah" required>
            </div>

            <div>
                <label class="label font-medium">Staff Terkait</label>
                <select class="select select-bordered w-full" wire:model.defer="diajukan_oleh" required>
                    <option value="">Pilih Staff</option>
                    @foreach ($listOrang as $id => $nama_lengkap)
                        <option value="{{ $nama_lengkap }}">{{ $nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label font-medium">Catatan</label>
                <textarea wire:model.defer="catatan" class="textarea textarea-bordered w-full" rows="3"></textarea>
            </div>

            <div class="modal-action justify-end pt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-error" onclick="document.getElementById('modaleditmutasibahan').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>
