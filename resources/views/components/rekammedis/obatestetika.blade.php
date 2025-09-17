<div class="bg-base-200 p-4 rounded border border-base-200"
    x-data="obatEstetikaForm()"
        x-init="
        $watch('produkItems', () => {
            const total = calcTotal();
            window.dispatchEvent(
                new CustomEvent('total-produk-updated', { detail: total })
            );
        })
    "

>
    @props([
        'obatEstetika' => [
            'produk_id' => [],
            'jumlah_produk' => [],
            'potongan' => [],
            'diskon' => [],
            'subtotal' => [],
        ],
        'layanandanbundling' => [
            'layanan' => [],
            'bundling' => [],
            'treatment' => [],
            'skincare' => [],
        ]
    ])

    <div class="divider">Produk Keperluan Estetika</div>

    <!-- Tombol tambah -->
    <div class="mb-6">
        <button type="button"
            class="btn btn-primary btn-sm"
            @click="addproduk">
            + Tambah Produk
        </button>
    </div>

    <!-- List Produk -->
    <div class="space-y-4">
        <template x-for="(item, index) in produkItems" :key="index">
            <div class="p-4 border rounded-lg bg-base-100 space-y-3">
                
                <!-- Baris 1: Produk + Jumlah -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Produk</label>
                        <select class="select select-bordered w-full"
                            :name="`obatEstetika[produk_id][${index}]`"
                            x-model="item.produk_id"
                            @change="syncItemProduk(index)"
                        >
                            <option value="">-- Pilih Produk --</option>
                            @foreach($layanandanbundling['skincare'] as $skincare)
                                <option value="{{ $skincare['id'] }}">
                                    {{ $skincare['nama_dagang'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Jumlah</label>
                        <input type="number" min="1"
                            class="input input-bordered w-full"
                            x-model.number="item.jumlah_produk"
                            @input="syncItemProduk(index)"
                        >
                    </div>
                </div>

                <!-- Baris 2: Harga Asli + Potongan + Diskon + Subtotal -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                    <!-- Harga Asli -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Harga Asli</label>
                        <input type="text"
                            class="input input-bordered w-full bg-base-200"
                            :value="formatCurrency(calcHargaAsli(item))"
                            readonly
                        >
                    </div>

                    <!-- Potongan Harga -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Potongan (Rp)</label>
                        <input type="text"
                            class="input input-bordered w-full"
                            :value="formatCurrency(item.potongan)"
                            @input="e => updatePotongan(index, e.target.value)"
                        >
                    </div>

                    <!-- Diskon -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Diskon</label>
                        <div class="flex items-center">
                            <input type="number" min="0" max="100"
                                class="input input-bordered w-full"
                                x-model.number="item.diskon"
                                @input="updateDiskon(index, item.diskon)"
                            >
                            <span class="ml-2">%</span>
                        </div>
                    </div>

                    <!-- Subtotal -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Subtotal</label>
                        <input type="text"
                            class="input input-bordered w-full bg-base-200"
                            :value="formatCurrency(calcSubtotal(item))"
                            readonly
                        >
                    </div>
                </div>

                <!-- Tombol Hapus -->
                <div class="flex justify-end">
                    <button type="button"
                        class="btn btn-error btn-sm"
                        @click="removeProduk(index)"
                        x-show="produkItems.length > 1">
                        Hapus
                    </button>
                </div>
            </div>
        </template>

        <!-- Footer Total -->
        <div class="flex justify-end mt-4 font-bold text-lg">
            <div class="mr-4">Total:</div>
            <div x-text="formatCurrency(calcTotal())"></div>
        </div>
    </div>
</div>

<script>
function obatEstetikaForm() {
    return {
        // state
        produkItems: [{ produk_id: '', jumlah_produk: 1, potongan: 0, diskon: 0, subtotal: 0 }],
        produk: @json($layanandanbundling['skincare']),

        // helpers
        getProduk(id) {
            return this.produk.find(t => t.id == id);
        },

        formatCurrency(value) {
            if (!value) return 'Rp 0';
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        },

        // update potongan
        updatePotongan(i, val) {
            let number = parseInt(val.replace(/[^\d]/g, '')) || 0;
            this.produkItems[i].potongan = number;
            this.syncItemProduk(i);
        },

        // update diskon
        updateDiskon(i, val) {
            let number = parseInt(val) || 0;
            if (number < 0) number = 0;
            if (number > 100) number = 100;
            this.produkItems[i].diskon = number;
            this.syncItemProduk(i);
        },

        // kalkulasi
        calcHargaAsli(item) {
            let produk = this.getProduk(item.produk_id);
            let harga = produk ? Number(produk.harga_dasar) : 0;
            let jumlah = Number(item.jumlah_produk) || 1;
            return harga * jumlah;
        },

        calcSubtotal(item) {
            let hargaAsli = this.calcHargaAsli(item);
            let potongan = Number(item.potongan) || 0;
            let diskon = Number(item.diskon) || 0;
            let afterPotongan = hargaAsli - potongan;
            if (afterPotongan < 0) afterPotongan = 0;
            return afterPotongan - (afterPotongan * (diskon / 100));
        },

        calcTotal() {
            return this.produkItems.reduce((total, item) => total + this.calcSubtotal(item), 0);
        },

        // aksi
        addproduk() {
            this.produkItems.push({ produk_id: '', jumlah_produk: 1, potongan: 0, diskon: 0, subtotal: 0 });
            this.syncProdukToLivewire();
        },

        removeProduk(index) {
            this.produkItems.splice(index, 1);
            this.reindexProduk();
            this.syncProdukToLivewire();
            this.cleanupProdukLivewire();
        },

        reindexProduk() {
            this.produkItems = this.produkItems.map(item => ({ ...item }));
        },

        syncItemProduk(i) {
            let item = this.produkItems[i];
            let subtotal = this.calcSubtotal(item);
            item.subtotal = subtotal;

            // Ganti $wire.set dengan @this.set
            @this.set(`obat_estetika.produk_id.${i}`, item.produk_id);
            @this.set(`obat_estetika.jumlah_produk.${i}`, item.jumlah_produk);
            @this.set(`obat_estetika.potongan.${i}`, item.potongan);
            @this.set(`obat_estetika.diskon.${i}`, item.diskon);
            @this.set(`obat_estetika.subtotal.${i}`, item.subtotal);
        },

        syncProdukToLivewire() {
            this.produkItems.forEach((item, i) => this.syncItemProduk(i));
        },

        cleanupProdukLivewire() {
            let length = this.produkItems.length;
            for (let i = length; i < 100; i++) {
                @this.set(`obat_estetika.produk_id.${i}`, null);
                @this.set(`obat_estetika.jumlah_produk.${i}`, null);
                @this.set(`obat_estetika.diskon.${i}`, null);
                @this.set(`obat_estetika.subtotal.${i}`, null);
            }
        }
    }
}
</script>
