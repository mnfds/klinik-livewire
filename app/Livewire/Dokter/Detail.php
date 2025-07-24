<?php

namespace App\Livewire\Dokter;

use App\Models\Dokter;
use Livewire\Component;

class Detail extends Component
{
    public $id;
    public $dokter;
    public $poli = [];

    public function mount($id)
    {
        $this->id = $id;
        $this->dokter = Dokter::findOrFail($id);
        $this->poli = $this->dokter->dokterpoli()->with('poli')->get();
    }
    public function render()
    {
        return view('livewire.dokter.detail');
    }
}
