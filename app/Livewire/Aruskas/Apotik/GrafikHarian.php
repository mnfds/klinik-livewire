<?php

namespace App\Livewire\Aruskas\Apotik;

use App\Models\Pendapatanlainnya;
use App\Models\TransaksiApotik;
use App\Models\Uangkeluar;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;

class GrafikHarian extends Component
{

    public $startDate;
    public $endDate;

    public function render()
    {
        return view('livewire.aruskas.apotik.grafik-harian');
    }

    public function mount()
    {
        $this->loadDefaultData();
    }

    public function tanggalDipilih()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        [$apotikPieMasuk, $apotikPieKeluar] = $this->hitungApotikPieHarian($start, $end);
        [$labelsTanggal, $apotikBarMasuk, $apotikBarKeluar] = $this->hitungApotikBarHarian($start, $end);

        // ===== KIRIM KE JS =====
        $this->dispatch('update-apotik-harian-pie', [
            'apotikHarianPieMasuk'  => $apotikPieMasuk,
            'apotikHarianPieKeluar' => $apotikPieKeluar,
        ]);

        $this->dispatch('update-apotik-harian-bar', [
            'labelstanggal'  => $labelsTanggal,
            'apotikHarianBarMasuk'  => $apotikBarMasuk,
            'apotikHarianBarKeluar' => $apotikBarKeluar,
        ]);
    }

    private function loadDefaultData()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate   = now()->endOfMonth()->format('Y-m-d');

        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        [$apotikPieMasuk, $apotikPieKeluar] = $this->hitungApotikPieHarian($start, $end);
        [$labelsTanggal, $apotikBarMasuk, $apotikBarKeluar] = $this->hitungApotikBarHarian($start, $end);

        // ===== KIRIM KE JS =====
        $this->dispatch('update-apotik-harian-pie', [
            'apotikHarianPieMasuk'  => $apotikPieMasuk,
            'apotikHarianPieKeluar' => $apotikPieKeluar,
        ]);
        
        $this->dispatch('update-apotik-harian-bar', [
            'labelstanggal'  => $labelsTanggal,
            'apotikHarianBarMasuk'  => $apotikBarMasuk,
            'apotikHarianBarKeluar' => $apotikBarKeluar,
        ]);
    }

    private function hitungApotikPieHarian(Carbon $start, Carbon $end)
    {
        $totalMasukApotik = TransaksiApotik::whereBetween('tanggal', [$start, $end])
            ->sum('total_harga');

        $totalMasukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
            ->where('unit_usaha','Apotik')
            ->whereIn('status',['lunas', 'belum lunas'])
            ->sum('total_tagihan');

        $totalKeluarApotik = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
            ->where('unit_usaha','Apotik')
            ->where('status','Disetujui')
            ->sum('jumlah_uang');

        $apotikPieMasuk = (int) $totalMasukApotik + $totalMasukLainnya;
        $apotikPieKeluar = (int) $totalKeluarApotik;

        return [$apotikPieMasuk, $apotikPieKeluar];

    }

    private function hitungApotikBarHarian(Carbon $start, Carbon $end)
    {
        $period = CarbonPeriod::create($start, $end);

        // ===== AMBIL DATA PER TANGGAL =====
        $masukApotik = TransaksiApotik::whereBetween('tanggal', [$start, $end])
            ->selectRaw('DATE(tanggal) as tanggal, SUM(total_harga) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $masukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
            ->where('unit_usaha', 'Apotik')
            ->whereIn('status', ['lunas','belum lunas'])
            ->selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(total_tagihan) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $keluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
            ->where('unit_usaha', 'Apotik')
            ->where('status', 'Disetujui')
            ->selectRaw('DATE(tanggal_pengajuan) as tanggal, SUM(jumlah_uang) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        // ===== SUSUN DATA UNTUK CHART =====
        $labelsTanggal = [];
        $apotikBarMasuk = [];
        $apotikBarKeluar = [];

        foreach ($period as $date) {
            $tglKey = $date->format('Y-m-d');

            $labelsTanggal[] = $date->format('d'); // tampilkan tanggal saja
            $apotikBarMasuk[] = ($masukApotik[$tglKey] ?? 0) + ($masukLainnya[$tglKey] ?? 0);
            $apotikBarKeluar[] = $keluar[$tglKey] ?? 0;
        }

        return [$labelsTanggal, $apotikBarMasuk, $apotikBarKeluar];
    }

    public function resetData()
    {
        $this->loadDefaultData();
    }
}
