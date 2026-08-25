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
                            <i class="fa-regular fa-folder-open"></i> Riwayat Transaksi
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-lg font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i> Riwayat Transaksi
            </h1>
        </div>

        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-7 gap-6">
                
                {{-- Kolom Kiri: Detail Produk --}}
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-base-100 shadow rounded-box p-4">
                        <div class="border-b pb-4 mb-4">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">
                                Detail Transaksi
                            </h3>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><span class="font-semibold">Pasien:</span> {{ $transaksi->pasien->nama ?? '-'}}</p>
                                <p><span class="font-semibold">Tanggal:</span> 
                                    {{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d M Y H:i') }}
                                </p>
                                <p><span class="font-semibold">No. Transaksi:</span> {{ $transaksi->no_transaksi }}</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            @foreach($transaksi->riwayat as $item)
                                <div class="border-b pb-2">
                                    <div class="flex justify-between items-center">
                                        <span>{{ $item->produk->nama_dagang }} ({{ number_format($item->produk->harga_dasar, 0, ',', '.')}} x {{ $item->jumlah_produk }} {{ $item->produk->sediaan }})</span>
                                        @php
                                            $harga_produk = $item->produk->harga_dasar;
                                            $jumlah_dibeli = $item->jumlah_produk;
                                            $harga_total = $harga_produk * $jumlah_dibeli;
                                        @endphp
                                        @if($item->potongan || $item->diskon)
                                            <span class="line-through text-gray-500">
                                                Rp {{ number_format($harga_total, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="font-semibold">
                                                Rp {{ number_format($harga_total, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($item->potongan || $item->diskon)
                                        <div class="ml-4 text-sm text-gray-600 space-y-1 text-right">
                                            @if($item->diskon)
                                                <div class="text-error">- {{ $item->diskon }}%</div>
                                            @endif
                                            @if($item->potongan)
                                                <div class="text-error">- Rp {{ number_format($item->potongan, 0, ',', '.') }}</div>
                                            @endif
                                            <div class="font-semibold text-success">
                                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            @foreach($transaksi->riwayatBarang as $item)
                                <div class="border-b pb-2">
                                    <div class="flex justify-between items-center">
                                        <span>{{ $item->barang->nama }} ({{ number_format($item->barang->harga_dasar, 0, ',', '.')}} x {{ $item->jumlah_barang }} {{ $item->barang->satuan }})</span>
                                        @php
                                            $harga_produk = $item->barang->harga_dasar;
                                            $jumlah_dibeli = $item->jumlah_barang;
                                            $harga_total = $harga_produk * $jumlah_dibeli;
                                        @endphp
                                        @if($item->potongan || $item->diskon)
                                            <span class="line-through text-gray-500">
                                                Rp {{ number_format($harga_total, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="font-semibold">
                                                Rp {{ number_format($item->barang->harga_dasar, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($item->potongan || $item->diskon)
                                        <div class="ml-4 text-sm text-gray-600 space-y-1 text-right">
                                            @if($item->diskon)
                                                <div class="text-error">- {{ $item->diskon }}%</div>
                                            @endif
                                            @if($item->potongan)
                                                <div class="text-error">- Rp {{ number_format($item->potongan, 0, ',', '.') }}</div>
                                            @endif
                                            <div class="font-semibold text-success">
                                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Invoice --}}
                <div class="lg:col-span-3">
                    <div class="sticky top-20 space-y-6">
                        <div class="bg-base-100 shadow rounded-box p-4">
                            <h3 class="font-semibold mb-4">Invoice</h3>

                            <div class="space-y-2">
                                @foreach($transaksi->riwayat as $item)
                                    <div class="flex justify-between">
                                        <span>{{ $item->produk->nama_dagang }} ({{ $item->jumlah_produk }} {{ $item->produk->sediaan }})</span>
                                        <span class="text-success">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                                @foreach($transaksi->riwayatBarang as $item)
                                    <div class="flex justify-between">
                                        <span>{{ $item->barang->nama }}</span>
                                        <span class="text-success">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach

                                <div class="flex justify-between font-semibold text-sm border-t mt-2">
                                    <span>Subtotal:</span>
                                    <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                                </div>
                                {{-- DISKON --}}
                                @if($transaksi->diskon > 0)
                                    <div class="flex justify-between text-error text-sm">
                                    <span>Diskon</span>
                                        <span>
                                            - {{ number_format($transaksi->diskon, 0, ',', '.') }}%
                                        </span>
                                    </div>
                                @endif
                                {{-- POTONGAN --}}
                                @if($transaksi->potongan > 0)
                                    <div class="flex justify-between text-error text-sm">
                                    <span>Potongan</span>
                                        <span>
                                            - Rp {{ number_format($transaksi->potongan, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif
                                {{-- TOTAL BERSIH --}}
                                <div class="flex justify-between font-bold text-base border-t mt-2">
                                    <span>Total Bayar</span>
                                    <span>
                                        Rp {{ number_format($transaksi->total_tagihan_bersih, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-end font-bold text-base">
                                    <span>
                                        {{ $transaksi->metode_pembayaran }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>