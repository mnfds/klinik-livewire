<dialog id="modaleditreservasi" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeModal', () => {
        document.getElementById('modaleditreservasi')?.close()
    })
">
    <div class="modal-box w-full max-w-4xl">
        <h3 class="text-xl font-semibold mb-4">Edit Pasien Reservasi</h3>

        <form wire:submit.prevent="update" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            {{-- Pasien --}}
            <div>
                <label class="label font-semibold">Pasien</label>
                <input type="text" class="input input-bordered w-full" readonly value="{{ $nama_pasien }}">
                <input type="hidden" wire:model="pasien_id">
                @error('pasien_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Poliklinik --}}
            <div>
                <label class="label font-semibold">Poliklinik</label>
                <select class="select select-bordered w-full" wire:model="poli_id">
                    <option value="">-- Pilih Poliklinik --</option>
                    @foreach ($polis as $poli)
                        <option value="{{ $poli->id }}">{{ $poli->nama_poli }}</option>
                    @endforeach
                </select>
                @error('poli_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Dokter --}}
            <div>
                <label class="label font-semibold">Dokter</label>
                <select class="select select-bordered w-full" wire:model="dokter_id">
                    <option value="">-- Pilih Dokter --</option>
                    @foreach ($dokters as $dokter)
                        <option value="{{ $dokter->id }}">{{ $dokter->nama_dokter }}</option>
                    @endforeach
                </select>
                @error('dokter_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Tanggal Reservasi --}}
            <div>
                <label class="label font-semibold">Tanggal Reservasi</label>
                <input type="date" class="input input-bordered w-full" wire:model="tanggal_reservasi">
                @error('tanggal_reservasi') <span class="text-error text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Jam Reservasi --}}
            <div>
                <label class="label font-semibold">Jam Reservasi</label>
                <input type="time" class="input input-bordered w-full" wire:model="jam_reservasi">
                @error('jam_reservasi') <span class="text-error text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="label font-semibold">Status Reservasi</label>
                <select class="select select-bordered w-full" wire:model="status">
                    <option value="belum bayar">Belum Bayar</option>
                    <option value="belum lunas">Belum Lunas</option>
                    <option value="lunas">Lunas</option>
                    <option value="selesai">Selesai</option>
                    <option value="batal">Batal</option>
                </select>
                @error('status') <span class="text-error text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Nominal Pembayaran --}}
            <div>
                <label class="label font-semibold">Nominal Pembayaran</label>
                <input type="number" min="0" class="input input-bordered w-full" wire:model="nominal_pembayaran" placeholder="Contoh: 150000">
                @error('nominal_pembayaran') <span class="text-error text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Catatan --}}
            <div class="sm:col-span-2">
                <label class="label font-semibold">Catatan</label>
                <textarea class="textarea textarea-bordered w-full" wire:model="catatan" rows="3" placeholder="Tambahkan catatan tambahan jika diperlukan..."></textarea>
                @error('catatan') <span class="text-error text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="col-span-1 sm:col-span-2 flex justify-end gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-neutral" onclick="document.getElementById('modaleditreservasi').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>