<?php

namespace App\Livewire\Apotik;

use Livewire\Component;
use App\Models\TransaksiApotik;

class Detail extends Component
{
    public $transaksi;

    public function mount($id)
    {
        $this->transaksi = TransaksiApotik::with(['riwayat.produk'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.apotik.detail');
    }
}
