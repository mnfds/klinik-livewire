<?php

namespace App\Livewire\Uangkeluar;

use Livewire\Component;

class Data extends Component
{
    public string $filterJenis = '';
    public string $filterUnitUsaha = '';
    public string $filterMetodePembayaran = '';
    public string $startDate = '';
    public string $endDate = '';

    public function render()
    {
        return view('livewire.uangkeluar.data');
    }

    public function updated($property): void
    {
        if (in_array($property, ['filterJenis', 'filterUnitUsaha', 'filterMetodePembayaran'])) {
            $this->dispatchFilterUangKeluar();
        }
    }

    public function tanggalDipilihUangKeluar(): void
    {
        $this->dispatchFilterUangKeluar();
    }

    public function clearFilterUangKeluar(string $property): void
    {
        $this->{$property} = '';
        $this->dispatchFilterUangKeluar();
    }

    public function clearTanggalUangKeluar(): void
    {
        $this->startDate = '';
        $this->endDate = '';
        $this->dispatchFilterUangKeluar();
        $this->dispatch('reset-flatpickr');
    }
    
    public function clearAllFilterUangKeluar(): void
    {
        $this->filterJenis = '';
        $this->filterUnitUsaha = '';
        $this->filterMetodePembayaran = '';
        $this->startDate = '';
        $this->endDate = '';

        $this->dispatchFilterUangKeluar();
        $this->dispatch('reset-flatpickr');
    }

    protected function dispatchFilterUangKeluar(): void
    {
        $this->dispatch('uangkeluar-filter-updated',
            jenis: $this->filterJenis,
            unitUsaha: $this->filterUnitUsaha,
            metodePembayaran: $this->filterMetodePembayaran,
            tanggalStart: $this->startDate,
            tanggalEnd: $this->endDate,
        );
    }
}
