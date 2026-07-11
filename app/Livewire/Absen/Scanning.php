<?php

namespace App\Livewire\Absen;

use App\Models\Biodata;
use Livewire\Component;

class Scanning extends Component
{
    public $user_code_qr;
    public $scannedData; // hasil scan tampil dari sini
    public $booleanScan = false; //default warna

    public $staff = [];

    public function mount()
    {
        $this->staff = Biodata::all();
    }
    
    #[\Livewire\Attributes\On('qrScanned')]
    public function handleQrScanned(string $result): void
    {
        $biodata_ditemukan = Biodata::where('user_code_qr', $result)->first();

        if ($biodata_ditemukan) {
            $this->user_code_qr = $biodata_ditemukan->user_code_qr;
            $this->scannedData = $biodata_ditemukan->nama_lengkap;
            $this->booleanScan = true;
            $this->dispatch('openTakeModal');
        } else {
            $this->scannedData = "QR Code Salah / Tidak Dikenali";
            $this->booleanScan = false;
        }
    }

    public function resetScan(): void
    {
        $this->scannedData = null;
        $this->booleanScan = false;
        $this->dispatch('startScanner');
    }

    public function render()
    {
        return view('livewire.absen.scanning');
    }
}
