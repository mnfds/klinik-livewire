<dialog id="modaleditrole" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeModal', () => {
        document.getElementById('modaleditrole')?.close()
    })
">
    <div class="modal-box max-w-6xl w-full">
        <h3 class="font-bold text-lg mb-4">Pilih Akses Untuk Role {{ $nama_jamkerja }}</h3>

        <form wire:submit.prevent="update" class="space-y-4">
            {{-- Checkbox Akses --}}
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold">Daftar Akses</span>
                </label>
                <div class="max-h-90 overflow-y-auto border p-2 rounded-box">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach ($allRole as $akses)
                            <label class="flex items-center space-x-2">
                                <input type="checkbox"
                                    value="{{ $akses->id }}"
                                    wire:model.defer="selectedRole"
                                    class="checkbox checkbox-primary">
                                <span>{{ $akses->nama_role }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-neutral" onclick="document.getElementById('modaleditrole').close()">Batal</button>
            </div>

        </form>
    </div>
</dialog>