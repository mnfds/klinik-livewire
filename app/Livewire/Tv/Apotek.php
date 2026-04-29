<?php

namespace App\Livewire\Tv;

use App\Models\PoliKlinik;
use Livewire\Component;

class Apotek extends Component
{
    public $poli;
    
    public function mount()
    {
        $this->loadAntrianApotek();
    }

    public function loadAntrianApotek()
    {
        $today = today()->toDateString();

        $this->poli = PoliKlinik::where('status', true)
        ->with(['pasienTerdaftars' => function ($q) use ($today){
            $q->whereDate('created_at', $today)
            ->where('status_terdaftar', 'peresepan')
            ->orderBy('updated_at', 'asc')
            ->limit(5);
        }])->get();
    }

    public function render()
    {
        return view('livewire.tv.apotek');
    }
}
