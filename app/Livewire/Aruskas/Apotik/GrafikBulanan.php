<?php

namespace App\Livewire\Aruskas\Apotik;

use App\Models\Pendapatanlainnya;
use App\Models\TransaksiApotik;
use App\Models\Uangkeluar;
use Illuminate\Support\Carbon;
use Livewire\Component;

class GrafikBulanan extends Component
{
    public $tahun;

    public function render()
    {
        return view('livewire.aruskas.apotik.grafik-bulanan');
    }

    public function mount()
    {
        $this->loadDefaultData();
    }

    public function tahunDipilih()
    {
        $year = $this->tahun;

        [$apotikBarMasuk, $apotikBarKeluar] = $this->hitungRekapBarBulanan((int) $year);

        // ===== KIRIM KE JS =====
        $this->dispatch('update-apotik-bulanan-bar', [
            'labelsBulan' => [
                'Jan','Feb','Mar','Apr','Mei','Jun',
                'Jul','Agu','Sep','Okt','Nov','Des'
            ],
            'apotikBulananBarMasuk' => $apotikBarMasuk,
            'apotikBulananBarKeluar' => $apotikBarKeluar,
        ]);
    }

    private function loadDefaultData()
    {
        $this->tahun = now()->year;
        $year = $this->tahun;

        [$apotikBarMasuk, $apotikBarKeluar] = $this->hitungRekapBarBulanan((int) $year);

        // ===== KIRIM KE JS =====
        $this->dispatch('update-apotik-bulanan-bar', [
            'labelsBulan' => [
                'Jan','Feb','Mar','Apr','Mei','Jun',
                'Jul','Agu','Sep','Okt','Nov','Des'
            ],
            'apotikBulananBarMasuk' => $apotikBarMasuk,
            'apotikBulananBarKeluar' => $apotikBarKeluar,
        ]);
    }

    private function hitungRekapBarBulanan(int $year): array
    {
        $start = Carbon::create($year, 1, 1)->startOfDay();
        $end   = Carbon::create($year, 12, 31)->endOfDay();

        // === PEMASUKAN ===
        $masukApotik = TransaksiApotik::whereBetween('tanggal', [$start, $end])
            ->selectRaw('MONTH(tanggal) as bulan, SUM(total_harga) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $masukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
            ->where('unit_usaha', 'Apotik')
            ->whereIn('status', ['lunas', 'belum lunas'])
            ->selectRaw('MONTH(tanggal_transaksi) as bulan, SUM(total_tagihan) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        // === PENGELUARAN ===
        $keluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
            ->where('unit_usaha', 'Apotik')
            ->where('status', 'Disetujui')
            ->selectRaw('MONTH(tanggal_pengajuan) as bulan, SUM(jumlah_uang) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        // === SUSUN JAN–DES ===
        $apotikBarMasuk  = [];
        $apotikBarKeluar = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $apotikBarMasuk[] = (int) (
                ($masukApotik[$bulan] ?? 0)
            + ($masukLainnya[$bulan] ?? 0));

            $apotikBarKeluar[] = (int) ($keluar[$bulan] ?? 0);
        }

        return [$apotikBarMasuk, $apotikBarKeluar];
    }

    public function resetData()
    {
        $this->loadDefaultData();
    }
}
