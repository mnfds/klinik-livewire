<dialog id="my_modal_1" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeModal', () => {
        document.getElementById('my_modal_1')?.close()
    })
">
    <div class="modal-box">
        <h3 class="text-lg font-bold">Edit Jam Kerja</h3>

        <form wire:submit.prevent="update">

            <div class="form-control">
                <label class="label">Nama Shift</label>
                <input type="text" class="input input-bordered" wire:model.lazy="nama_shift">
            </div>

            <div class="form-control mt-2">
                <label class="label">Tipe Shift</label>
                <select class="select select-bordered" wire:model.lazy="tipe_shift">
                    <option value="">Pilih Tipe</option>
                    <option value="full">Full</option>
                    <option value="pagi">Pagi</option>
                    <option value="siang">Siang</option>
                    <option value="malam">Malam</option>
                    <option value="libur">Libur</option>
                    <option value="mp">MP</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2 mt-2">
                <div class="form-control">
                    <label class="label">Jam Mulai</label>
                    <input type="time" class="input input-bordered" wire:model.lazy="jam_mulai">
                </div>
                <div class="form-control">
                    <label class="label">Jam Selesai</label>
                    <input type="time" class="input input-bordered" wire:model.lazy="jam_selesai">
                </div>
            </div>

            <div class="form-control mt-2">
                <label class="cursor-pointer label">
                    <span class="label-text">Lewat Hari?</span> 
                    <input type="checkbox" class="checkbox" wire:model.lazy="lewat_hari">
                </label>
            </div>

            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn" onclick="document.getElementById('my_modal_1').close()">Batal</button>
            </div>
        </form>
    </div>
    <script>
        Livewire.on('closeModal', () => {
            const modal = document.getElementById('my_modal_1');
            if (modal?.open) {
                modal.close();
            }
        });
    </script>
</dialog>
