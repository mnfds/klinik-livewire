<div>
    <dialog id="modalEditShift" class="modal"
        wire:ignore
        x-data
        x-on:open-modal-shift.window="$el.showModal()"
        x-on:close-modal-shift.window="$el.close()"
    >
        <div class="modal-box">
            <h3 class="font-bold text-lg">Ubah Shift</h3>

            <div class="space-y-2">
                @foreach ($jamKerjaList as $jamKerja)
                    <button wire:click="saveShift({{ $jamKerja->id }})" class="btn btn-success w-full justify-start">
                        {{ $jamKerja->nama_shift }}
                    </button>
                @endforeach

                <button wire:click="hapusShift" class="btn btn-error w-full justify-start">
                    Hapus / Kosongkan
                </button>
            </div>

            <div class="modal-action mt-4">
                <button type="button" class="btn btn-sm" onclick="document.getElementById('modalEditShift').close()">
                    Batal
                </button>
            </div>
        </div>
    </dialog>
</div>