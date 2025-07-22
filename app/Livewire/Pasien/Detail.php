<?php

namespace App\Livewire\Pasien;

use App\Models\Pasien;
use Livewire\Component;

class Detail extends Component
{
    public $id;
    public $pasien;

    public function mount($id)
    {
        $this->id = $id;
        $this->pasien = Pasien::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.pasien.detail');
    }
}
