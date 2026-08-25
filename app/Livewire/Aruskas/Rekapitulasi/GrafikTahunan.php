<?php

namespace App\Livewire\Aruskas\Rekapitulasi;

use Livewire\Component;
use App\Models\Uangkeluar;
use Illuminate\Support\Carbon;
use App\Models\TransaksiApotik;
use App\Models\TransaksiKlinik;
use App\Models\Pendapatanlainnya;

class GrafikTahunan extends Component
{
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

    public function loadGrafik()
    {
        [$rekapBarMasuk, $rekapBarKeluar] = $this->hitungRekapBarTahunan();

        $this->dispatch('update-rekap-tahunan-bar', [
            'labelsTahunan' => [
                '2024','2025','2026','2027','2028','2029',
                '2030','2031','2032','2033','2034','2035'
            ],
            'rekapTahunanBarMasuk'  => $rekapBarMasuk,
            'rekapTahunanBarKeluar' => $rekapBarKeluar,
        ]);
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

        $this->loadGrafik();
    }

    public function resetAll(): void
    {
        $this->reset(['tipe', 'filterUnitUsaha', 'filterMetodePembayaran', 'filterJenisPengeluaran',
                       'draftTipe', 'draftUnitUsaha', 'draftMetodePembayaran', 'draftJenisPengeluaran']);
        $this->loadGrafik();
    }

    private function hitungRekapBarTahunan(): array
    {
        $startYear = 2024;
        $endYear   = 2035;

        $rekapMasuk  = [];
        $rekapKeluar = [];

        for ($year = $startYear; $year <= $endYear; $year++) {
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end   = Carbon::create($year, 12, 31)->endOfDay();

            $totalKlinik = 0;
            $totalApotik = 0;

            if ($this->tipe !== 'keluar' && ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Klinik')) {
                $totalKlinik = TransaksiKlinik::whereBetween('tanggal_transaksi', [$start, $end])
                    ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                    ->sum('total_tagihan_bersih');
            }

            if ($this->tipe !== 'keluar' && ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Apotik')) {
                $totalApotik = TransaksiApotik::whereBetween('tanggal', [$start, $end])
                    ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                    ->sum('total_tagihan_bersih');
            }

            $totalLainnya = 0;
            if ($this->tipe !== 'keluar') {
                $totalLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
                    ->whereIn('status', ['lunas', 'belum lunas'])
                    ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
                    ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                    ->sum('total_dibayarkan');
            }

            $totalKeluar = 0;
            if ($this->tipe !== 'masuk') {
                $totalKeluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
                    ->where('status', 'Disetujui')
                    ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
                    ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                    ->when($this->filterJenisPengeluaran, fn ($q) => $q->where('jenis_pengeluaran', $this->filterJenisPengeluaran))
                    ->sum('jumlah_uang');
            }

            $rekapMasuk[]  = (int) ($totalKlinik + $totalApotik + $totalLainnya);
            $rekapKeluar[] = (int) $totalKeluar;
        }

        return [$rekapMasuk, $rekapKeluar];
    }

    public function render()
    {
        return view('livewire.aruskas.rekapitulasi.grafik-tahunan');
    }
}