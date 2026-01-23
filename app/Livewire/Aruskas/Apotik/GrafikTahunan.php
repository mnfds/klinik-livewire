<?php

namespace App\Livewire\Aruskas\Apotik;

use App\Models\Pendapatanlainnya;
use App\Models\TransaksiApotik;
use App\Models\Uangkeluar;
use Illuminate\Support\Carbon;
use Livewire\Component;

class GrafikTahunan extends Component
{
    public function render()
    {
        return view('livewire.aruskas.apotik.grafik-tahunan');
    }

    public function mount()
    {
        $this->loadDefaultData();
    }

    private function loadDefaultData()
    {
        [$apotikBarMasuk, $apotikBarKeluar] = $this->hitungApotikBarTahunan();

        $this->dispatch('update-apotik-tahunan-bar', [
            'labelsTahunan' => [
                '2024','2025','2026','2027','2028','2029',
                '2030','2031','2032','2033','2034','2035'
            ],
            'apotikTahunanBarMasuk'  => $apotikBarMasuk,
            'apotikTahunanBarKeluar' => $apotikBarKeluar,
        ]);
    }

    private function hitungApotikBarTahunan(): array
    {
        $startYear = 2024;
        $endYear   = 2035;

        $apotikMasuk  = [];
        $apotikKeluar = [];

        for ($year = $startYear; $year <= $endYear; $year++) {

            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end   = Carbon::create($year, 12, 31)->endOfDay();

            // === PEMASUKAN ===
            $masukApotik = TransaksiApotik::whereBetween('tanggal', [$start, $end])
                ->sum('total_harga');

            $masukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
                ->where('unit_usaha', 'Apotik')
                ->whereIn('status', ['lunas', 'belum lunas'])
                ->sum('total_tagihan');

            // === PENGELUARAN ===
            $keluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
                ->where('unit_usaha', 'Apotik')
                ->where('status', 'Disetujui')
                ->sum('jumlah_uang');

            $apotikMasuk[]  = (int) ($masukApotik + $masukLainnya);
            $apotikKeluar[] = (int) $keluar;
        }

        return [$apotikMasuk, $apotikKeluar];
    }
}
