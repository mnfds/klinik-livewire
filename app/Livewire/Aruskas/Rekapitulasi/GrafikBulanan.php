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

    private function hitungRekapBarBulanan(int $year): array
    {
        $start = Carbon::create($year, 1, 1)->startOfDay();
        $end   = Carbon::create($year, 12, 31)->endOfDay();

        // === PEMASUKAN ===
        $masukKlinik = TransaksiKlinik::whereBetween('tanggal_transaksi', [$start, $end])
            ->selectRaw('MONTH(tanggal_transaksi) as bulan, SUM(total_tagihan_bersih) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $masukApotik = TransaksiApotik::whereBetween('tanggal', [$start, $end])
            ->selectRaw('MONTH(tanggal) as bulan, SUM(total_harga) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $masukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
            ->whereIn('status', ['lunas', 'belum lunas'])
            ->selectRaw('MONTH(tanggal_transaksi) as bulan, SUM(total_tagihan) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        // === PENGELUARAN ===
        $keluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
            ->where('status', 'Disetujui')
            ->selectRaw('MONTH(tanggal_pengajuan) as bulan, SUM(jumlah_uang) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

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

    public function resetData()
    {
        $this->tahun = now()->year;
        $this->loadGrafik();
    }

}
