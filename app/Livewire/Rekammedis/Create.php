<?php

namespace App\Livewire\Rekammedis;

use App\Models\PasienTerdaftar;
use App\Models\KajianAwal;
use Livewire\Component;
use Livewire\Volt\Compilers\Mount;

class Create extends Component
{
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;
    public $kajian;

    public function mount($pasien_terdaftar_id = null){
        $this->pasien_terdaftar_id = $pasien_terdaftar_id;

        if ($this->pasien_terdaftar_id) {
            $this->pasienTerdaftar = PasienTerdaftar::findOrFail($this->pasien_terdaftar_id);
            $this->kajian = kajianAwal::findOrFail($this->pasienTerdaftar->id);
        }
    }

    public function render()
    {
        return view('livewire.rekammedis.create');
    }
}
