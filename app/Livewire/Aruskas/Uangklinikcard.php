<?php

namespace App\Livewire\Aruskas;

use App\Models\Pendapatanlainnya;
use App\Models\TransaksiKlinik;
use App\Models\Uangkeluar;
use Livewire\Component;
use Illuminate\Support\Carbon;

class Uangklinikcard extends Component
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

        $totalMasuk = TransaksiKlinik::whereBetween('tanggal_transaksi', [$start, $end])
            ->sum('total_tagihan_bersih');
        $totalMasukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
            ->where('unit_usaha','Klinik')
            ->whereIn('status',['lunas', 'belum lunas'])
            ->sum('total_tagihan');
        $this->totalKeluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
            ->where('unit_usaha','Klinik')
            ->where('status','Disetujui')
            ->sum('jumlah_uang');
        $this->totalMasuk = $totalMasuk + $totalMasukLainnya;
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

        $totalMasuk = TransaksiKlinik::whereBetween('tanggal_transaksi',[$start, $end])
            ->sum('total_tagihan_bersih');
        $totalMasukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
            ->where('unit_usaha','Klinik')
            ->whereIn('status',['lunas', 'belum lunas'])
            ->sum('total_tagihan');
        $this->totalKeluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
            ->where('unit_usaha','Klinik')
            ->where('status','Disetujui')
            ->sum('jumlah_uang');

        $this->totalMasuk = $totalMasuk + $totalMasukLainnya;

        $this->totalBersih = $this->totalMasuk - $this->totalKeluar;
    }

    public function render()
    {
        return view('livewire.aruskas.uangklinikcard');
    }
}
