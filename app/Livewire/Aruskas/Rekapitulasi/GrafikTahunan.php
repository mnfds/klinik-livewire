<?php

namespace App\Livewire\Aruskas\Rekapitulasi;

use Livewire\Component;
use Carbon\CarbonPeriod;
use App\Models\Uangkeluar;
use Illuminate\Support\Carbon;
use App\Models\TransaksiApotik;
use App\Models\TransaksiKlinik;
use App\Models\Pendapatanlainnya;
use Illuminate\Support\Facades\DB;

class GrafikTahunan extends Component
{
    public function render()
    {
        return view('livewire.aruskas.rekapitulasi.grafik-tahunan');
    }

    public function mount()
    {
        $this->loadDefaultData();
    }

    private function loadDefaultData()
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

    private function hitungRekapBarTahunan(): array
    {
        $startYear = 2024;
        $endYear   = 2035;

        $rekapMasuk  = [];
        $rekapKeluar = [];

        for ($year = $startYear; $year <= $endYear; $year++) {

            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end   = Carbon::create($year, 12, 31)->endOfDay();

            // === PEMASUKAN ===
            $masukKlinik = TransaksiKlinik::whereBetween('tanggal_transaksi', [$start, $end])
                ->sum('total_tagihan_bersih');

            $masukApotik = TransaksiApotik::whereBetween('tanggal', [$start, $end])
                ->sum('total_harga');

            $masukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
                ->whereIn('status', ['lunas', 'belum lunas'])
                ->sum('total_tagihan');

            // === PENGELUARAN ===
            $keluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
                ->where('status', 'Disetujui')
                ->sum('jumlah_uang');

            $rekapMasuk[]  = (int) ($masukKlinik + $masukApotik + $masukLainnya);
            $rekapKeluar[] = (int) $keluar;
        }

        return [$rekapMasuk, $rekapKeluar];
    }
}
