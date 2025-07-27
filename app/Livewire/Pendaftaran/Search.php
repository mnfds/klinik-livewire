<?php

namespace App\Livewire\Pendaftaran;

use App\Models\Pasien;
use Livewire\Component;

class Search extends Component
{
    public $selectedPasienId;

    protected $listeners = ['setPasien' => 'setPasienId'];

    public function setPasienId($id)
    {
        $this->selectedPasienId = $id;
        $pasien = \App\Models\Pasien::find($id);

        $this->emit('pasienSelected', $pasien);
    }

    public function render()
    {
        return view('livewire.pendaftaran.search');
    }
}
