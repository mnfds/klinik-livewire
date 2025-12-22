<div class="pt-1 pb-12">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Breadcrumbs -->
        <div class="hidden lg:flex justify-end px-4">
            <div class="breadcrumbs text-sm">
                <ul>
                    <li>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('apotik.kasir') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i> Apotik
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('apotik.kasir') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i> Transaksi
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-lg font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i> Proses Transaksi Apotik
            </h1>
        </div>

        {{-- MAIN --}}
        <main class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-6 gap-6 relative">

                {{-- Kolom Kanan: Form Dinamis --}}
                <div class="lg:col-span-4 space-y-6">
                    <form wire:submit.prevent="create" class="space-y-6">
                        <div class="bg-base-100 shadow rounded-box p-6"
                            x-data="{
                                items: @entangle('obat_estetika'),
                                produkList: {{ Js::from($produk) }},
                                get showPasienInput() {
                                    // cek apakah ada row yang produknya masuk golongan wajib pasien
                                    return Object.values(this.items).some(row => {
                                        let p = this.produkList.find(pr => pr.id == row.produk_id);
                                        if (!p) return false;
                                        return ['Obat Bebas Terbatas','Obat Keras','Skincare','Obat Narkotika']
                                            .includes(p.golongan);
                                    });
                                }
                            }"
                        >
                            <!-- hanya tampil kalau perlu -->
                            <template x-if="showPasienInput">
                                <div class="mb-4" x-data="searchPasien()">
                                    <label class="block text-sm font-semibold mb-1">Nama Pasien</label>

                                    <input 
                                        type="text" 
                                        placeholder="Ketik nama atau no register..."
                                        class="input input-bordered w-full"
                                        x-model="query"
                                        @input.debounce.300ms="search()"
                                        @click="open = true"
                                    >

                                    <div 
                                        x-show="open && results.length > 0" 
                                        class="border bg-white mt-1 rounded shadow max-h-60 overflow-y-auto z-50 w-full"
                                    >
                                        <template x-for="item in results" :key="item.id">
                                            <div 
                                                @click="select(item)" 
                                                class="px-3 py-2 hover:bg-blue-100 cursor-pointer"
                                                x-text="item.text">
                                            </div>
                                        </template>
                                    </div>

                                    <input type="hidden" name="pasien_id" x-model="selectedId" x-ref="pasienId">
                                </div>
                            </template>

                            <div class="divider">Pembelian</div>

                            @foreach($obat_estetika as $uuid => $item)
                                <div wire:key="row-{{ $uuid }}"
                                    x-data="{
                                        produk_id: @entangle("obat_estetika.$uuid.produk_id"),
                                        jumlah_produk: @entangle("obat_estetika.$uuid.jumlah_produk"),
                                        potongan: @entangle("obat_estetika.$uuid.potongan"),
                                        diskon: @entangle("obat_estetika.$uuid.diskon"),
                                        harga_asli: @entangle("obat_estetika.$uuid.harga_asli"),
                                        subtotal: @entangle("obat_estetika.$uuid.subtotal"),

                                        query: '',
                                        results: [],
                                        open: false,

                                        async searchProduk() {
                                            if (this.query.length < 2) {
                                                this.results = [];
                                                return;
                                            }

                                            const res = await fetch(`/search-produk-obat?q=${this.query}`);
                                            const data = await res.json();
                                            this.results = data;
                                        },

                                        selectProduk(item) {
                                            this.query = item.text;
                                            this.produk_id = item.id;
                                            this.harga_asli = item.harga; // langsung assign harga dari API
                                            this.results = [];
                                            this.open = false;
                                            this.hitung();
                                        },

                                        hitung() {
                                            let base = (this.harga_asli || 0) * (this.jumlah_produk || 1);
                                            let afterPotongan = base - (this.potongan || 0);
                                            this.subtotal = afterPotongan - (afterPotongan * (this.diskon || 0)/100);
                                        },

                                        formatRupiah(val) {
                                            return (val || 0).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits:0 });
                                        }
                                    }"
                                    x-init="hitung()"
                                    @input="hitung()"
                                >
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                                        <div x-data>
                                            <label class="block text-sm font-semibold mb-1">Produk</label>
                                            <input type="text"
                                                placeholder="Ketik nama produk..."
                                                class="input input-bordered w-full"
                                                x-model="query"
                                                @input.debounce.300ms="searchProduk()"
                                                @click="open = true"
                                            >

                                            <div x-show="open && results.length > 0"
                                                class="border bg-white mt-1 rounded shadow max-h-60 overflow-y-auto z-50 w-full">
                                                <template x-for="item in results" :key="item.id">
                                                    <div @click="selectProduk(item)"
                                                        class="px-3 py-2 hover:bg-blue-100 cursor-pointer"
                                                        x-text="item.text">
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold mb-1">Jumlah</label>
                                            <input type="number" min="1" class="input input-bordered w-full" x-model="jumlah_produk" @input="hitung()">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end mt-2">
                                        <div>
                                            <label class="block text-sm font-semibold mb-1">Harga Asli</label>
                                            <input type="text" class="input input-bordered w-full bg-base-200" :value="formatRupiah(harga_asli)" readonly>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold mb-1">Potongan (Rp)</label>
                                            <input type="text" class="input input-bordered w-full input-rupiah-transaksi" placeholder="Rp 0">
                                            <input type="hidden" class="input-rupiah-hidden-transaksi" wire:model.defer="obat_estetika.{{ $uuid }}.potongan">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold mb-1">Diskon (%)</label>
                                            <input type="number" min="0" max="100" class="input input-bordered w-full" x-model="diskon" @input="hitung()">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold mb-1">Subtotal</label>
                                            <input type="text" class="input input-bordered w-full bg-base-200" :value="formatRupiah(subtotal)" readonly>
                                        </div>
                                    </div>

                                    <div class="flex justify-end mt-2">
                                        <button type="button" class="btn btn-error btn-sm"
                                                wire:click="removeRow('{{ $uuid }}')"
                                                @if(count($obat_estetika) === 1) disabled @endif>
                                            Hapus
                                        </button>
                                    </div>

                                    <hr class="my-2">
                                </div>
                            @endforeach

                            <button type="button" class="btn btn-primary btn-sm mt-2" wire:click="addRow">
                                Tambah Produk
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Kolom Kiri: Invoice --}}
                <div class="lg:col-span-2">
                    <div class="sticky top-20 space-y-6">
                        <div class="bg-base-100 shadow rounded-box p-4">
                            <h3 class="font-semibold mb-4">Invoice</h3>

                            <div class="space-y-2" x-data="{ items: @entangle('obat_estetika') }">
                                <template x-for="row in Object.values(items)" :key="row.uuid">
                                    <div class="border-b pb-2 mb-2">
                                        <div class="flex justify-between">
                                            <span>
                                                <span x-text="(row.produk_id ? ({{ Js::from($produk) }}.find(p => p.id == row.produk_id)?.nama_dagang) : '-')"></span>
                                                (<span x-text="row.jumlah_produk"></span>x)
                                            </span>

                                            <!-- Harga asli (coret kalau ada potongan/diskon) -->
                                            <span :class="(row.potongan > 0 || row.diskon > 0) ? 'line-through text-gray-500' : ''"
                                                x-text="(row.harga_asli || 0).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits:0 })">
                                            </span>
                                        </div>

                                        <!-- tampilkan potongan jika ada -->
                                        <template x-if="row.potongan && row.potongan > 0">
                                            <div class="flex justify-between text-sm text-red-600">
                                                <span>Potongan:</span>
                                                <span x-text="Number(row.potongan || 0).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits:0 })"></span>
                                            </div>
                                        </template>

                                        <!-- tampilkan diskon jika ada -->
                                        <template x-if="row.diskon && row.diskon > 0">
                                            <div class="flex justify-between text-sm text-blue-600">
                                                <span>Diskon:</span>
                                                <span x-text="row.diskon + '%'"></span>
                                            </div>
                                        </template>

                                        <!-- subtotal final -->
                                        <div class="flex justify-between font-semibold text-green-600">
                                            <span>Subtotal:</span>
                                            <span x-text="(row.subtotal || 0).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits:0 })"></span>
                                        </div>
                                    </div>
                                </template>

                                <div class="flex justify-between font-bold my-4 border-t pt-2">
                                    <span>Total:</span>
                                    <span x-text="Object.values(items).reduce((acc, cur) => acc + (Number(cur.subtotal) || 0), 0).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits:0 })"></span>
                                </div>
                            </div>
                            @can('akses', 'Transaksi Apotik Tambah')
                            <button wire:click.prevent="create"
                                class="btn btn-success w-full mt-4"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="fa-solid fa-plus"></i> Simpan</span>
                                <span wire:loading.inline>Loading...</span>
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>

            </div>
        </main>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("livewire:navigated", initTransaksiRupiah);
    document.addEventListener("livewire:initialized", () => {
        Livewire.hook('morph.updated', () => {
            initTransaksiRupiah();
        });
    });

    function initTransaksiRupiah() {
        document.querySelectorAll('.input-rupiah-transaksi').forEach(function(input) {
            if (!input.cleave) { // biar tidak double init
                input.cleave = new Cleave(input, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    numeralDecimalMark: ',',
                    delimiter: '.',
                });

                // sinkron ke hidden input wire:model
                input.addEventListener('input', function() {
                    let rawValue = input.cleave.getRawValue();
                    let hidden = input.closest('div').querySelector('.input-rupiah-hidden-transaksi');
                    if (hidden) hidden.value = rawValue;
                    hidden.dispatchEvent(new Event('input'));
                });
            }
        });
    }
</script>
{{-- SEARCH PASIEN --}}
<script>
    function searchPasien() {
        return {
            query: '',
            results: [],
            open: false,
            selectedId: null,

            async search() {
                if (this.query.length < 2) {
                    this.results = [];
                    return;
                }
                const res = await fetch(`/search/pasien?q=${this.query}`);
                this.results = await res.json();
            },

            select(item) {
                this.query = item.text;
                this.selectedId = item.id;
                this.results = [];
                this.open = false;
                // update ke Livewire
                @this.set('pasien_id', this.selectedId);
            }
        }
    }
</script>
@endpush

