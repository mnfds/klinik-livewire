<?php

namespace App\Livewire\Aruskas\Klinik;

use App\Models\Pendapatanlainnya;
use App\Models\TransaksiKlinik;
use App\Models\Uangkeluar;
use Carbon\Carbon;
use Livewire\Component;

class GrafikTahunan extends Component
{
    public function render()
    {
        return view('livewire.aruskas.klinik.grafik-tahunan');
    }

    public function mount()
    {
        $this->loadDefaultData();
    }

    private function loadDefaultData()
    {
        [$klinikBarMasuk, $klinikBarKeluar] = $this->hitungKlinikBarTahunan();

        $this->dispatch('update-klinik-tahunan-bar', [
            'labelsTahunan' => [
                '2024','2025','2026','2027','2028','2029',
                '2030','2031','2032','2033','2034','2035'
            ],
            'klinikTahunanBarMasuk'  => $klinikBarMasuk,
            'klinikTahunanBarKeluar' => $klinikBarKeluar,
        ]);
    }

    private function hitungKlinikBarTahunan(): array
    {
        $startYear = 2024;
        $endYear   = 2035;

        $klinikMasuk  = [];
        $klinikKeluar = [];

        for ($year = $startYear; $year <= $endYear; $year++) {

            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end   = Carbon::create($year, 12, 31)->endOfDay();

            // === PEMASUKAN ===
            $masukKlinik = TransaksiKlinik::whereBetween('tanggal_transaksi', [$start, $end])
                ->sum('total_tagihan_bersih');

            $masukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
                ->where('unit_usaha', 'Klinik')
                ->whereIn('status', ['lunas', 'belum lunas'])
                ->sum('total_tagihan');

            // === PENGELUARAN ===
            $keluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
                ->where('unit_usaha', 'Klinik')
                ->where('status', 'Disetujui')
                ->sum('jumlah_uang');

            $klinikMasuk[]  = (int) ($masukKlinik + $masukLainnya);
            $klinikKeluar[] = (int) $keluar;
        }

        return [$klinikMasuk, $klinikKeluar];
    }
}
