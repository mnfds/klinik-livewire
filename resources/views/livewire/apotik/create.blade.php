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

                        <div class="bg-base-100 shadow rounded-box p-6">
                            <div class="divider">Pembelian</div>
                                @foreach($obat_estetika as $uuid => $item)
                                    <div wire:key="row-{{ $uuid }}"
                                        x-data="{
                                            produk_id: @entangle('obat_estetika.' . $uuid . '.produk_id').defer,
                                            jumlah_produk: @entangle('obat_estetika.' . $uuid . '.jumlah_produk').defer,
                                            potongan: @entangle('obat_estetika.' . $uuid . '.potongan').defer,
                                            diskon: @entangle('obat_estetika.' . $uuid . '.diskon').defer,
                                            harga_asli: @entangle('obat_estetika.' . $uuid . '.harga_asli').defer,
                                            subtotal: @entangle('obat_estetika.' . $uuid . '.subtotal').defer,
                                            get hargaProduk() {
                                                let produk = {{ Js::from($produk) }}.find(p => p.id == this.produk_id);
                                                return produk ? produk.harga_dasar : 0;
                                            },
                                            hitung() {
                                                let base = this.hargaProduk * (this.jumlah_produk || 1);
                                                this.harga_asli = base;
                                                let afterPotongan = base - (this.potongan || 0);
                                                this.subtotal = afterPotongan - (afterPotongan * (this.diskon || 0) / 100);
                                                @this.set('obat_estetika.{{ $uuid }}.harga_asli', this.harga_asli);
                                                @this.set('obat_estetika.{{ $uuid }}.subtotal', this.subtotal);
                                            },
                                            formatRupiah(val) {
                                                return (val || 0).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
                                            }
                                        }"
                                        x-init="hitung()"
                                        @input="hitung()"
                                    >
                                        <!-- Baris 1: Produk + Jumlah -->
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
                                                <input type="number" min="1" class="input input-bordered w-full"
                                                    x-model="jumlah_produk" @input="hitung()">
                                            </div>
                                        </div>

                                        <!-- Baris 2: Harga Asli + Potongan + Diskon + Subtotal -->
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                                            <div>
                                                <label class="block text-sm font-semibold mb-1">Harga Asli</label>
                                                <input type="text" class="input input-bordered w-full bg-base-200"
                                                    :value="formatRupiah(harga_asli)" readonly>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold mb-1">Potongan (Rp)</label>
                                                <input type="number" class="input input-bordered w-full"
                                                    x-model="potongan" @input="hitung()">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold mb-1">Diskon</label>
                                                <div class="flex items-center">
                                                    <input type="number" min="0" max="100" class="input input-bordered w-full"
                                                        x-model="diskon" @input="hitung()">
                                                    <span class="ml-2">%</span>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold mb-1">Subtotal</label>
                                                <input type="text" class="input input-bordered w-full bg-base-200"
                                                    :value="formatRupiah(subtotal)" readonly>
                                            </div>
                                        </div>

                                        <!-- Tombol Hapus -->
                                        <div class="flex justify-end">
                                            <button type="button" class="btn btn-error btn-sm"
                                                    wire:click="removeRow('{{ $uuid }}')"
                                                    @if(count($obat_estetika) === 1) disabled @endif>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            <!-- Tombol Tambah Row -->
                            <div class="mt-4">
                                <button type="button" class="btn btn-primary btn-sm" wire:click="addRow">
                                    Tambah Produk
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Kolom Kiri: Invoice --}}
                <div class="lg:col-span-2">
                    <div class="sticky top-20 space-y-6">
                        <div class="bg-base-100 shadow rounded-box p-4">
                            <h3 class="font-semibold mb-4">Invoice</h3>

                            <div class="space-y-2">
                                @foreach($obat_estetika as $item)
                                    <div class="flex justify-between">
                                        <span>{{ $item['produk_id'] ? $produk->find($item['produk_id'])->nama_dagang : '-' }}</span>
                                        <span>
                                            Rp {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex justify-between font-bold my-4">
                                <span>Total:</span>
                                <span>
                                    Rp {{ number_format(collect($obat_estetika)->sum('subtotal'), 0, ',', '.') }}
                                </span>
                            </div>

                            <button wire:click.prevent="create"
                                class="btn btn-success w-full mt-4"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="fa-solid fa-plus"></i> Simpan</span>
                                <span wire:loading.inline>Loading...</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </main>

    </div>
</div>