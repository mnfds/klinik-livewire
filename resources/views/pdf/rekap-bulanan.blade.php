<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Perbulan</title>
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
        .text-error {
            color: red;
        }
    </style>
</head>
<body>

<h2>Laporan Pendapatan & Pengeluaran Bulanan</h2>
<h2 class="text-error">
    {{ $labelBulan }}
</h2>
    {{-- ================= DETAIL ================= --}}
    <hr>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Uang Masuk</th>
                <th>Uang Keluar</th>
                <th>Sisa</th>
            </tr>
        </thead>

        <tbody>
            @foreach($detailPerHari as $row)
                <tr>
                    <td>{{ $row['no'] }}</td>
                    <td>{{ $row['tanggal'] }}</td>
                    <td>
                        Rp {{ number_format($row['masuk'],0,',','.') }}
                    </td>
                    <td>
                        Rp {{ number_format($row['keluar'],0,',','.') }}
                    </td>
                    <td>
                        Rp {{ number_format($row['sisa'],0,',','.') }}
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
