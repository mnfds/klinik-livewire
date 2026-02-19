<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Harian</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }
        th {
            background: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .small {
            font-size: 11px;
        }
    </style>
</head>
<body>

<h3>Laporan Pendapatan & Pengeluaran Harian</h3>
<h5>
    {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
</h5>
<hr>

{{-- ================= PENDAPATAN KLINIK ================= --}}
<h4>Pendapatan Klinik</h4>

<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>No. Transaksi</th>
            <th>Keterangan</th>
            <th>Jumlah Uang</th>
        </tr>
    </thead>
    <tbody>

    @foreach($klinik as $trx)
        <tr>
            <td>{{ $trx->rekammedis->pasienTerdaftar->pasien->nama ?? '-' }}</td>
            <td>{{ $trx->no_transaksi }}</td>

            {{-- KETERANGAN = DETAIL ITEM --}}
            <td class="small">
                @php
                    $grouped = $trx->riwayatTransaksi->groupBy('jenis_item');
                    $labels = [
                        'produk' => 'Produk',
                        'pelayanan' => 'Pelayanan',
                        'treatment' => 'Treatment',
                        'bundling' => 'Bundling',
                        'obat_non_racik' => 'Obat Non Racik',
                        'obat_racik' => 'Obat Racik',
                        'produk_tambahan' => 'Produk Tambahan',
                    ];
                @endphp
                @foreach($grouped as $jenis => $items)
                    <div style="margin-top:8px; font-weight:bold;">
                        {{ $labels[$jenis] ?? ucfirst($jenis) }}
                    </div>
                    @foreach ($items as $detail)
                        @php
                            $qty          = $detail->qty;
                            $harga        = $detail->harga_jual;
                            $total_kotor  = $qty * $harga;
                            $diskon       = $detail->diskon ?? 0;
                            $potongan     = $detail->potongan ?? 0;
                            $total_bersih = $detail->subtotal ?? $total_kotor;
                        @endphp
                        <div style="margin-left:8px; margin-bottom:6px;">
                            <strong>{{ $detail->nama_item }}</strong><br>
                            {{ $qty }} x Rp {{ number_format($harga,0,',','.') }}
                            = Rp {{ number_format($total_kotor,0,',','.') }}<br>
                            @if($diskon > 0)
                                (-) Diskon: {{ number_format($diskon,0,',','.') }}%<br>
                            @endif
                            @if($potongan > 0)
                                (-) Potongan: Rp {{ number_format($potongan,0,',','.') }}<br>
                            @endif
                            @if($diskon > 0 || $potongan > 0)
                                <strong>Total: Rp {{ number_format($total_bersih,0,',','.') }}</strong>
                            @endif
                        </div>
                    @endforeach
                @endforeach
                <div style="margin-top:8px; font-weight:bold;">
                    Item Tambahan
                </div>
                @foreach ($trx->riwayatTransaksi()->where('jenis_item', 'produk_tambahan')->get() as $produk)
                <div style="margin-left:8px; margin-bottom:6px;">
                    <strong>{{ $produk->nama_item }}</strong><br>
                    {{ $produk->qty }} x Rp {{ number_format($produk->harga,0,',','.') }}
                    @php
                        $total_kotor = $produk->qty * $produk->harga;
                    @endphp
                    = Rp {{ number_format($total_kotor,0,',','.') }}<br>
                    @if($produk->diskon > 0)
                        (-) Diskon: {{ number_format($produk->diskon,0,',','.') }}%<br>
                    @endif
                    @if($produk->potongan > 0)
                        (-) Potongan: Rp {{ number_format($produk->potongan,0,',','.') }}<br>
                    @endif
                    @if($produk->diskon > 0 || $produk->potongan > 0)
                        <strong>Total: Rp {{ number_format($produk->subtotal,0,',','.') }}</strong>
                    @endif
                </div>
                @endforeach
            </td>

            <td class="text-right">
                Rp {{ number_format($trx->total_tagihan,0,',','.') }}<br>
                (-) {{ $trx->diskon }}%<br>
                (-)Rp {{ number_format($trx->potongan,0,',','.') }}<br>
                Rp {{ number_format($trx->total_tagihan_bersih,0,',','.') }}<br>
            </td>
        </tr>
    @endforeach

    </tbody>
</table>


{{-- ================= PENDAPATAN APOTIK ================= --}}
<h4>Pendapatan Apotik</h4>

<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>No. Transaksi</th>
            <th>Keterangan</th>
            <th>Jumlah Uang</th>
        </tr>
    </thead>
    <tbody>
    @foreach($apotik as $trx)
        <tr>
            <td>{{ $trx->pasien->nama ?? '-' }}</td>
            <td>{{ $trx->no_transaksi }}</td>
            <td class="small">
                @foreach($trx->riwayat as $item)
                    @php
                        $produk      = $item->produk;
                        $nama        = $produk->nama_dagang ?? $item->nama_item ?? 'Item tidak ditemukan';
                        $harga       = $produk->harga_dasar ?? 0;
                        $qty         = $item->jumlah_produk ?? 1;
                        $sediaan     = $produk->sediaan ?? '';
                        $subtotal    = $harga * $qty;
                        $diskon      = $item->diskon ?? 0;
                        $potongan    = $item->potongan ?? 0;
                        $total       = $item->subtotal;
                    @endphp

                    <div style="margin-bottom:6px;">
                        <strong>{{ $nama }}</strong><br>
                        {{ $qty }} {{ $sediaan }} x Rp {{ number_format($harga,0,',','.') }}
                        = Rp {{ number_format($subtotal,0,',','.') }}<br>
                        @if($diskon > 0)
                            (-) Diskon: {{ number_format($diskon,0,',','.') }}%<br>
                        @endif
                        @if($potongan > 0)
                            (-) Potongan: Rp {{ number_format($potongan,0,',','.') }}<br>
                        @endif
                        @if($diskon > 0 || $potongan > 0)
                            <strong>Total: Rp {{ number_format($total,0,',','.') }}</strong>
                        @endif
                    </div>
                @endforeach
            </td>
            <td class="text-right">
                Rp {{ number_format($trx->total_harga,0,',','.') }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{{-- ================= PENDAPATAN LAINNYA ================= --}}
<h4>Pendapatan Lainnya</h4>
<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>No. Transaksi</th>
            <th>Keterangan</th>
            <th>Jumlah Uang</th>
        </tr>
    </thead>
    <tbody>

    @foreach($lainnya as $item)
        <tr>
            <td>{{ $item->unit_usaha }}</td>
            <td>{{ $item->no_transaksi }}</td>
            <td>
                {{ $item->keterangan }}
                <br>
                Status: {{ ucfirst($item->status) }}
            </td>
            <td class="text-right">
                Rp {{ number_format($item->total_tagihan,0,',','.') }}
            </td>
        </tr>
    @endforeach

    </tbody>
</table>
{{-- ================= PENGELUARAN ================= --}}
<h4>Pengeluaran</h4>
<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Keterangan</th>
            <th>Jumlah Uang</th>
        </tr>
    </thead>
    <tbody>

    @foreach($keluar as $item)
        <tr>
            <td>{{ $item->diajukan_oleh }}</td>
            <td>{{ $item->jenis_pengeluaran }} ({{ $item->unit_usaha }})</td>
            <td>{{ $item->keterangan ?? '-' }}</td>
            <td class="text-right">
                Rp {{ number_format($item->jumlah_uang,0,',','.') }}
            </td>
        </tr>
    @endforeach

    </tbody>
</table>
{{-- ================= TOTAL ================= --}}
<hr>
<table>
    <tr>
        <td><strong>Total Masuk</strong></td>
        <td class="text-right">
            <strong>Rp {{ number_format($totalMasuk,0,',','.') }}</strong>
        </td>
    </tr>
    <tr>
        <td><strong>Total Keluar</strong></td>
        <td class="text-right">
            <strong>Rp {{ number_format($totalKeluar,0,',','.') }}</strong>
        </td>
    </tr>
    <tr>
        <td><strong>Sisa</strong></td>
        <td class="text-right">
            <strong>Rp {{ number_format($sisa,0,',','.') }}</strong>
        </td>
    </tr>
</table>
</body>
</html>
