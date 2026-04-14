<?php

namespace App\Livewire\Barang;

use App\Models\Barang;
use Livewire\Component;

class Scanning extends Component
{
    public $barang_id, $jumlah, $diajukan_oleh, $catatan;
    public $tipe = 'keluar';
    public $scannedData; // hasil scan tampil dari sini

    public $barang = [];

    public function mount()
    {
        $this->barang = Barang::all();
    }

    #[\Livewire\Attributes\On('qrScanned')]
    public function handleQrScanned(string $result): void
    {
        $this->scannedData = $result;
    }

    public function resetScan(): void
    {
        $this->scannedData = null;
        $this->dispatch('startScanner');
    }

    public function render()
    {
        return view('livewire.barang.scanning');
    }
}
