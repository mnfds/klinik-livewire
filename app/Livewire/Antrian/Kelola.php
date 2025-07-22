<?php

namespace App\Livewire\Antrian;

use Livewire\Component;

class Kelola extends Component
{
    public function refreshTableMasuk()
    {
        $this->dispatch('pg:eventRefresh-AntrianMasuk');
    }
    public function refreshTableDipanggil()
    {
        $this->dispatch('pg:eventRefresh-AntrianDipanggil');
    }

    public function render()
    {
        return view('livewire.antrian.kelola');
    }
}
