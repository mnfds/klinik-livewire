<dialog id="modalsetujuireservasi" class="modal" wire:ignore.self x-data x-on:closemodalsetujuireservasi.window="$el.close()">
    <div class="modal-box w-full max-w-4xl">
        @if ($permintaan)
            <h3 class="text-xl font-semibold mb-1">Setujui & Simpan Reservasi</h3>
            <p class="text-sm text-base-content/60 mb-4">
                Permintaan dari <strong>{{ $permintaan->nama }}</strong> ({{ $permintaan->no_telp }})
                @if ($permintaan->pasien_baru)
                    <span class="badge badge-warning badge-sm ml-1">Pasien Baru</span>
                @else
                    <span class="badge badge-ghost badge-sm ml-1">Pasien Lama</span>
                @endif
            </p>

            @if ($permintaan->no_register)
                <div class="alert alert-info text-sm py-2 mb-4">
                    No. Register yang diisi pasien: <strong>{{ $permintaan->no_register }}</strong> — gunakan ini untuk mencari data pasien di kolom pencarian.
                </div>
            @endif

            <form wire:submit.prevent="confirm" class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Pasien (wajib dipilih manual, sesuai data yang sudah terdaftar) --}}
                <div x-data="pasienSearchApproval(@entangle('pasien_id'))" class="relative sm:col-span-2">
                    <label class="label font-semibold">Pasien</label>
                    <input
                        type="text"
                        class="input input-bordered w-full @error('pasien_id') input-error @enderror"
                        placeholder="Ketik nama atau no register pasien..."
                        x-model="query"
                        @input.debounce.300ms="searchPasien"
                        @click.away="results = []"
                        autocomplete="off"
                    >
                    <input type="hidden" x-model="selectedId" wire:model="pasien_id">

                    <ul x-show="results.length > 0" class="absolute z-50 bg-base-100 border rounded-lg mt-1 w-full max-h-48 overflow-auto shadow-lg">
                        <template x-for="pasien in results" :key="pasien.id">
                            <li class="px-3 py-2 hover:bg-base-200 cursor-pointer" @click="selectPasien(pasien)" x-text="pasien.text"></li>
                        </template>
                    </ul>

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

                {{-- Tanggal --}}
                <div>
                    <label class="label font-semibold">Tanggal Reservasi</label>
                    <input type="date" class="input input-bordered w-full" wire:model="tanggal_reservasi">
                    @error('tanggal_reservasi') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Jam --}}
                <div>
                    <label class="label font-semibold">Jam Reservasi</label>
                    <input type="time" class="input input-bordered w-full" wire:model="jam_reservasi">
                    @error('jam_reservasi') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Catatan --}}
                <div class="sm:col-span-2">
                    <label class="label font-semibold">Catatan</label>
                    <textarea class="textarea textarea-bordered w-full" wire:model="catatan" rows="3"></textarea>
                    @error('catatan') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-1 sm:col-span-2 flex justify-end gap-2 mt-4">
                    <button type="submit" class="btn btn-success btn-sm">Ya, Setujui & Simpan</button>
                    <button type="button" class="btn btn-neutral btn-sm" onclick="document.getElementById('modalsetujuireservasi').close()">Batal</button>
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

<script>
    function pasienSearchApproval(pasienEntangle) {
        return {
            query: '',
            selectedId: pasienEntangle,
            results: [],

            async searchPasien() {
                if (this.query.length < 2) {
                    this.results = [];
                    return;
                }

                try {
                    const res = await fetch(`/search/pasien?q=${encodeURIComponent(this.query)}`);
                    const data = await res.json();
                    this.results = data;
                } catch (e) {
                    console.error('Gagal fetch pasien:', e);
                    this.results = [];
                }
            },

            selectPasien(pasien) {
                this.query = pasien.text;
                this.selectedId = pasien.id;
                this.results = [];
            }
        }
    }
</script>