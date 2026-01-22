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

    public $rekapPieMasuk = 0;
    public $rekapPieKeluar = 0;

    public function render()
    {
        return view('livewire.aruskas.rekapitulasi.grafik-harian');
    }
    
    public function mount()
    {
        $this->loadDefaultData();
    }

    public function tanggalDipilih()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        $this->hitungRekapPieHarian($start, $end);
        [$labelsTanggal, $rekapBarMasuk, $rekapBarKeluar] = $this->hitungRekapBarHarian($start, $end);

        // ===== KIRIM KE JS =====
        $this->dispatch('update-rekap-harian-pie', [
            'rekapHarianPieMasuk'  => $this->rekapPieMasuk,
            'rekapHarianPieKeluar' => $this->rekapPieKeluar,
        ]);

        $this->dispatch('update-rekap-harian-bar', [
            'labelstanggal'  => $labelsTanggal,
            'rekapHarianBarMasuk'  => $rekapBarMasuk,
            'rekapHarianBarKeluar' => $rekapBarKeluar,
        ]);
    }

    private function loadDefaultData()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate   = now()->endOfMonth()->format('Y-m-d');

        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        $this->hitungRekapPieHarian($start, $end);
        [$labelsTanggal, $rekapBarMasuk, $rekapBarKeluar] = $this->hitungRekapBarHarian($start, $end);

        // ===== KIRIM KE JS =====
        $this->dispatch('update-rekap-harian-pie', [
            'rekapHarianPieMasuk'  => $this->rekapPieMasuk,
            'rekapHarianPieKeluar' => $this->rekapPieKeluar,
        ]);
        
        $this->dispatch('update-rekap-harian-bar', [
            'labelstanggal'  => $labelsTanggal,
            'rekapHarianBarMasuk'  => $rekapBarMasuk,
            'rekapHarianBarKeluar' => $rekapBarKeluar,
        ]);
    }

    private function hitungRekapPieHarian(Carbon $start, Carbon $end)
    {
        $totalMasukKlinik = TransaksiKlinik::whereBetween('tanggal_transaksi', [$start, $end])
            ->sum('total_tagihan_bersih');

        $totalKeluarKlinik = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
            ->where('unit_usaha','Klinik')
            ->where('status','Disetujui')
            ->sum('jumlah_uang');

        $totalMasukApotik = TransaksiApotik::whereBetween('tanggal', [$start, $end])
            ->sum('total_harga');

        $totalKeluarApotik = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
            ->where('unit_usaha','Apotik')
            ->where('status','Disetujui')
            ->sum('jumlah_uang');

        $totalMasukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
            ->whereIn('unit_usaha',['Klinik', 'Apotik', 'Sewa Multifunction', 'Coffeshop', 'Dll'])
            ->whereIn('status',['lunas', 'belum lunas'])
            ->sum('total_tagihan');

        $totalKeluarLainnya = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
            ->where('unit_usaha','Lainnya')
            ->where('status','Disetujui')
            ->sum('jumlah_uang');

        $this->rekapPieMasuk =$totalMasukKlinik + $totalMasukApotik + $totalMasukLainnya;
        $this->rekapPieKeluar =$totalKeluarKlinik + $totalKeluarApotik + $totalKeluarLainnya;
    }

    private function hitungRekapBarHarian(Carbon $start, Carbon $end)
    {
        $period = CarbonPeriod::create($start, $end);

        // ===== AMBIL DATA PER TANGGAL =====
        $masukKlinik = TransaksiKlinik::whereBetween('tanggal_transaksi', [$start, $end])
            ->selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(total_tagihan_bersih) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $masukApotik = TransaksiApotik::whereBetween('tanggal', [$start, $end])
            ->selectRaw('DATE(tanggal) as tanggal, SUM(total_harga) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $masukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
            ->whereIn('unit_usaha', ['Klinik','Apotik','Sewa Multifunction','Coffeshop','Dll'])
            ->whereIn('status', ['lunas','belum lunas'])
            ->selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(total_tagihan) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $keluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
            ->where('status', 'Disetujui')
            ->selectRaw('DATE(tanggal_pengajuan) as tanggal, SUM(jumlah_uang) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        // ===== SUSUN DATA UNTUK CHART =====
        $labelsTanggal = [];
        $rekapBarMasuk = [];
        $rekapBarKeluar = [];

        foreach ($period as $date) {
            $tglKey = $date->format('Y-m-d');

            $labelsTanggal[] = $date->format('d'); // tampilkan tanggal saja
            $rekapBarMasuk[] =($masukKlinik[$tglKey] ?? 0) + ($masukApotik[$tglKey] ?? 0) + ($masukLainnya[$tglKey] ?? 0);
            $rekapBarKeluar[] = $keluar[$tglKey] ?? 0;
        }

        return [$labelsTanggal, $rekapBarMasuk, $rekapBarKeluar];
    }

    public function resetData()
    {
        $this->loadDefaultData();
    }
}
