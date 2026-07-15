<div>
    <dialog id="modalEditShift" class="modal"
        wire:ignore.self
        x-data
        x-on:open-modal-shift.window="$el.showModal()"
        x-on:close-modal-shift.window="$el.close()"
    >
        <div class="modal-box">
            <div class="mb-4">
                <h3 class="font-bold text-lg">
                    Ubah Shift — {{ $nama_user?->biodata?->nama_lengkap ?? $nama_user?->dokter?->nama_dokter ?? '-' }}
                </h3>
                <p class="text-sm text-gray-500">
                    {{ $tanggal ? \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') : '-' }}
                </p>
            </div>

            <div class="space-y-2">
                @foreach ($jamKerjaList as $jamKerja)
                    <button wire:click="saveShift({{ $jamKerja->id }})" class="btn w-full justify-start {{ $jamKerja->tipe_shift === 'libur' ? 'btn-error' : 'btn-secondary' }}">
                        {{ $jamKerja->nama_shift }} ({{ \Carbon\Carbon::parse($jamKerja->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jamKerja->jam_selesai)->format('H:i') }})
                    </button>
                @endforeach

                <button wire:click="hapusShift" class="btn btn-neutral w-full justify-start">
                    Hapus / Kosongkan
                </button>
            </div>

            <div class="modal-action mt-4">
                <button type="button" class="btn btn-error" onclick="document.getElementById('modalEditShift').close()">
                    <i class="fa-solid fa-x"></i>Batal
                </button>
            </div>
        </div>
    </dialog>
</div>