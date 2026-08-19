<?php

namespace App\Livewire\Aruskas;

use App\Models\Pendapatanlainnya;
use App\Models\TransaksiKlinik;
use App\Models\TransaksiApotik;
use App\Models\Uangkeluar;
use Livewire\Component;
use Illuminate\Support\Carbon;

class Uangrekapitulasicard extends Component
{
    public $startDate;
    public $endDate;

    // Filter aktif
    public string $tipe = '';
    public string $filterUnitUsaha = '';
    public string $filterMetodePembayaran = '';
    public string $filterJenisPengeluaran = '';

    // Draft (dipakai di dalam modal)
    public string $draftTipe = '';
    public string $draftUnitUsaha = '';
    public string $draftMetodePembayaran = '';
    public string $draftJenisPengeluaran = '';

    public $totalMasuk = 0;
    public $totalKeluar = 0;
    public $totalBersih = 0;

    public function mount()
    {
        $this->startDate = now()->format('Y-m-d');
        $this->endDate   = now()->format('Y-m-d');
        $this->hitung();
    }

    public function tanggalDipilih()
    {
        $this->hitung();
    }

    public function resetAll(): void
    {
        $this->reset(['tipe', 'filterUnitUsaha', 'filterMetodePembayaran', 'filterJenisPengeluaran',
                    'draftTipe', 'draftUnitUsaha', 'draftMetodePembayaran', 'draftJenisPengeluaran']);
        $this->startDate = now()->format('Y-m-d');
        $this->endDate   = now()->format('Y-m-d');
        $this->hitung();
    }

    public function openFilterModal(): void
    {
        $this->draftTipe = $this->tipe;
        $this->draftUnitUsaha = $this->filterUnitUsaha;
        $this->draftMetodePembayaran = $this->filterMetodePembayaran;
        $this->draftJenisPengeluaran = $this->filterJenisPengeluaran;
    }

    public function filter(): void
    {
        $this->tipe = $this->draftTipe;
        $this->filterUnitUsaha = $this->draftUnitUsaha;
        $this->filterMetodePembayaran = $this->draftMetodePembayaran;
        $this->filterJenisPengeluaran = $this->tipe === 'keluar' ? $this->draftJenisPengeluaran : '';

        $this->hitung();
    }

    protected function hitung(): void
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        $this->totalMasuk  = $this->tipe === 'keluar' ? 0 : $this->hitungMasuk($start, $end);
        $this->totalKeluar = $this->tipe === 'masuk'  ? 0 : $this->hitungKeluar($start, $end);
        $this->totalBersih = $this->totalMasuk - $this->totalKeluar;
    }

    protected function hitungMasuk($start, $end): float
    {
        $totalKlinik = 0;
        $totalApotik = 0;

        if ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Klinik') {
            $totalKlinik = TransaksiKlinik::whereBetween('tanggal_transaksi', [$start, $end])
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->sum('total_tagihan_bersih');
        }

        if ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Apotik') {
            $totalApotik = TransaksiApotik::whereBetween('tanggal', [$start, $end])
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->sum('total_harga');
        }

        $totalLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
            ->whereIn('status', ['lunas', 'belum lunas'])
            ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
            ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
            ->sum('total_tagihan');

        return $totalKlinik + $totalApotik + $totalLainnya;
    }

    protected function hitungKeluar($start, $end): float
    {
        return Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
            ->where('status', 'Disetujui')
            ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
            ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
            ->when($this->filterJenisPengeluaran, fn ($q) => $q->where('jenis_pengeluaran', $this->filterJenisPengeluaran))
            ->sum('jumlah_uang');
    }

    public function render()
    {
        return view('livewire.aruskas.uangrekapitulasicard');
    }
}