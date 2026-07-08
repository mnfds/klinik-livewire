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
        $surat = SuratKeterangan::findOrFail($rowId);

        if ($surat->jenis_surat == 'standar') {
            $view = 'pdf.surat-keterangan-sehat';
        } elseif ($surat->jenis_surat == 'lengkap') {
            $view = 'pdf.surat-keterangan-sehat-lengkap';
        } else {
            $view = 'pdf.surat-keterangan-sakit';
        }

        $pdf = Pdf::loadView($view, [
            'tanggal' => $surat->mulai_berlaku,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "Surat_Keterangan.pdf"
        );
    }

    public function render()
    {
        return view('livewire.surat.download');
    }
}
