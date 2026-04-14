<?php

namespace App\Livewire\Barang;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Data extends Component
{
    public $qrcode = 'BRG140426DRL';

    public function generateQrCode(): string
    {
        return QrCode::size(200)
            ->errorCorrection('H')
            ->generate($this->qrcode);
    }
    
    public function render()
    {
        if (! Gate::allows('akses', 'Persediaan Barang')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.barang.data',[
            'qrImage' => $this->generateQrCode(),
        ]);
    }
}
