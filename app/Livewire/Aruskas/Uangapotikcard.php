<?php

namespace App\Livewire\Aruskas;

use App\Models\TransaksiApotik;
use Livewire\Component;
use Illuminate\Support\Carbon;

class Uangapotikcard extends Component
{
    public $startDate;
    public $endDate;

    public $totalMasuk = 0;
    public $totalKeluar = 0;
    public $totalBersih = 0;

    public function mount()
    {
        $this->loadDefaultData();
    }

    public function tanggalDipilih()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        $this->totalMasuk = TransaksiApotik::whereBetween('tanggal', [$start, $end])
            ->sum('total_harga');
        $this->totalKeluar = 1000000;
        $this->totalBersih = $this->totalMasuk - $this->totalKeluar;
    }

    public function resetData()
    {
        $this->loadDefaultData();
    }
    
    private function loadDefaultData()
    {
        $this->startDate = now()->format('Y-m-d');
        $this->endDate   = now()->format('Y-m-d');

        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        $this->totalMasuk = TransaksiApotik::whereBetween('tanggal',[$start, $end])
            ->sum('total_harga');
        $this->totalKeluar = 100;
        $this->totalBersih = $this->totalMasuk - $this->totalKeluar;
    }

    public function render()
    {
        return view('livewire.aruskas.uangapotikcard');
    }
}
