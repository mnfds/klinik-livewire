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

    public function handleQrScanned(string $result): void
    {
        if ($result === $this->user_code_qr) {
            $this->scannedData = $this->user_code_qr;
            $this->booleanScan = true;
            $this->dispatch('openTakeModal');
        } else {
            $this->scannedData = "QR Code Salah / Tidak Dikenali";
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
