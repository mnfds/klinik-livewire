<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Sakit</title>
    <style>
        @page {
            margin: 160px 60px 80px 60px;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 12px;
            color: #1a1a1a;
            line-height: 1.6;
        }
        #header {
            position: fixed;
            top: -140px;
            left: 0px;
            right: 0px;
            height: 126px;
        }
        #header img {
            width: 100%;
            height: 120px;
            display: block;
            margin: 0 auto;
        }
        #header .garis-tebal {
            width: 100%;
            height: 3px;
            background-color: black;
            margin-bottom: 2px;
        }
        #header .garis-tipis {
            width: 100%;
            height: 1px;
            background-color: black;
        }
        .title {
            text-align: center;
            margin-bottom: 20px;
        }
        .title h2 {
            font-size: 14px;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }
        .title p {
            font-size: 11px;
            margin: 2px 0 0 0;
        }
        .content {
            text-align: justify;
            margin-bottom: 20px;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 20px 0;
        }
        table.data td {
            padding: 3px 0;
            vertical-align: top;
        }
        table.data td.label {
            width: 160px;
        }
        table.data td.colon {
            width: 15px;
        }
        table.vital {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 20px 0;
        }
        table.vital th, table.vital td {
            border: 1px solid #999;
            padding: 6px 8px;
            font-size: 11px;
            text-align: left;
        }
        table.vital th {
            background-color: #f2f2f2;
        }
        .footer-ttd {
            margin-top: 40px;
            width: 100%;
        }
        .footer-ttd table {
            width: 100%;
        }
        .footer-ttd td {
            width: 50%;
            vertical-align: top;
            text-align: center;
        }
        .ttd-space {
            height: 70px;
        }
        .ttd-name {
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div id="header">
        <img src="{{ public_path('/assets/aset/kop_surat.jpg') }}">
        <div class="garis-tebal"></div>
        <div class="garis-tipis"></div>
    </div>

    <div class="title">
        <h2>Surat Keterangan Sakit</h2>
        <p>Nomor: {{ $surat->no_surat ?? '-' }}</p>
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini, dokter yang bertugas di Klinik Dokter L, menerangkan bahwa:</p>

        <table class="data">
            <tr>
                <td class="label">Nama</td>
                <td class="colon">:</td>
                <td>{{ $pasien->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Umur</td>
                <td class="colon">:</td>
                <td>{{ $pasien->tanggal_lahir ? \Carbon\Carbon::parse($pasien->tanggal_lahir)->age . ' tahun' : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td class="colon">:</td>
                <td>{{ $pasien->alamat ?? '-' }}</td>
            </tr>
        </table>

        <p>
            Berdasarkan hasil pemeriksaan pada tanggal
            <strong>{{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y') }}</strong>,
            pasien tersebut dinyatakan dalam kondisi <strong>{{ $surat->sakit }}</strong> dan memerlukan istirahat selama
            <strong>
                @php
                    $lamaIstirahat = ($surat->mulai_berlaku && $surat->selesai_berlaku)
                        ? \Carbon\Carbon::parse($surat->mulai_berlaku)->diffInDays(\Carbon\Carbon::parse($surat->selesai_berlaku)) + 1
                        : null;
                @endphp
                {{ $lamaIstirahat !== null ? $lamaIstirahat : '-' }} hari
            </strong>,
            terhitung mulai tanggal
            <strong>{{ \Carbon\Carbon::parse($surat->mulai_berlaku)->locale('id')->translatedFormat('d F Y') }}</strong>
            sampai dengan
            <strong>{{ \Carbon\Carbon::parse($surat->selesai_berlaku)->locale('id')->translatedFormat('d F Y') }}</strong>.
        </p>

        <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="footer-ttd">
        <table>
            <tr>
                <td></td>
                <td>
                    Banjarmasin, {{ \Carbon\Carbon::parse($surat->mulai_berlaku ?? now())->locale('id')->translatedFormat('d F Y') }}<br>
                    Dokter Pemeriksa
                    <div class="ttd-space" style="position: relative; height: 100px; width: 180px; margin: 0 auto;">
                        @if ($surat->tipe_ttd === 'digital')
                            <img src="{{ public_path('/assets/aset/ttd_dokter.jpg') }}"
                                style="width: 100px; height: auto; position: absolute; top: 10px; left: 0;">
                            <img src="{{ public_path('/assets/aset/test.png') }}"
                                style="width: 90px; height: auto; position: absolute; top: 0; left: 40px; opacity: 0.7;">
                        @elseif ($surat->tipe_ttd === 'basah')
                            <div style="height: 100px;"></div>
                        @endif
                    </div>
                    <span class="ttd-name">{{ $dokter->nama_dokter ?? '-' }}</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>