<?php

namespace App\Livewire\Surat;

use App\Models\SuratKeterangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Download extends Component
{
    #[\Livewire\Attributes\On('getUnduh')]
    public function getUnduh($rowId)
    {
        $surat = SuratKeterangan::with([
            'pasienTerdaftar.pasien',
            'pasienTerdaftar.rekamMedis.tandaVitalRM',
            'pasienTerdaftar.rekamMedis.pemeriksaanFisikRM',
            'pasienTerdaftar.rekamMedis.kolestrolRM',
        ])->findOrFail($rowId);

        if ($surat->jenis_surat == 'standar') {
            $view = 'pdf.surat-keterangan-sehat';
            $labelJenis = 'Sehat';
        } elseif ($surat->jenis_surat == 'lengkap') {
            $view = 'pdf.surat-keterangan-sehat-lengkap';
            $labelJenis = 'Sehat_Lengkap';
        } else {
            $view = 'pdf.surat-keterangan-sakit';
            $labelJenis = 'Sakit';
        }

        $rekamMedis = $surat->pasienTerdaftar?->rekamMedis;
        $namaPasien = $surat->pasienTerdaftar?->pasien?->nama ?? 'Pasien';

        $pdf = Pdf::loadView($view, [
            'tanggal'          => $surat->mulai_berlaku,
            'surat'            => $surat,
            'pasien'           => $surat->pasienTerdaftar?->pasien,
            'dokter'           => $surat->pasienTerdaftar?->dokter,
            'tandaVital'       => $rekamMedis?->tandaVitalRM,
            'pemeriksaanFisik' => $rekamMedis?->pemeriksaanFisikRM,
            'kolestrol'        => $rekamMedis?->kolestrolRM,
        ])->setPaper('a4', 'portrait');

        $namaFile = $this->buatNamaFile($labelJenis, $namaPasien);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $namaFile
        );
    }

    private function buatNamaFile(string $labelJenis, string $namaPasien): string
    {
        // ganti spasi jadi underscore, buang karakter selain huruf/angka/underscore
        $namaBersih = preg_replace('/[^A-Za-z0-9]+/', '_', trim($namaPasien));
        $namaBersih = trim($namaBersih, '_');

        return "Surat_Keterangan_{$labelJenis}_{$namaBersih}.pdf";
    }

    public function render()
    {
        return view('livewire.surat.download');
    }
}
