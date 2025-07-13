<dialog id="my_modal_1" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeModal', () => {
        document.getElementById('my_modal_1')?.close()
    })
">
    <div class="modal-box w-full max-w-2xl">
        <h3 class="text-xl font-semibold mb-4">Edit Jam Kerja</h3>

        <form wire:submit.prevent="update" class="space-y-4">

            <div>
                <label class="label font-medium">Nama Shift</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="nama_shift">
            </div>

            <div>
                <label class="label font-medium">Tipe Shift</label>
                <select class="select select-bordered w-full" wire:model.lazy="tipe_shift">
                    <option value="">Pilih Tipe</option>
                    <option value="full">Full</option>
                    <option value="pagi">Pagi</option>
                    <option value="siang">Siang</option>
                    <option value="malam">Malam</option>
                    <option value="libur">Libur</option>
                    <option value="mp">MP</option>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label font-medium">Jam Mulai</label>
                    <input type="time" class="input input-bordered w-full" wire:model.lazy="jam_mulai">
                </div>
                <div>
                    <label class="label font-medium">Jam Selesai</label>
                    <input type="time" class="input input-bordered w-full" wire:model.lazy="jam_selesai">
                </div>
            </div>

            <div>
                <label class="label cursor-pointer justify-between">
                    <span class="label-text font-medium">Lewat Hari?</span>
                    <input type="checkbox" class="toggle toggle-primary" wire:model.lazy="lewat_hari" />
                </label>
            </div>

            <div class="modal-action justify-end mt-6">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-error" onclick="document.getElementById('my_modal_1').close()">Batal</button>
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
