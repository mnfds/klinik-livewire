<?php

namespace App\Livewire\Aruskas\Klinik;

use App\Models\Pendapatanlainnya;
use App\Models\TransaksiKlinik;
use App\Models\Uangkeluar;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Livewire\Component;

class GrafikHarian extends Component
{
    public $startDate;
    public $endDate;

    public function render()
    {
        return view('livewire.aruskas.klinik.grafik-harian');
    }

    public function mount()
    {
        $this->loadDefaultData();
    }

    public function tanggalDipilih()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        [$klinikPieMasuk, $klinikPieKeluar] = $this->hitungKlinikPieHarian($start, $end);
        [$labelsTanggal, $klinikBarMasuk, $klinikBarKeluar] = $this->hitungKlinikBarHarian($start, $end);

        // ===== KIRIM KE JS =====
        $this->dispatch('update-klinik-harian-pie', [
            'klinikHarianPieMasuk'  => $klinikPieMasuk,
            'klinikHarianPieKeluar' => $klinikPieKeluar,
        ]);

        $this->dispatch('update-klinik-harian-bar', [
            'labelstanggal'  => $labelsTanggal,
            'klinikHarianBarMasuk'  => $klinikBarMasuk,
            'klinikHarianBarKeluar' => $klinikBarKeluar,
        ]);
    }

    private function loadDefaultData()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate   = now()->endOfMonth()->format('Y-m-d');

        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        [$klinikPieMasuk, $klinikPieKeluar] = $this->hitungKlinikPieHarian($start, $end);
        [$labelsTanggal, $klinikBarMasuk, $klinikBarKeluar] = $this->hitungKlinikBarHarian($start, $end);

        // ===== KIRIM KE JS =====
        $this->dispatch('update-klinik-harian-pie', [
            'klinikHarianPieMasuk'  => $klinikPieMasuk,
            'klinikHarianPieKeluar' => $klinikPieKeluar,
        ]);
        
        $this->dispatch('update-klinik-harian-bar', [
            'labelstanggal'  => $labelsTanggal,
            'klinikHarianBarMasuk'  => $klinikBarMasuk,
            'klinikHarianBarKeluar' => $klinikBarKeluar,
        ]);
    }

    private function hitungKlinikPieHarian(Carbon $start, Carbon $end)
    {
        $totalMasukKlinik = TransaksiKlinik::whereBetween('tanggal_transaksi', [$start, $end])
            ->sum('total_tagihan_bersih');

        $totalMasukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
            ->where('unit_usaha','Klinik')
            ->whereIn('status',['lunas', 'belum lunas'])
            ->sum('total_tagihan');

        $totalKeluarKlinik = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
            ->where('unit_usaha','Klinik')
            ->where('status','Disetujui')
            ->sum('jumlah_uang');

        $klinikPieMasuk = (int) $totalMasukKlinik + $totalMasukLainnya;
        $klinikPieKeluar = (int) $totalKeluarKlinik;

        return [$klinikPieMasuk, $klinikPieKeluar];

    }

    private function hitungKlinikBarHarian(Carbon $start, Carbon $end)
    {
        $period = CarbonPeriod::create($start, $end);

        // ===== AMBIL DATA PER TANGGAL =====
        $masukKlinik = TransaksiKlinik::whereBetween('tanggal_transaksi', [$start, $end])
            ->selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(total_tagihan_bersih) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $masukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
            ->where('unit_usaha', 'Klinik')
            ->whereIn('status', ['lunas','belum lunas'])
            ->selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(total_tagihan) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $keluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
            ->where('unit_usaha', 'Klinik')
            ->where('status', 'Disetujui')
            ->selectRaw('DATE(tanggal_pengajuan) as tanggal, SUM(jumlah_uang) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        // ===== SUSUN DATA UNTUK CHART =====
        $labelsTanggal = [];
        $klinikBarMasuk = [];
        $klinikBarKeluar = [];

        foreach ($period as $date) {
            $tglKey = $date->format('Y-m-d');

            $labelsTanggal[] = $date->format('d'); // tampilkan tanggal saja
            $klinikBarMasuk[] = ($masukKlinik[$tglKey] ?? 0) + ($masukLainnya[$tglKey] ?? 0);
            $klinikBarKeluar[] = $keluar[$tglKey] ?? 0;
        }

        return [$labelsTanggal, $klinikBarMasuk, $klinikBarKeluar];
    }

    public function resetData()
    {
        $this->loadDefaultData();
    }
}
