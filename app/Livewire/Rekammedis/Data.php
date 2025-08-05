<?php

namespace App\Livewire\Rekammedis;

use App\Models\Pasien;
use App\Models\PasienTerdaftar;
use Livewire\Component;

class Data extends Component
{
    public ?int $pasien_id = null;
    public ?Pasien $pasien = null;

    public $pasienTerdaftar;

    public function mount($pasien_id = null)
    {
        $this->pasien_id = $pasien_id;
        if ($this->pasien_id) {
            $this->pasien = Pasien::find($this->pasien_id);
            
            $this->pasienTerdaftar = $this->pasien->kunjungan()->latest()->get();
        }

    }

    public function render()
    {
        return view('livewire.rekammedis.data');
    }
}
