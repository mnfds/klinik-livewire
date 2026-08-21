<?php

namespace App\Livewire\Aruskas\Rekapitulasi;

use Livewire\Component;
use Carbon\CarbonPeriod;
use App\Models\Uangkeluar;
use Illuminate\Support\Carbon;
use App\Models\TransaksiApotik;
use App\Models\TransaksiKlinik;
use App\Models\Pendapatanlainnya;

class GrafikHarian extends Component
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

    public $rekapPieMasuk = 0;
    public $rekapPieKeluar = 0;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate   = now()->endOfMonth()->format('Y-m-d');
    }

    public function loadGrafik()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        $this->hitungRekapPieHarian($start, $end);
        [$labelsTanggal, $rekapBarMasuk, $rekapBarKeluar] = $this->hitungRekapBarHarian($start, $end);

        $this->dispatch('update-rekap-harian-pie', [
            'rekapHarianPieMasuk'  => $this->rekapPieMasuk,
            'rekapHarianPieKeluar' => $this->rekapPieKeluar,
        ]);

        $this->dispatch('update-rekap-harian-bar', [
            'labelstanggal'        => $labelsTanggal,
            'rekapHarianBarMasuk'  => $rekapBarMasuk,
            'rekapHarianBarKeluar' => $rekapBarKeluar,
        ]);
    }

    public function tanggalDipilih()
    {
        $this->loadGrafik();
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
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate   = now()->endOfMonth()->format('Y-m-d');
        $this->loadGrafik();
    }

    private function hitungRekapPieHarian(Carbon $start, Carbon $end)
    {
        $this->rekapPieMasuk  = $this->tipe === 'keluar' ? 0 : $this->hitungMasuk($start, $end);
        $this->rekapPieKeluar = $this->tipe === 'masuk'  ? 0 : $this->hitungKeluar($start, $end);
    }

    private function hitungMasuk(Carbon $start, Carbon $end): float
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
            ->sum('total_dibayarkan');

        return $totalKlinik + $totalApotik + $totalLainnya;
    }

    private function hitungKeluar(Carbon $start, Carbon $end): float
    {
        return Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
            ->where('status', 'Disetujui')
            ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
            ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
            ->when($this->filterJenisPengeluaran, fn ($q) => $q->where('jenis_pengeluaran', $this->filterJenisPengeluaran))
            ->sum('jumlah_uang');
    }

    private function hitungRekapBarHarian(Carbon $start, Carbon $end)
    {
        $period = CarbonPeriod::create($start, $end);

        $masukKlinik = collect();
        $masukApotik = collect();

        if ($this->tipe !== 'keluar' && ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Klinik')) {
            $masukKlinik = TransaksiKlinik::whereBetween('tanggal_transaksi', [$start, $end])
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(total_tagihan_bersih) as total')
                ->groupBy('tanggal')
                ->pluck('total', 'tanggal');
        }

        if ($this->tipe !== 'keluar' && ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Apotik')) {
            $masukApotik = TransaksiApotik::whereBetween('tanggal', [$start, $end])
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->selectRaw('DATE(tanggal) as tanggal, SUM(total_harga) as total')
                ->groupBy('tanggal')
                ->pluck('total', 'tanggal');
        }

        $masukLainnya = collect();
        if ($this->tipe !== 'keluar') {
            $masukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
                ->whereIn('status', ['lunas', 'belum lunas'])
                ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(total_dibayarkan) as total')
                ->groupBy('tanggal')
                ->pluck('total', 'tanggal');
        }

        $keluar = collect();
        if ($this->tipe !== 'masuk') {
            $keluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
                ->where('status', 'Disetujui')
                ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->when($this->filterJenisPengeluaran, fn ($q) => $q->where('jenis_pengeluaran', $this->filterJenisPengeluaran))
                ->selectRaw('DATE(tanggal_pengajuan) as tanggal, SUM(jumlah_uang) as total')
                ->groupBy('tanggal')
                ->pluck('total', 'tanggal');
        }

        $labelsTanggal  = [];
        $rekapBarMasuk  = [];
        $rekapBarKeluar = [];

        foreach ($period as $date) {
            $tglKey = $date->format('Y-m-d');
            $labelsTanggal[]  = $date->format('d');
            $rekapBarMasuk[]  = ($masukKlinik[$tglKey] ?? 0) + ($masukApotik[$tglKey] ?? 0) + ($masukLainnya[$tglKey] ?? 0);
            $rekapBarKeluar[] = $keluar[$tglKey] ?? 0;
        }

        return [$labelsTanggal, $rekapBarMasuk, $rekapBarKeluar];
    }

    public function render()
    {
        return view('livewire.aruskas.rekapitulasi.grafik-harian');
    }
}
