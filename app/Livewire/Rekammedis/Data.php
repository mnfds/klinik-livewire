<?php

namespace App\Livewire\Rekammedis;

use App\Models\Pasien;
use Livewire\Component;

class Data extends Component
{
    public ?int $pasien_id = null;
    public ?Pasien $pasien = null;

    public function mount($pasien_id = null)
    {
        $this->pasien_id = $pasien_id;
        if ($this->pasien_id) {
            $this->pasien = Pasien::find($this->pasien_id);
        }
    }

    public function render()
    {
        return view('livewire.rekammedis.data');
    }
}
