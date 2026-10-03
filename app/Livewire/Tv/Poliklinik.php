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
            ->with(['pasienTerdaftars' => function ($q) use ($today) {
                $q->whereDate('created_at', $today)
                ->whereIn('status_terdaftar', ['terdaftar', 'konsultasi'])
                ->orderBy('updated_at', 'asc');
            }])
            ->get()
            ->each(function ($poli) {
                // Kecantikan langsung konsultasi, poli lain menunggu kajian awal (terdaftar)
                $status = $poli->kode === 'KCT'   // sesuaikan dengan penanda poli kecantikan Anda
                    ? ['konsultasi']
                    : ['terdaftar'];

                $poli->setRelation(
                    'pasienTerdaftars',
                    $poli->pasienTerdaftars
                        ->whereIn('status_terdaftar', $status)
                        ->take(5)
                        ->values()
                );
            });
    }
    
    public function render()
    {
        return view('livewire.tv.poliklinik');
    }
}
