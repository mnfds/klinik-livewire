<dialog id="modaltolakreservasi" class="modal" wire:ignore.self x-data x-on:closemodaltolakreservasi.window="$el.close()">
    <div class="modal-box w-full max-w-lg">
        @if ($permintaan)
            <h3 class="text-xl font-semibold mb-1">Tolak Permintaan Reservasi</h3>
            <p class="text-sm text-base-content/60 mb-4">
                Permintaan dari <strong>{{ $permintaan->nama }}</strong> ({{ $permintaan->no_telp }})
                @if ($permintaan->pasien_baru)
                    <span class="badge badge-warning badge-sm ml-1">Pasien Baru</span>
                @else
                    <span class="badge badge-ghost badge-sm ml-1">Pasien Lama</span>
                @endif
            </p>

            <form wire:submit.prevent="confirm" class="grid grid-cols-1 gap-4">
                <div>
                    <label class="label font-semibold">Alasan Penolakan</label>
                    <select class="select select-bordered w-full @error('alasan_penolakan') select-error @enderror" wire:model="alasan_penolakan">
                        <option value="">-- Pilih Alasan --</option>
                        @foreach (\App\Livewire\Reservasi\Rejection::ALASAN as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('alasan_penolakan') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="submit" class="btn btn-warning btn-sm">Ya, Tolak</button>
                    <button type="button" class="btn btn-neutral btn-sm" onclick="document.getElementById('modaltolakreservasi').close()">Batal</button>
                </div>
            </form>
        @else
            <p class="text-sm text-base-content/60">Memuat data...</p>
        @endif
    </div>

    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>