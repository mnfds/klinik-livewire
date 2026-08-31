<dialog id="modalapprovalpengajuancuti" class="modal" wire:ignore.self x-data x-init="Livewire.on('closemodalapprovalpengajuancuti', () => { document.getElementById('modalapprovalpengajuancuti')?.close() })">
    <div class="modal-box w-full max-w-lg">
        @if ($pengajuan)
            @php
                $nama = $pengajuan->user->biodata?->nama_lengkap
                    ?? $pengajuan->user->dokter?->nama_dokter
                    ?? '-';
            @endphp

            <h3 class="text-xl font-semibold mb-1">
                {{ $mode === 'approve' ? 'Setujui Pengajuan Cuti' : 'Tolak Pengajuan Cuti' }}
            </h3>
            <p class="text-sm text-base-content/60 mb-4">{{ $nama }}</p>

            {{-- Ringkasan tanggal --}}
            <div class="bg-base-200 rounded-box p-3 mb-4 max-h-40 overflow-y-auto">
                <p class="text-sm font-medium mb-1">Tanggal Cuti ({{ $pengajuan->tanggals->count() }} hari)</p>
                <div class="flex flex-wrap gap-1">
                    @foreach ($pengajuan->tanggals->sortBy('tanggal') as $t)
                        <span class="badge badge-sm badge-outline">{{ $t->tanggal->format('d M Y') }}</span>
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <p class="text-sm font-medium mb-1">Alasan Karyawan</p>
                <p class="text-sm text-base-content/70">{{ $pengajuan->alasan }}</p>
            </div>

            @error('mode')
                <div class="alert alert-error text-sm py-2 mb-4">{{ $message }}</div>
            @enderror

            <form wire:submit.prevent="confirm" class="space-y-4">
                <div>
                    <label class="label font-medium">
                        Catatan Admin
                        @if ($mode === 'tolak')
                            <span class="text-error">*</span>
                        @else
                            <span class="text-base-content/40 font-normal">(opsional)</span>
                        @endif
                    </label>
                    <textarea
                        class="textarea textarea-bordered w-full @error('catatan_admin') textarea-error @enderror"
                        rows="3"
                        wire:model="catatan_admin"
                        placeholder="{{ $mode === 'tolak' ? 'Alasan penolakan...' : 'Catatan tambahan (opsional)...' }}"
                    ></textarea>
                    @error('catatan_admin')
                        <span class="text-error text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modal-action justify-end pt-2">
                    <button type="submit" class="btn btn-sm {{ $mode === 'approve' ? 'btn-success' : 'btn-warning' }}">
                        {{ $mode === 'approve' ? 'Ya, Setujui' : 'Ya, Tolak' }}
                    </button>
                    <button type="button" class="btn btn-error btn-sm" onclick="document.getElementById('modalapprovalpengajuancuti').close()">
                        Batal
                    </button>
                </div>
            </form>
        @else
            <p class="text-sm text-base-content/60">Memuat data...</p>
        @endif
    </div>

    {{-- Backdrop --}}
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>