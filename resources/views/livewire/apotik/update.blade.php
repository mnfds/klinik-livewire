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
                            <i class="fa-regular fa-folder-open"></i> Update Transaksi
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-lg font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i> Update Transaksi
            </h1>
        </div>

        <main class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-6 gap-6 relative">

                {{-- Kolom Kanan: Form Dinamis --}}
                <div class="lg:col-span-4 space-y-6">
                    <form wire:submit.prevent="update" class="space-y-6">
                        <div class="bg-base-100 shadow rounded-box p-6">
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
                                        get hargaProduk() {
                                            let produk = {{ Js::from($produk) }}.find(p => p.id == this.produk_id);
                                            return produk ? produk.harga_dasar : 0;
                                        },
                                        hitung() {
                                            let base = this.hargaProduk * (this.jumlah_produk || 1);
                                            this.harga_asli = base;
                                            let afterPotongan = base - (this.potongan || 0);
                                            this.subtotal = afterPotongan - (afterPotongan * (this.diskon || 0)/100);
                                        },
                                        formatRupiah(val) {
                                            return (val || 0).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits:0 });
                                        }
                                    }"
                                    x-init="hitung()"
                                    @input="hitung()">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                                        <div>
                                            <label class="block text-sm font-semibold mb-1">Produk</label>
                                            <select class="select select-bordered w-full" x-model="produk_id" @change="hitung()">
                                                <option value="">-- Pilih Produk --</option>
                                                @foreach($produk as $p)
                                                    <option value="{{ $p->id }}">{{ $p->nama_dagang }}</option>
                                                @endforeach
                                            </select>
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

                                            <span :class="(row.potongan > 0 || row.diskon > 0) ? 'line-through text-gray-500' : ''"
                                                x-text="(row.harga_asli || 0).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits:0 })">
                                            </span>
                                        </div>

                                        <template x-if="row.potongan && row.potongan > 0">
                                            <div class="flex justify-between text-sm text-red-600">
                                                <span>Potongan:</span>
                                                <span x-text="Number(row.potongan || 0).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits:0 })"></span>
                                            </div>
                                        </template>

                                        <template x-if="row.diskon && row.diskon > 0">
                                            <div class="flex justify-between text-sm text-blue-600">
                                                <span>Diskon:</span>
                                                <span x-text="row.diskon + '%'"></span>
                                            </div>
                                        </template>

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
                            @can('akses', 'Transaksi Apotik Edit')
                            <button wire:click.prevent="update"
                                    class="btn btn-success w-full mt-4"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="fa-solid fa-pen-to-square"></i> Update</span>
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

<script>
    document.addEventListener("livewire:navigated", initTransaksiRupiah);
    document.addEventListener("livewire:initialized", () => {
        Livewire.hook('morph.updated', () => {
            initTransaksiRupiah();
        });
    });

    function initTransaksiRupiah() {
        document.querySelectorAll('.input-rupiah-transaksi').forEach(function(input) {
            if (!input.cleave) {
                input.cleave = new Cleave(input, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    numeralDecimalMark: ',',
                    delimiter: '.',
                });

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