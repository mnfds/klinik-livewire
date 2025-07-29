<?php

namespace App\Livewire\Kajianawal;

use Livewire\Component;

class Pemeriksaanfisik extends Component
{
    public string $pemeriksaan_fisik;

    public function updatedPemeriksaanFisik()
    {
        logger('njay Dispatching event pemeriksaanFisikUpdated: ' . $this->pemeriksaan_fisik);

        $this->dispatch('pemeriksaanFisikUpdated', [
            'pemeriksaan_fisik' => $this->pemeriksaan_fisik,
        ]);
    }

    public function render()
    {
        return view('livewire.kajianawal.pemeriksaanfisik');
    }
}
