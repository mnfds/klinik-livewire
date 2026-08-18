<?php

namespace App\Livewire\Uangkeluar;

use Livewire\Component;

class Data extends Component
{
    public string $filterJenis = '';
    public string $filterUnitUsaha = '';
    public string $filterMetodePembayaran = '';

    public function render()
    {
        return view('livewire.uangkeluar.data');
    }

    public function updated($property): void
    {
        if (in_array($property, ['filterJenis', 'filterUnitUsaha', 'filterMetodePembayaran'])) {
            $this->dispatch('uangkeluar-filter-updated',
                jenis: $this->filterJenis,
                unitUsaha: $this->filterUnitUsaha,
                metodePembayaran: $this->filterMetodePembayaran,
            );
        }
    }
}
