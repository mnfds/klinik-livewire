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
    public $detailMasuk = [];
    public $detailKeluar = [];
    public $detailTotalMasuk = 0;
    public $detailTotalKeluar = 0;
    public $detailSisa = 0;

    protected int $startYear = 2024;
    protected int $endYear = 2035;

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
                    ->sum('total_tagihan');
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

    // DETAIL — versi dasar, akan disempurnakan
    public function showDetail($tahun)
    {
        $this->detailTahun = $tahun;

        $start = Carbon::create((int) $tahun, 1, 1)->startOfYear();
        $end   = Carbon::create((int) $tahun, 12, 31)->endOfYear();

        [$klinik, $apotik, $lainnya, $keluar] = $this->ambilData($start, $end);

        $this->detailMasuk = [
            'klinik'  => $klinik,
            'apotik'  => $apotik,
            'lainnya' => $lainnya,
        ];
        $this->detailKeluar = $keluar;

        $totalKlinik  = $klinik->sum('total_tagihan_bersih');
        $totalApotik  = $apotik->sum('total_harga');
        $totalLainnya = $lainnya->sum('total_tagihan');

        $this->detailTotalMasuk  = $totalKlinik + $totalApotik + $totalLainnya;
        $this->detailTotalKeluar = $keluar->sum('jumlah_uang');
        $this->detailSisa        = $this->detailTotalMasuk - $this->detailTotalKeluar;

        $this->dispatch('open-detail-modal-tahunan');
    }

    // UNDUH — versi dasar, akan disempurnakan
    public function unduh($tahun)
    {
        $start = Carbon::create((int) $tahun, 1, 1)->startOfYear();
        $end   = Carbon::create((int) $tahun, 12, 31)->endOfYear();

        [$klinik, $apotik, $lainnya, $keluar] = $this->ambilData($start, $end, withPasien: true);

        $totalKlinik  = $klinik->sum('total_tagihan_bersih');
        $totalApotik  = $apotik->sum('total_harga');
        $totalLainnya = $lainnya->sum('total_tagihan');

        $totalMasuk  = $totalKlinik + $totalApotik + $totalLainnya;
        $totalKeluar = $keluar->sum('jumlah_uang');
        $sisa        = $totalMasuk - $totalKeluar;

        $pdf = Pdf::loadView('pdf.rekap-tahunan', [
            'tahun'        => $tahun,
            'klinik'       => $klinik,
            'apotik'       => $apotik,
            'lainnya'      => $lainnya,
            'keluar'       => $keluar,
            'totalKlinik'  => $totalKlinik,
            'totalApotik'  => $totalApotik,
            'totalLainnya' => $totalLainnya,
            'totalMasuk'   => $totalMasuk,
            'totalKeluar'  => $totalKeluar,
            'sisa'         => $sisa,
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