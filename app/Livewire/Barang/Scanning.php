<?php

namespace App\Livewire\Barang;

use App\Models\Barang;
use Livewire\Component;

class Scanning extends Component
{
    public $barang_id, $jumlah, $diajukan_oleh, $catatan;
    public $tipe = 'keluar';
    public $scannedData; // hasil scan tampil dari sini
    public $booleanScan = false; //default warna
    public $qrcode = 'BRG140426DRL';

    public $barang = [];

    public function mount()
    {
        $this->barang = Barang::all();
    }

    #[\Livewire\Attributes\On('qrScanned')]
    public function handleQrScanned(string $result): void
    {
        if ($result === $this->qrcode) {
            $this->scannedData = "QR Code Benar (BRG140426DRL)";
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
        return view('livewire.barang.scanning');
    }
}
