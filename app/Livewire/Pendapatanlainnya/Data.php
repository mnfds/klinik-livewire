<?php

namespace App\Livewire\Pendapatanlainnya;

use Livewire\Component;

class Data extends Component
{
    public string $filterStatus = '';
    public string $filterUnitUsaha = '';

    public function render()
    {
        return view('livewire.pendapatanlainnya.data');
    }

    public function updated($property): void
    {
        if (in_array($property, ['filterStatus', 'filterUnitUsaha'])) {
            $this->dispatch('pendapatan-filter-updated',
                status: $this->filterStatus,
                unitUsaha: $this->filterUnitUsaha,
            );
        }
    }
}
