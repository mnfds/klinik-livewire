<?php

namespace App\Livewire\Barang;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Riwayat extends Component
{
    public function render()
    {
        return view('livewire.barang.riwayat');
    }
}
