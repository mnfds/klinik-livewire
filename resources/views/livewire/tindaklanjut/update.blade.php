<dialog id="updateModalTindakLanjut" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeupdateModalTindakLanjut', () => {
        document.getElementById('updateModalTindakLanjut')?.close()
    })
">
    <div class="modal-box w-full max-w-4xl">
        <h3 class="text-xl font-semibold mb-4">Tambah Layanan pada pasien</h3>

        <div class="col-span-1 sm:col-span-2 flex justify-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn btn-neutral" onclick="document.getElementById('updateModalTindakLanjut').close()">Batal</button>
        </div>
    </div>
</dialog>