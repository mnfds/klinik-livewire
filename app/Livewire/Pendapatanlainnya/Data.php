<?php

namespace App\Livewire\Pendapatanlainnya;

use Livewire\Component;

class Data extends Component
{
    public string $filterStatus = '';
    public string $filterUnitUsaha = '';
    public string $filterMetodePembayaran = '';
    public string $startDate = '';
    public string $endDate = '';

    public function render()
    {
        return view('livewire.pendapatanlainnya.data');
    }

    public function updated($property): void
    {
        if (in_array($property, ['filterStatus', 'filterUnitUsaha', 'filterMetodePembayaran'])) {
            $this->dispatchFilter();
        }
    }

    public function tanggalDipilih(): void
    {
        $this->dispatchFilter();
    }

    public function clearFilter(string $property): void
    {
        $this->{$property} = '';
        $this->dispatchFilter();
    }

    public function clearTanggal(): void
    {
        $this->startDate = '';
        $this->endDate = '';
        $this->dispatchFilter();
        $this->dispatch('reset-flatpickr');
    }
    
    public function clearAll(): void
    {
        $this->filterStatus = '';
        $this->filterUnitUsaha = '';
        $this->filterMetodePembayaran = '';
        $this->startDate = '';
        $this->endDate = '';

        $this->dispatchFilter();
        $this->dispatch('reset-flatpickr');
    }

    protected function dispatchFilter(): void
    {
        $this->dispatch('pendapatan-filter-updated',
            status: $this->filterStatus,
            unitUsaha: $this->filterUnitUsaha,
            metodePembayaran: $this->filterMetodePembayaran,
            tanggalStart: $this->startDate,
            tanggalEnd: $this->endDate,
        );
    }
}
