<?php

namespace App\Livewire\Aruskas\Rekapitulasi;

use App\Models\TransaksiKlinik;
use App\Models\TransaksiApotik;
use App\Models\Pendapatanlainnya;
use App\Models\Uangkeluar;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Component;

class TableRekapTahunan extends Component
{
    // Filter aktif
    public string $tipe = '';
    public string $filterUnitUsaha = '';
    public string $filterMetodePembayaran = '';
    public string $filterJenisPengeluaran = '';

    // Draft (dipakai di dalam modal)
    public string $draftTipe = '';
    public string $draftUnitUsaha = '';
    public string $draftMetodePembayaran = '';
    public string $draftJenisPengeluaran = '';

    // Table Rekap
    public $rekapTahunan = [];
    public $totalMasuk = 0;
    public $totalKeluar = 0;
    public $totalSisa = 0;

    // Detail
    public $detailTahun;
    public $detailLabelTahun;
    public $detailPerBulan = [];
    public $detailTotalMasuk = 0;
    public $detailTotalKeluar = 0;
    public $detailSisa = 0;

    protected int $startYear = 2024;
    protected int $endYear = 2035;
    protected array $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    
    public function render()
    {
        return view('livewire.aruskas.rekapitulasi.table-rekap-tahunan');
    }

    public function mount()
    {
        $this->loadData();
    }

    public function openFilterModal(): void
    {
        $this->draftTipe = $this->tipe;
        $this->draftUnitUsaha = $this->filterUnitUsaha;
        $this->draftMetodePembayaran = $this->filterMetodePembayaran;
        $this->draftJenisPengeluaran = $this->filterJenisPengeluaran;
    }

    public function filter(): void
    {
        $this->tipe = $this->draftTipe;
        $this->filterUnitUsaha = $this->draftUnitUsaha;
        $this->filterMetodePembayaran = $this->draftMetodePembayaran;
        $this->filterJenisPengeluaran = $this->tipe === 'keluar' ? $this->draftJenisPengeluaran : '';

        $this->loadData();
    }

    public function resetAll(): void
    {
        $this->reset(['tipe', 'filterUnitUsaha', 'filterMetodePembayaran', 'filterJenisPengeluaran',
                       'draftTipe', 'draftUnitUsaha', 'draftMetodePembayaran', 'draftJenisPengeluaran']);
        $this->loadData();
    }

    private function loadData(): void
    {
        $rekapTable = [];
        $totalMasukSemua  = 0;
        $totalKeluarSemua = 0;

        for ($year = $this->startYear; $year <= $this->endYear; $year++) {
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end   = Carbon::create($year, 12, 31)->endOfDay();

            $totalKlinik = 0;
            $totalApotik = 0;

            if ($this->tipe !== 'keluar' && ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Klinik')) {
                $totalKlinik = TransaksiKlinik::whereBetween('tanggal_transaksi', [$start, $end])
                    ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                    ->sum('total_tagihan_bersih');
            }

            if ($this->tipe !== 'keluar' && ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Apotik')) {
                $totalApotik = TransaksiApotik::whereBetween('tanggal', [$start, $end])
                    ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                    ->sum('total_harga');
            }

            $totalLainnya = 0;
            if ($this->tipe !== 'keluar') {
                $totalLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
                    ->whereIn('status', ['lunas', 'belum lunas'])
                    ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
                    ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                    ->sum('total_dibayarkan');
            }

            $totalKeluar = 0;
            if ($this->tipe !== 'masuk') {
                $totalKeluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
                    ->where('status', 'Disetujui')
                    ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
                    ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                    ->when($this->filterJenisPengeluaran, fn ($q) => $q->where('jenis_pengeluaran', $this->filterJenisPengeluaran))
                    ->sum('jumlah_uang');
            }

            $masuk = $totalKlinik + $totalApotik + $totalLainnya;
            $sisa  = $masuk - $totalKeluar;

            $totalMasukSemua  += $masuk;
            $totalKeluarSemua += $totalKeluar;

            $rekapTable[] = [
                'no'        => $year - $this->startYear + 1,
                'tahun'     => (string) $year,
                'tahun_raw' => $year,
                'masuk'     => $masuk,
                'keluar'    => $totalKeluar,
                'sisa'      => $sisa,
            ];
        }

        $this->rekapTahunan = $rekapTable;
        $this->totalMasuk   = $totalMasukSemua;
        $this->totalKeluar  = $totalKeluarSemua;
        $this->totalSisa    = $totalMasukSemua - $totalKeluarSemua;
    }

    public function showDetailTahun($tahun)
    {
        $this->detailTahun = $tahun;
        $this->detailLabelTahun = "Tahun {$tahun}";

        $start = Carbon::create($tahun, 1, 1)->startOfYear();
        $end   = Carbon::create($tahun, 12, 31)->endOfYear();

        $this->detailPerBulan = $this->hitungRekapBulananDalamTahun(
            $start,
            $end,
            $tahun
        );

        $this->detailTotalMasuk = collect($this->detailPerBulan)->sum('masuk');
        $this->detailTotalKeluar = collect($this->detailPerBulan)->sum('keluar');
        $this->detailSisa = $this->detailTotalMasuk - $this->detailTotalKeluar;

        $this->dispatch('open-detail-modal-tahunan');
    }

    private function hitungRekapBulananDalamTahun(Carbon $start, Carbon $end, int $tahun): array {

        $masukKlinik = collect();
        $masukApotik = collect();
        $masukLainnya = collect();
        $keluar = collect();

        if ($this->tipe !== 'keluar' && ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Klinik')) {
            $masukKlinik = TransaksiKlinik::whereBetween(
                    'tanggal_transaksi',
                    [$start, $end]
                )
                ->when(
                    $this->filterMetodePembayaran,
                    fn ($q) => $q->where(
                        'metode_pembayaran',
                        $this->filterMetodePembayaran
                    )
                )
                ->selectRaw(
                    'MONTH(tanggal_transaksi) bulan,
                    SUM(total_tagihan_bersih) total'
                )
                ->groupBy('bulan')
                ->pluck('total', 'bulan');
        }

        if ($this->tipe !== 'keluar' && ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Apotik')) {
            $masukApotik = TransaksiApotik::whereBetween(
                    'tanggal',
                    [$start, $end]
                )
                ->when(
                    $this->filterMetodePembayaran,
                    fn ($q) => $q->where(
                        'metode_pembayaran',
                        $this->filterMetodePembayaran
                    )
                )
                ->selectRaw(
                    'MONTH(tanggal) bulan,
                    SUM(total_harga) total'
                )
                ->groupBy('bulan')
                ->pluck('total', 'bulan');
        }

        if ($this->tipe !== 'keluar') {
            $masukLainnya = Pendapatanlainnya::whereBetween(
                    'tanggal_transaksi',
                    [$start, $end]
                )
                ->whereIn('status', ['lunas', 'belum lunas'])
                ->when(
                    $this->filterUnitUsaha,
                    fn ($q) => $q->where(
                        'unit_usaha',
                        $this->filterUnitUsaha
                    )
                )
                ->when(
                    $this->filterMetodePembayaran,
                    fn ($q) => $q->where(
                        'metode_pembayaran',
                        $this->filterMetodePembayaran
                    )
                )
                ->selectRaw(
                    'MONTH(tanggal_transaksi) bulan,
                    SUM(total_dibayarkan) total'
                )
                ->groupBy('bulan')
                ->pluck('total', 'bulan');
        }

        if ($this->tipe !== 'masuk') {
            $keluar = Uangkeluar::whereBetween(
                    'tanggal_pengajuan',
                    [$start, $end]
                )
                ->where('status', 'Disetujui')
                ->when(
                    $this->filterUnitUsaha,
                    fn ($q) => $q->where(
                        'unit_usaha',
                        $this->filterUnitUsaha
                    )
                )
                ->when(
                    $this->filterMetodePembayaran,
                    fn ($q) => $q->where(
                        'metode_pembayaran',
                        $this->filterMetodePembayaran
                    )
                )
                ->when(
                    $this->filterJenisPengeluaran,
                    fn ($q) => $q->where(
                        'jenis_pengeluaran',
                        $this->filterJenisPengeluaran
                    )
                )
                ->selectRaw(
                    'MONTH(tanggal_pengajuan) bulan,
                    SUM(jumlah_uang) total'
                )
                ->groupBy('bulan')
                ->pluck('total', 'bulan');
        }

        $hasil = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {

            $masuk =
                ($masukKlinik[$bulan] ?? 0)
                + ($masukApotik[$bulan] ?? 0)
                + ($masukLainnya[$bulan] ?? 0);

            $keluarValue = $keluar[$bulan] ?? 0;

            $hasil[] = [
                'no' => $bulan,
                'bulan' => $this->namaBulan[$bulan],
                'masuk' => $masuk,
                'keluar' => $keluarValue,
                'sisa' => $masuk - $keluarValue,
            ];
        }

        return $hasil;
    }

    public function unduhTahun($tahun)
    {
        $start = Carbon::create($tahun, 1, 1)->startOfYear();
        $end   = Carbon::create($tahun, 12, 31)->endOfYear();

        $detailPerBulan = $this->hitungRekapBulananDalamTahun(
            $start,
            $end,
            $tahun
        );

        $totalMasuk = collect($detailPerBulan)->sum('masuk');
        $totalKeluar = collect($detailPerBulan)->sum('keluar');
        $sisa = $totalMasuk - $totalKeluar;

        $pdf = Pdf::loadView('pdf.rekap-tahunan', [
            'tahun' => $tahun,
            'detailPerBulan' => $detailPerBulan,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'sisa' => $sisa,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "rekap-tahunan-{$tahun}.pdf"
        );
    }

    private function ambilData(Carbon $start, Carbon $end, bool $withPasien = false): array
    {
        $klinik = collect();
        if ($this->tipe !== 'keluar' && ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Klinik')) {
            $klinik = TransaksiKlinik::with([
                'rekammedis.rencanaProdukRM.produk',
                'rekammedis.rencanaLayananRM.pelayanan',
                'rekammedis.rencanaTreatmentRM.treatment',
                'rekammedis.rencanaBundlingRM.bundling',
                'rekammedis.obatFinal.obatNonRacikanFinals.produk',
                'rekammedis.obatFinal.obatRacikanFinals.bahanRacikanFinals.produk',
                'riwayatTransaksi',
                'rekammedis.treatmentBundlingUsages' => fn ($q) => $q->where('is_pembelian_baru', false),
                'rekammedis.treatmentBundlingUsages.bundling',
                'rekammedis.treatmentBundlingUsages.treatment',
                'rekammedis.pelayananBundlingUsages' => fn ($q) => $q->where('is_pembelian_baru', false),
                'rekammedis.pelayananBundlingUsages.bundling',
                'rekammedis.pelayananBundlingUsages.pelayanan',
                'rekammedis.produkBundlingUsages' => fn ($q) => $q->where('is_pembelian_baru', false),
                'rekammedis.produkBundlingUsages.bundling',
                'rekammedis.produkBundlingUsages.produk',
            ])
            ->whereBetween('tanggal_transaksi', [$start, $end])
            ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
            ->get();
        }

        $apotik = collect();
        if ($this->tipe !== 'keluar' && ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Apotik')) {
            $relations = $withPasien
                ? ['riwayat.produk', 'riwayatBarang.barang', 'pasien']
                : ['riwayat.produk', 'riwayatBarang.barang'];

            $apotik = TransaksiApotik::with($relations)
                ->whereBetween('tanggal', [$start, $end])
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->get();
        }

        $lainnya = collect();
        if ($this->tipe !== 'keluar') {
            $lainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
                ->whereIn('status', ['lunas', 'belum lunas'])
                ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->get();
        }

        $keluar = collect();
        if ($this->tipe !== 'masuk') {
            $keluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
                ->where('status', 'Disetujui')
                ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->when($this->filterJenisPengeluaran, fn ($q) => $q->where('jenis_pengeluaran', $this->filterJenisPengeluaran))
                ->get();
        }

        return [$klinik, $apotik, $lainnya, $keluar];
    }
}