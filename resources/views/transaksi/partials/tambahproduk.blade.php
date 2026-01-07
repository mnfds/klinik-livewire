<div class="pt-1 pb-12">
    {{-- Kolom Kanan: Form Dinamis --}}
    <div class="lg:col-span-4 space-y-6">
        <div class="divider">Produk/Obat Tambahan</div>
        @foreach($produktambahan as $uuid => $item)
            <div wire:key="row-{{ $uuid }}"
                x-data="{
                    produk_id: @entangle("produktambahan.$uuid.produk_id"),
                    jumlah_produk: @entangle("produktambahan.$uuid.jumlah_produk"),
                    potongan_harga: @entangle("produktambahan.$uuid.potongan_harga"),
                    diskon: @entangle("produktambahan.$uuid.diskon"),
                    harga_satuan: @entangle("produktambahan.$uuid.harga_satuan"),
                    subtotal: @entangle("produktambahan.$uuid.subtotal"),
    
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
                        this.harga_satuan = item.harga; // langsung assign harga dari API
                        this.results = [];
                        this.open = false;
                        this.hitung();
                    },
    
                    hitung() {
                        const qty = Number(this.jumlah_produk) || 1;
                        const harga = Number(this.harga_satuan) || 0;
                        const diskon = Number(this.diskon) || 0;
                        const potongan = Number(this.potongan_harga) || 0;
    
                        let base = harga * qty;
                        let afterDiskon = base - (base * diskon / 100);
                        let subtotal = afterDiskon - potongan;
    
                        this.subtotal = subtotal > 0 ? subtotal : 0;
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
                        <input type="text" class="input input-bordered w-full bg-base-200" :value="formatRupiah(harga_satuan)" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Diskon (%)</label>
                        <input type="number" min="0" max="100" class="input input-bordered w-full" x-model="diskon" @input="hitung()">
                    </div>
                    <div x-data x-init="
                                const cleave = new Cleave($refs.input, {
                                    numeral: true,
                                    numeralThousandsGroupStyle: 'thousand',
                                    delimiter: '.',
                                    numeralDecimalMark: ',',
                                });
    
                                $refs.input.addEventListener('input', () => {
                                    potongan_harga = Number(cleave.getRawValue()) || 0;
                                    hitung();
                                });
                            "
                        >
                        <label class="block text-sm font-semibold mb-1">Potongan (Rp)</label>
    
                        <input
                            x-ref="input"
                            type="text"
                            class="input input-bordered w-full"
                            placeholder="Rp 0"
                        >
    
                        <!-- sinkron ke Livewire -->
                        <input
                            type="hidden"
                            x-model="potongan_harga"
                            wire:model.defer="produktambahan.{{ $uuid }}.potongan_harga"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Subtotal</label>
                        <input type="text" class="input input-bordered w-full bg-base-200" :value="formatRupiah(subtotal)" readonly>
                    </div>
                </div>
    
                <div class="flex justify-end mt-2">
                    <button type="button" class="btn btn-error btn-sm"
                            wire:click="removeRow('{{ $uuid }}')"
                            @if(count($produktambahan) === 1) disabled @endif>
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
</div>