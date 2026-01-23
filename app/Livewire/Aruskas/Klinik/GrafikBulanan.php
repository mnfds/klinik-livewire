<?php

namespace App\Livewire\Aruskas\Klinik;

use App\Models\Pendapatanlainnya;
use App\Models\TransaksiKlinik;
use App\Models\Uangkeluar;
use Carbon\Carbon;
use Livewire\Component;

class GrafikBulanan extends Component
{
    public $tahun;

    public function render()
    {
        return view('livewire.aruskas.klinik.grafik-bulanan');
    }

    public function mount()
    {
        $this->loadDefaultData();
    }

    public function tahunDipilih()
    {
        $year = $this->tahun;

        [$klinikBarMasuk, $klinikBarKeluar] = $this->hitungRekapBarBulanan((int) $year);

        // ===== KIRIM KE JS =====
        $this->dispatch('update-klinik-bulanan-bar', [
            'labelsBulan' => [
                'Jan','Feb','Mar','Apr','Mei','Jun',
                'Jul','Agu','Sep','Okt','Nov','Des'
            ],
            'klinikBulananBarMasuk' => $klinikBarMasuk,
            'klinikBulananBarKeluar' => $klinikBarKeluar,
        ]);
    }

    private function loadDefaultData()
    {
        $this->tahun = now()->year;
        $year = $this->tahun;

        [$klinikBarMasuk, $klinikBarKeluar] = $this->hitungRekapBarBulanan((int) $year);

        // ===== KIRIM KE JS =====
        $this->dispatch('update-klinik-bulanan-bar', [
            'labelsBulan' => [
                'Jan','Feb','Mar','Apr','Mei','Jun',
                'Jul','Agu','Sep','Okt','Nov','Des'
            ],
            'klinikBulananBarMasuk' => $klinikBarMasuk,
            'klinikBulananBarKeluar' => $klinikBarKeluar,
        ]);
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

        $masukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
            ->where('unit_usaha', 'Klinik')
            ->whereIn('status', ['lunas', 'belum lunas'])
            ->selectRaw('MONTH(tanggal_transaksi) as bulan, SUM(total_tagihan) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        // === PENGELUARAN ===
        $keluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
            ->where('unit_usaha', 'Klinik')
            ->where('status', 'Disetujui')
            ->selectRaw('MONTH(tanggal_pengajuan) as bulan, SUM(jumlah_uang) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        // === SUSUN JAN–DES ===
        $klinikBarMasuk  = [];
        $klinikBarKeluar = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $klinikBarMasuk[] = (int) (
                ($masukKlinik[$bulan] ?? 0)
            + ($masukLainnya[$bulan] ?? 0));

            $klinikBarKeluar[] = (int) ($keluar[$bulan] ?? 0);
        }

        return [$klinikBarMasuk, $klinikBarKeluar];
    }

    public function resetData()
    {
        $this->loadDefaultData();
    }
}
