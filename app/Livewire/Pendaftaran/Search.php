<?php

namespace App\Livewire\Pendaftaran;

use App\Models\NomorAntrian;
use App\Models\Pasien;
use Livewire\Component;

class Search extends Component
{
    public $selectedPasienId;
    public $antrianId;
    public $antrianTerdaftar;

    public function mount($id = null)
    {
        $this->antrianId = $id;

        if ($id) {
            $this->antrianTerdaftar = NomorAntrian::find($id);
        }
        dd($this->antrianTerdaftar);
    }

    protected $listeners = ['setPasien' => 'setPasienId'];

    public function setPasienId($id)
    {
        $this->selectedPasienId = $id;
        $pasien = \App\Models\Pasien::find($id);

        $this->emit('pasienSelected', $pasien);
    }

    public function render()
    {
        return view('livewire.pendaftaran.search', [
            'antrianTerdaftar' => $this->antrianTerdaftar,
        ]);
    }

}
