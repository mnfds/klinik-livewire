<dialog id="modaleditbahanlayanan" class="modal" wire:ignore.self x-data x-init="
        Livewire.on('closeModalbahanlayanan', () => {
            document.getElementById('modaleditbahanlayanan')?.close()
        })
    ">
    <div class="modal-box max-w-2xl">
        <h3 class="font-bold text-lg mb-4">
            Pilih Bahan Baku Untuk Layanan {{ $nama_layanan }}
        </h3>

        <form wire:submit.prevent="updatelayanan" class="space-y-4">

            {{-- Checkbox Bahan Baku --}}
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold">Daftar Bahan Baku</span>
                </label>

                <div class="space-y-2 max-h-60 overflow-y-auto border p-2 rounded-box">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach ($allBahan as $bahan)
                            <label class="flex items-center space-x-2">
                                <input type="checkbox"
                                    value="{{ $bahan->id }}"
                                    wire:model.defer="selectedBahan"
                                    class="checkbox checkbox-primary">
                                <span>{{ $bahan->nama }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="modal-action">
                @can('akses', 'Pelayanan Estetika Tambah Bahan')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-neutral" 
                        onclick="document.getElementById('modaleditbahanlayanan').close()">
                    Batal
                </button>
            </div>

        </form>
    </div>
</dialog>
    