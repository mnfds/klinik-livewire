<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Sehat</title>
    <style>
        @page {
            margin: 160px 60px 80px 60px;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            line-height: 1.4;
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
            margin-bottom: 10px;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 20px 0;
        }
        table.data td {
            padding: 2px 0;
            vertical-align: top;
        }
        table.data td.label {
            width: 160px;
        }
        table.data td.colon {
            width: 15px;
        }
        .section-title {
            font-weight: bold;
            font-size: 11px;
            margin: 8px 0 4px 0;
            text-transform: uppercase;
            border-bottom: 1px solid #999;
            padding-bottom: 2px;
        }
        table.vital {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 8px 0;
        }
        table.vital th, table.vital td {
            border: 1px solid #999;
            padding: 2px 6px;
            font-size: 10px;
            text-align: left;
            line-height: 1.3;
        }
        table.vital th {
            background-color: #f2f2f2;
            width: 40%;
        }
        .kesimpulan-box {
            border: 1px solid #999;
            padding: 6px 8px;
            margin: 8px 0 10px 0;
            background-color: #fafafa;
            font-size: 11px;
        }
        .footer-ttd {
            margin-top: 30px;
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
        <h2>Surat Keterangan Sehat</h2>
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
            dengan hasil pemeriksaan sebagai berikut:
        </p>

        @if($tandaVital)
        <div class="section-title">Hasil Pemeriksaan Tanda Vital</div>
        <table class="vital">
            <tr>
                <th>Tekanan Darah</th>
                <td>{{ $tandaVital->sistole ?? '-' }}/{{ $tandaVital->diastole }} mmHg</td>
            </tr>
            @if ($surat->jenis_surat === "lengkap" && !empty($tandaVital?->nadi))
            <tr>
                <th>Nadi</th>
                <td>{{ $tandaVital->nadi ?? '-' }} kali/menit</td>
            </tr>
            @endif
            @if ($surat->jenis_surat === "lengkap" && !empty($tandaVital?->suhu_tubuh))  
            <tr>
                <th>Suhu Tubuh</th>
                <td>{{ $tandaVital->suhu_tubuh ?? '-' }} °C</td>
            </tr>
            @endif
            @if ($surat->jenis_surat === "lengkap" && !empty($tandaVital?->frekuensi_pernapasan))
            <tr>
                <th>Frekuensi Pernapasan</th>
                <td>{{ $tandaVital->frekuensi_pernapasan ?? '-' }} kali/menit</td>
            </tr>
            @endif
        </table>
        @endif

        @if($pemeriksaanFisik)
        <div class="section-title">Hasil Pemeriksaan Fisik</div>
        <table class="vital">
            <tr>
                <th>Tinggi Badan</th>
                <td>{{ $pemeriksaanFisik->tinggi_badan ?? '-' }} cm</td>
            </tr>
            <tr>
                <th>Berat Badan</th>
                <td>{{ $pemeriksaanFisik->berat_badan ?? '-' }} kg</td>
            </tr>
        </table>
        @endif

        @if($kolestrol)
            @if ($surat->jenis_surat === "lengkap")
                <div class="section-title">Hasil Pemeriksaan Kolesterol</div>
                <table class="vital">  
                    @if (!empty($kolestrol?->kolestrol_hdl))
                    <tr>
                        <th>Kolesterol Baik (HDL)</th>
                        <td>{{ $kolestrol->kolestrol_hdl ?? '-' }} mg/dL</td>
                    </tr>
                    @endif             
                    @if (!empty($kolestrol?->kolestrol_ldl))
                    <tr>
                        <th>Kolesterol Jahat (LDL)</th>
                        <td>{{ $kolestrol->kolestrol_ldl ?? '-' }} mg/dL</td>
                    </tr>
                    @endif 
                    @if (!empty($kolestrol?->trigliserida))
                    <tr>
                        <th>Trigliserida</th>
                        <td>{{ $kolestrol->trigliserida ?? '-' }} mg/dL</td>
                    </tr>
                    @endif
                    @if (!empty($kolestrol?->kolestrol_total))
                    <tr>
                        <th>Kolesterol Total</th>
                        <td>{{ $kolestrol->kolestrol_total ?? '-' }} mg/dL</td>
                    </tr>
                    @endif
                </table>
            @endif
        @endif

        <div class="kesimpulan-box">
            <strong>Kesimpulan:</strong> Berdasarkan hasil pemeriksaan di atas, yang bersangkutan dinyatakan
            dalam kondisi <strong>sehat</strong> dan tidak ditemukan kelainan yang berarti, sehingga mampu
            untuk melakukan aktivitas sehari-hari / bekerja secara normal
            @if(!empty($surat->perihal_alasan))
                dalam rangka <strong>{{ $surat->perihal_alasan }}</strong>.
            @else
                .
            @endif
        </div>

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
                            <img src="{{ public_path('storage/' . $dokter->ttd_digital) }}"
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