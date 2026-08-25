<?php

namespace App\Livewire\Aruskas\Rekapitulasi;

use Livewire\Component;
use App\Models\Uangkeluar;
use Illuminate\Support\Carbon;
use App\Models\TransaksiApotik;
use App\Models\TransaksiKlinik;
use App\Models\Pendapatanlainnya;

class GrafikBulanan extends Component
{
    public $tahun;

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

    public function render()
    {
        return view('livewire.aruskas.rekapitulasi.grafik-bulanan');
    }

    public function mount()
    {
        $this->tahun = now()->year;
    }

    public function loadGrafik()
    {
        $year = (int) $this->tahun;
        [$rekapBarMasuk, $rekapBarKeluar] = $this->hitungRekapBarBulanan($year);

        $this->dispatch('update-rekap-bulanan-bar', [
            'labelsBulan' => ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
            'rekapBulananBarMasuk'  => $rekapBarMasuk,
            'rekapBulananBarKeluar' => $rekapBarKeluar,
        ]);
    }

    public function tahunDipilih()
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

    public function resetAll()
    {
        $this->reset(['tipe', 'filterUnitUsaha', 'filterMetodePembayaran', 'filterJenisPengeluaran',
                       'draftTipe', 'draftUnitUsaha', 'draftMetodePembayaran', 'draftJenisPengeluaran']);
        $this->tahun = now()->year;
        $this->loadGrafik();
    }

    private function hitungRekapBarBulanan(int $year): array
    {
        $start = Carbon::create($year, 1, 1)->startOfDay();
        $end   = Carbon::create($year, 12, 31)->endOfDay();

        // === PEMASUKAN ===
        $masukKlinik = collect();
        $masukApotik = collect();

        if ($this->tipe !== 'keluar' && ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Klinik')) {
            $masukKlinik = TransaksiKlinik::whereBetween('tanggal_transaksi', [$start, $end])
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->selectRaw('MONTH(tanggal_transaksi) as bulan, SUM(total_tagihan_bersih) as total')
                ->groupBy('bulan')
                ->pluck('total', 'bulan');
        }

        if ($this->tipe !== 'keluar' && ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Apotik')) {
            $masukApotik = TransaksiApotik::whereBetween('tanggal', [$start, $end])
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->selectRaw('MONTH(tanggal) as bulan, SUM(total_tagihan_bersih) as total')
                ->groupBy('bulan')
                ->pluck('total', 'bulan');
        }

        $masukLainnya = collect();
        if ($this->tipe !== 'keluar') {
            $masukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
                ->whereIn('status', ['lunas', 'belum lunas'])
                ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->selectRaw('MONTH(tanggal_transaksi) as bulan, SUM(total_dibayarkan) as total')
                ->groupBy('bulan')
                ->pluck('total', 'bulan');
        }

        // === PENGELUARAN ===
        $keluar = collect();
        if ($this->tipe !== 'masuk') {
            $keluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
                ->where('status', 'Disetujui')
                ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->when($this->filterJenisPengeluaran, fn ($q) => $q->where('jenis_pengeluaran', $this->filterJenisPengeluaran))
                ->selectRaw('MONTH(tanggal_pengajuan) as bulan, SUM(jumlah_uang) as total')
                ->groupBy('bulan')
                ->pluck('total', 'bulan');
        }

        // === SUSUN JAN–DES ===
        $rekapBarMasuk  = [];
        $rekapBarKeluar = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $rekapBarMasuk[] = (int) (
                ($masukKlinik[$bulan] ?? 0)
              + ($masukApotik[$bulan] ?? 0)
              + ($masukLainnya[$bulan] ?? 0));

            $rekapBarKeluar[] = (int) ($keluar[$bulan] ?? 0);
        }

        return [$rekapBarMasuk, $rekapBarKeluar];
    }
}