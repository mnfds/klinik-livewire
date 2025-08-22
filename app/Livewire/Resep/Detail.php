<?php

namespace App\Livewire\Resep;

use Livewire\Component;
use App\Models\PasienTerdaftar;

class Detail extends Component
{    
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;

    public function mount($pasien_terdaftar_id = null)
    {
        $this->pasien_terdaftar_id = $pasien_terdaftar_id;

        $this->pasienTerdaftar = PasienTerdaftar::with([
            'rekamMedis.obatNonRacikanRM',
            'rekamMedis.obatRacikanRM.bahanRacikan',
        ])->findOrFail($this->pasien_terdaftar_id);
    }
    public function render()
    {
        return view('livewire.resep.detail');
    }
}
