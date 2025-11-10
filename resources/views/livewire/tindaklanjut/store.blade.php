<dialog id="storeModalTindakLanjut" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestoreModalTindakLanjut', () => {
        document.getElementById('storeModalTindakLanjut')?.close()
    })">
    <div class="modal-box w-full max-w-4xl">
        <h3 class="text-xl font-semibold mb-4">Tambah Layanan pada pasien</h3>
        <form wire:submit.prevent="store" class="space-y-4">
            <!-- Input Pasien -->
            <div class="form-control relative" x-data="pasienSearch()" @click.outside="open = false">
                <label class="label">
                    <span class="label-text font-medium">Pasien</span>
                </label>

                <input
                    type="text"
                    class="input input-bordered w-full"
                    placeholder="Ketik nama atau nomor register pasien..."
                    x-model="query"
                    @input.debounce.300ms="search()"
                    @focus="open = true"
                    required
                >

                <!-- Dropdown hasil pencarian -->
                <ul
                    class="absolute z-50 w-full bg-base-100 border rounded-md mt-1 shadow-md max-h-60 overflow-auto"
                    x-show="open && results.length"
                    x-transition
                    >
                    <template x-for="item in results" :key="item.id">
                        <li
                            class="px-3 py-2 hover:bg-base-200 cursor-pointer"
                            @click="select(item)"
                            x-text="`${item.text} (${item.no_register})`"
                        ></li>
                    </template>
                </ul>
            </div>

            <!-- Input Bundling -->
            <div class="form-control relative" x-data="bundlingSearch()" @click.outside="open = false">
                <label class="label">
                    <span class="label-text font-medium">Bundling</span>
                </label>

                <input
                    type="text" class="input input-bordered w-full" placeholder="Ketik nama bundling..."
                    x-model="query"
                    @input.debounce.300ms="search()"
                    @focus="open = true"
                    required
                >

                <!-- Dropdown hasil pencarian -->
                <ul
                    class="absolute z-50 w-full bg-base-100 border rounded-md mt-1 shadow-md max-h-60 overflow-auto"
                    x-show="open && results.length"
                    x-transition
                >
                    <template x-for="item in results" :key="item.id">
                        <li
                            class="px-3 py-2 hover:bg-base-200 cursor-pointer"
                            @click="select(item)"
                        >
                            <div class="flex justify-between">
                                <span x-text="item.text"></span>
                                <span class="text-sm text-gray-500" x-text="'Rp ' + Number(item.harga).toLocaleString()"></span>
                            </div>
                        </li>
                    </template>
                </ul>

                <!-- Detail Bundling -->
                <div class="mt-4" wire:loading.remove wire:target="bundling_id, jumlah_bundling">
                    @if(!empty($bundlingDetails))
                        <div class="p-3 border rounded-lg bg-base-200">
                            <h3 class="font-semibold mb-2">{{ $bundlingDetails['nama'] }}</h3>

                            {{-- Pelayanan --}}
                            @if(!empty($bundlingDetails['pelayanans']))
                                <p class="text-sm font-medium mt-2">Pelayanan</p>
                                <ul class="list-disc list-inside text-sm">
                                    @foreach($bundlingDetails['pelayanans'] as $index => $p)
                                        <li class="flex justify-between items-center mb-1">
                                            <div>
                                                {{ $p['nama'] }}
                                                <span class="text-gray-500">
                                                    ×{{ $p['jumlah'] * ($jumlah_bundling ?? 1) }}
                                                </span>
                                            </div>
                                            <input 
                                                type="number" 
                                                min="0" 
                                                wire:model.lazy="bundlingDetails.pelayanans.{{ $index }}.terpakai" 
                                                class="input input-xs input-bordered w-20 text-right"
                                                placeholder="Terpakai">
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            {{-- Produk Obat --}}
                            @if(!empty($bundlingDetails['produk_obats']))
                                <p class="text-sm font-medium mt-2">Produk</p>
                                <ul class="list-disc list-inside text-sm">
                                    @foreach($bundlingDetails['produk_obats'] as $index => $p)
                                        <li class="flex justify-between items-center mb-1">
                                            <div>
                                                {{ $p['nama'] }}
                                                <span class="text-gray-500">
                                                    ×{{ $p['jumlah'] * ($jumlah_bundling ?? 1) }}
                                                </span>
                                            </div>
                                            <input 
                                                type="number" 
                                                min="0" 
                                                wire:model.lazy="bundlingDetails.produk_obats.{{ $index }}.terpakai" 
                                                class="input input-xs input-bordered w-20 text-right"
                                                placeholder="Terpakai">
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            {{-- Treatment --}}
                            @if(!empty($bundlingDetails['treatments']))
                                <p class="text-sm font-medium mt-2">Treatment</p>
                                <ul class="list-disc list-inside text-sm">
                                    @foreach($bundlingDetails['treatments'] as $index => $t)
                                        <li class="flex justify-between items-center mb-1">
                                            <div>
                                                {{ $t['nama'] }}
                                                <span class="text-gray-500">
                                                    ×{{ $t['jumlah'] * ($jumlah_bundling ?? 1) }}
                                                </span>
                                            </div>
                                            <input 
                                                type="number" 
                                                min="0" 
                                                wire:model.lazy="bundlingDetails.treatments.{{ $index }}.terpakai" 
                                                class="input input-xs input-bordered w-20 text-right"
                                                placeholder="Terpakai">
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Input Jumlah Bundling -->
                <div class="form-control mt-2">
                    <label class="label">
                        <span class="label-text font-medium">Jumlah Bundling</span>
                    </label>
                    <input
                        type="number"
                        class="input input-bordered w-full"
                        min="1"
                        wire:model.live="jumlah_bundling"
                    >
                </div>

                <!-- Total Harga -->
                {{-- @if(!empty($bundlingDetails))
                    <div class="mt-2 text-right text-sm">
                        <span class="font-medium">Total Harga:</span>
                        <span class="text-primary font-semibold">
                            Rp {{ number_format(($bundlingDetails['harga'] ?? 0) * ($jumlah_bundling ?? 1), 0, ',', '.') }}
                        </span>
                    </div>
                @endif --}}

                <!-- Loader kecil saat loading -->
                <div wire:loading wire:target="bundling_id" class="mt-2 text-sm text-gray-500 italic">
                    Memuat detail bundling...
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-2 pt-4 border-t mt-4">
                <button type="submit" class="btn btn-primary">
                    Simpan
                </button>
                <button type="button" class="btn btn-neutral" onclick="document.getElementById('storeModalTindakLanjut').close()">
                    Batal
                </button>
            </div>
        </form>
    </div>
</dialog>
<script>
    function pasienSearch() {
        return {
            query: '',
            results: [],
            open: false,

            async search() {
                if (this.query.length < 2) {
                    this.results = [];
                    return;
                }

                try {
                    const res = await fetch(`{{ route('api.pasien.search') }}?q=${this.query}`);
                    const data = await res.json();
                    this.results = data;
                    this.open = true;
                } catch (err) {
                    console.error('Gagal memuat data pasien:', err);
                }
            },

            select(item) {
                this.query = `${item.text} (${item.no_register})`;
                this.results = [];
                this.open = false;

                // Simpan ID pasien ke Livewire
                @this.set('pasien_id', item.id);
            }
        }
    }
</script>
<script>
    function bundlingSearch() {
        return {
            query: '',
            results: [],
            open: false,

            async search() {
                if (this.query.length < 2) {
                    this.results = [];
                    return;
                }

                try {
                    const res = await fetch(`{{ url('/ajax/bundling') }}?q=${encodeURIComponent(this.query)}`);
                    const data = await res.json();
                    this.results = data;
                    this.open = true;
                } catch (err) {
                    console.error('Gagal memuat data bundling:', err);
                }
            },

            select(item) {
                this.query = `${item.text}`;
                this.results = [];
                this.open = false;

                // Simpan ID bundling ke Livewire
                @this.set('bundling_id', item.id);

                // Kalau kamu mau kirim harga, potongan, diskon juga:
                // @this.set('harga_asli', item.harga);
                // @this.set('potongan', item.potongan);
                // @this.set('diskon', item.diskon);
            }
        }
    }
</script>