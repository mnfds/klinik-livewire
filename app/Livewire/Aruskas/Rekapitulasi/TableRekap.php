<?php

namespace App\Livewire\Aruskas\Rekapitulasi;

use Livewire\Component;
use Carbon\CarbonPeriod;
use App\Models\Uangkeluar;
use Illuminate\Support\Carbon;
use App\Models\TransaksiApotik;
use App\Models\TransaksiKlinik;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Pendapatanlainnya;

class TableRekap extends Component
{
    public $startDate;
    public $endDate;

    // Table Rekap
    public $rekapHarian = [];
    public $totalMasuk = 0;
    public $totalKeluar = 0;
    public $totalSisa = 0;

    // Detail
    public $detailTanggal;
    public $detailMasuk = [];
    public $detailKeluar = [];
    public $detailTotalMasuk = 0;
    public $detailTotalKeluar = 0;
    public $detailSisa = 0;


    public function render()
    {
        return view('livewire.aruskas.rekapitulasi.table-rekap');
    }

    public function mount()
    {
        $this->loadDefaultData();
    }

    public function tanggalDipilih()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        [$labelsTanggal, $rekapMasuk, $rekapKeluar, $rekapTable] = $this->hitungRekap($start, $end);

        // Simpan untuk tabel
        $this->rekapHarian = $rekapTable;

        // Hitung total
        $this->totalMasuk = collect($rekapMasuk)->sum();
        $this->totalKeluar = collect($rekapKeluar)->sum();
        $this->totalSisa = $this->totalMasuk - $this->totalKeluar;

        $this->dispatch('update-rekap-harian-bar', [
            'labelstanggal' => $labelsTanggal,
            'rekapMasuk'    => $rekapMasuk,
            'rekapKeluar'   => $rekapKeluar,
        ]);
    }

    private function loadDefaultData()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate   = now()->endOfMonth()->format('Y-m-d');

        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        [$labelsTanggal, $rekapMasuk, $rekapKeluar, $rekapTable] = $this->hitungRekap($start, $end);

        // Simpan untuk tabel
        $this->rekapHarian = $rekapTable;

        // Hitung total
        $this->totalMasuk = collect($rekapMasuk)->sum();
        $this->totalKeluar = collect($rekapKeluar)->sum();
        $this->totalSisa = $this->totalMasuk - $this->totalKeluar;

        $this->dispatch('update-rekap-harian-bar', [
            'labelstanggal' => $labelsTanggal,
            'rekapMasuk'    => $rekapMasuk,
            'rekapKeluar'   => $rekapKeluar,
        ]);
    }

    private function hitungRekap(Carbon $start, Carbon $end)
    {
        $period = CarbonPeriod::create($start, $end);

        // ===== AMBIL DATA PER TANGGAL =====
        $masukKlinik = TransaksiKlinik::whereBetween('tanggal_transaksi', [$start, $end])
            ->selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(total_tagihan_bersih) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $masukApotik = TransaksiApotik::whereBetween('tanggal', [$start, $end])
            ->get()
            ->groupBy(fn($item) => Carbon::parse($item->tanggal)->format('Y-m-d'))
            ->map(fn($items) => $items->sum('total_harga'));

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

        $labelsTanggal = [];
        $rekapMasuk = [];
        $rekapKeluar = [];
        $rekapTable = [];

        foreach ($period as $index => $date) {
            $tglKey = $date->format('Y-m-d');

            $masuk = ($masukKlinik[$tglKey] ?? 0)
                    + ($masukApotik[$tglKey] ?? 0)
                    + ($masukLainnya[$tglKey] ?? 0);

            $keluarValue = $keluar[$tglKey] ?? 0;
            $sisa = $masuk - $keluarValue;

            // untuk chart
            $labelsTanggal[] = $date->format('d');
            $rekapMasuk[] = $masuk;
            $rekapKeluar[] = $keluarValue;

            // untuk tabel
            $rekapTable[] = [
                'no' => $index + 1,
                'tanggal' => $date->translatedFormat('d F Y'), // untuk tampilan
                'tanggal_raw' => $date->format('Y-m-d'), // untuk logic
                'masuk' => $masuk,
                'keluar' => $keluarValue,
                'sisa' => $sisa,
            ];
        }
        // dd($masukApotik);
        return [$labelsTanggal, $rekapMasuk, $rekapKeluar, $rekapTable];
    }

    public function resetData()
    {
        $this->loadDefaultData();
    }

    // DETAIL
    public function showDetail($tanggal)
    {
        $this->detailTanggal = $tanggal;

        $start = Carbon::parse($tanggal)->startOfDay();
        $end   = Carbon::parse($tanggal)->endOfDay();

        // =====================
        // UANG MASUK
        // =====================

        $klinik = TransaksiKlinik::with([
            'rekammedis.rencanaProdukRM.produk',
            'rekammedis.rencanaLayananRM.pelayanan',
            'rekammedis.rencanaTreatmentRM.treatment',
            'rekammedis.rencanaBundlingRM.bundling',
            'rekammedis.obatFinal.produk',
            'riwayatTransaksi',
        ])
        ->whereBetween('tanggal_transaksi', [$start, $end])
        ->get();

        $apotik = TransaksiApotik::with('riwayat.produk')
            ->whereBetween('tanggal', [$start, $end])
            ->get();

        $lainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
            ->whereIn('status', ['lunas','belum lunas'])
            ->get();

        $this->detailMasuk = [
            'klinik' => $klinik,
            'apotik' => $apotik,
            'lainnya' => $lainnya,
        ];

        // =====================
        // UANG KELUAR
        // =====================

        $keluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
            ->where('status', 'Disetujui')
            ->get();

        $this->detailKeluar = $keluar;

        // =====================
        // TOTAL
        // =====================

        $totalKlinik = $klinik->sum('total_tagihan_bersih');
        $totalApotik = $apotik->sum('total_harga');
        $totalLainnya = $lainnya->sum('total_tagihan');

        $this->detailTotalMasuk = $totalKlinik + $totalApotik + $totalLainnya;
        $this->detailTotalKeluar = $keluar->sum('jumlah_uang');
        $this->detailSisa = $this->detailTotalMasuk - $this->detailTotalKeluar;

        $this->dispatch('open-detail-modal');
    }

    public function unduh($tanggal)
    {
        $start = Carbon::parse($tanggal)->startOfDay();
        $end   = Carbon::parse($tanggal)->endOfDay();

        $klinik = TransaksiKlinik::with([
            'rekammedis.rencanaProdukRM.produk',
            'rekammedis.rencanaLayananRM.pelayanan',
            'rekammedis.rencanaTreatmentRM.treatment',
            'rekammedis.rencanaBundlingRM.bundling',
            'rekammedis.obatFinal.produk',
            'riwayatTransaksi',
        ])
        ->whereBetween('tanggal_transaksi', [$start, $end])
        ->get();

        $apotik = TransaksiApotik::with([
            'riwayat.produk',
            'pasien'
        ])
        ->whereBetween('tanggal', [$start, $end])
        ->get();

        $lainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
            ->whereIn('status', ['lunas','belum lunas'])
            ->get();

        $keluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
            ->where('status', 'Disetujui')
            ->get();

        $totalKlinik  = $klinik->sum('total_tagihan_bersih');
        $totalApotik  = $apotik->sum('total_harga');
        $totalLainnya = $lainnya->sum('total_tagihan');

        $totalMasuk  = $totalKlinik + $totalApotik + $totalLainnya;
        $totalKeluar = $keluar->sum('jumlah_uang');
        $sisa        = $totalMasuk - $totalKeluar;

        $pdf = Pdf::loadView('pdf.rekap-harian', [
            'tanggal' => $tanggal,
            'klinik' => $klinik,
            'apotik' => $apotik,
            'lainnya' => $lainnya,
            'keluar' => $keluar,
            'totalKlinik' => $totalKlinik,
            'totalApotik' => $totalApotik,
            'totalLainnya' => $totalLainnya,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'sisa' => $sisa,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "rekap-harian-{$tanggal}.pdf"
        );
    }

}
