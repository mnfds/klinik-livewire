<?php

namespace App\Livewire\Tv;

use App\Models\NomorAntrian;
use App\Models\PoliKlinik;
use Livewire\Component;

class Pendaftaran extends Component
{
    public $poli;

    public function mount()
    {
        $this->loadAntrian();
    }

    public function loadAntrian()
    {
        $today = today()->toDateString();

        $this->poli = PoliKlinik::where('status', true)
            ->with(['antrians' => function ($q) use ($today) {
                $q->whereDate('created_at', $today)
                ->where('status', 'dipanggil')
                ->orderBy('updated_at', 'desc')
                ->limit(5);
            }])
            ->get();
    }

    public function render()
    {
        return view('livewire.tv.pendaftaran');
    }
}
