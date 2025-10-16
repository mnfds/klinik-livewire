<dialog id="storeModalReservasi" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeStoreModalReservasi', () => {
        document.getElementById('storeModalReservasi')?.close()
    })
">
    <div class="modal-box w-full max-w-4xl">
        <h3 class="text-xl font-semibold mb-4">Tambah Pasien Reservasi</h3>

        <form wire:submit.prevent="store" class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- Pasien --}}
            <div x-data="pasienSearch(@entangle('pasien_id'))" class="relative">
                <label class="label font-semibold">Pasien</label>

                {{-- Input pencarian --}}
                <input 
                    type="text" 
                    class="input input-bordered w-full" 
                    placeholder="Ketik nama atau no register pasien..."
                    x-model="query"
                    @input.debounce.300ms="searchPasien"
                    @click.away="results = []"
                    autocomplete="off"
                >

                {{-- Hidden input untuk Livewire --}}
                <input type="hidden" x-model="selectedId" wire:model="pasien_id">

                <!-- Dropdown hasil pencarian -->
                <ul 
                    x-show="results.length > 0" 
                    class="absolute z-50 bg-base-100 border rounded-lg mt-1 w-full max-h-48 overflow-auto shadow-lg"
                >
                    <template x-for="pasien in results" :key="pasien.id">
                        <li 
                            class="px-3 py-2 hover:bg-base-200 cursor-pointer"
                            @click="selectPasien(pasien)"
                            x-text="pasien.text"
                        ></li>
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

            {{-- Tombol Aksi --}}
            <div class="col-span-1 sm:col-span-2 flex justify-end gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-neutral" onclick="document.getElementById('storeModalReservasi').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>
<script>
    function pasienSearch(pasienEntangle) {
        return {
            query: '',
            selectedId: pasienEntangle, // ini langsung sinkron ke Livewire
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
                this.selectedId = pasien.id; // langsung update ke Livewire
                this.results = [];
            }
        }
    }
</script>

