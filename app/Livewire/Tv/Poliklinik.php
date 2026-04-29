<?php

namespace App\Livewire\Tv;

use App\Models\PoliKlinik as ModelsPoliKlinik;
use Livewire\Component;

class Poliklinik extends Component
{
    public $poli;

    public function mount()
    {
        $this->loadAntrianPoli();
    }

    public function loadAntrianPoli()
    {
        $today = today()->toDateString();

        $this->poli = ModelsPoliKlinik::where('status', true)
        ->with(['pasienTerdaftars' => function ($q) use ($today){
            $q->whereDate('created_at', $today)
            ->where('status_terdaftar', 'terdaftar')
            ->orderBy('updated_at', 'asc')
            ->limit(5);
        }])->get();
    }
    
    public function render()
    {
        return view('livewire.tv.poliklinik');
    }
}
