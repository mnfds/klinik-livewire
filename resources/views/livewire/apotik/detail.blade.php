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
            <div class="grid grid-cols-1 lg:grid-cols-6 gap-6">
                
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
                                        <span>{{ $item->produk->nama_dagang }} ({{ $item->jumlah_produk }}x)</span>
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
                                                Rp {{ number_format($item->produk->harga_dasar, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($item->potongan || $item->diskon)
                                        <div class="ml-4 text-sm text-gray-600 space-y-1">
                                            @if($item->potongan)
                                                <div>Potongan: Rp {{ number_format($item->potongan, 0, ',', '.') }}</div>
                                            @endif
                                            @if($item->diskon)
                                                <div>Diskon: {{ $item->diskon }}%</div>
                                            @endif
                                            <div class="font-semibold text-base-content">
                                                Subtotal: Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Invoice --}}
                <div class="lg:col-span-2">
                    <div class="sticky top-20 space-y-6">
                        <div class="bg-base-100 shadow rounded-box p-4">
                            <h3 class="font-semibold mb-4">Invoice</h3>

                            <div class="space-y-2">
                                @foreach($transaksi->riwayat as $item)
                                    <div class="flex justify-between">
                                        <span>{{ $item->produk->nama_dagang }}</span>
                                        <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach

                                <div class="flex justify-between font-bold my-4">
                                    <span>Total:</span>
                                    <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>