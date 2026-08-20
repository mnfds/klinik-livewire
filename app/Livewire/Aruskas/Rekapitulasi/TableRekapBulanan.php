<?php

namespace App\Livewire\Aruskas\Rekapitulasi;

use App\Models\TransaksiKlinik;
use App\Models\TransaksiApotik;
use App\Models\Pendapatanlainnya;
use App\Models\Uangkeluar;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;

class TableRekapBulanan extends Component
{
    public $tahun;

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
    public $rekapBulanan = [];
    public $totalMasuk = 0;
    public $totalKeluar = 0;
    public $totalSisa = 0;

    // Detail
    public $detailBulan;
    public $detailLabelBulan;
    public $detailPerHari = [];
    public $detailTotalMasuk = 0;
    public $detailTotalKeluar = 0;
    public $detailSisa = 0;

    protected array $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function render()
    {
        return view('livewire.aruskas.rekapitulasi.table-rekap-bulanan');
    }

    public function mount()
    {
        $this->tahun = now()->year;
        $this->loadData();
    }

    public function tahunDipilih()
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
        $this->tahun = now()->year;
        $this->loadData();
    }

    private function loadData(): void
    {
        $year = (int) $this->tahun;
        $start = Carbon::create($year, 1, 1)->startOfDay();
        $end   = Carbon::create($year, 12, 31)->endOfDay();

        // ===== AMBIL DATA PER BULAN =====
        $masukKlinik = collect();
        $masukApotik = collect();

        if ($this->tipe !== 'keluar' && ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Klinik')) {
            $masukKlinik = TransaksiKlinik::whereBetween('tanggal_transaksi', [$start, $end])
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->selectRaw('MONTH(tanggal_transaksi) as bulan, SUM(total_tagihan_bersih) as total')
                ->groupBy('bulan')
                ->pluck('total', 'bulan');
        }

        if ($this->tipe !== 'keluar' && ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Apotik')) {
            $masukApotik = TransaksiApotik::whereBetween('tanggal', [$start, $end])
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->selectRaw('MONTH(tanggal) as bulan, SUM(total_harga) as total')
                ->groupBy('bulan')
                ->pluck('total', 'bulan');
        }

        $masukLainnya = collect();
        if ($this->tipe !== 'keluar') {
            $masukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
                ->whereIn('status', ['lunas', 'belum lunas'])
                ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->selectRaw('MONTH(tanggal_transaksi) as bulan, SUM(total_tagihan) as total')
                ->groupBy('bulan')
                ->pluck('total', 'bulan');
        }

        $keluar = collect();
        if ($this->tipe !== 'masuk') {
            $keluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
                ->where('status', 'Disetujui')
                ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->when($this->filterJenisPengeluaran, fn ($q) => $q->where('jenis_pengeluaran', $this->filterJenisPengeluaran))
                ->selectRaw('MONTH(tanggal_pengajuan) as bulan, SUM(jumlah_uang) as total')
                ->groupBy('bulan')
                ->pluck('total', 'bulan');
        }

        $rekapTable = [];
        $totalMasukSemua = 0;
        $totalKeluarSemua = 0;

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $masuk = ($masukKlinik[$bulan] ?? 0)
                   + ($masukApotik[$bulan] ?? 0)
                   + ($masukLainnya[$bulan] ?? 0);

            $keluarValue = $keluar[$bulan] ?? 0;
            $sisa = $masuk - $keluarValue;

            $totalMasukSemua  += $masuk;
            $totalKeluarSemua += $keluarValue;

            $rekapTable[] = [
                'no'          => $bulan,
                'bulan'       => $this->namaBulan[$bulan] . ' ' . $year,
                'bulan_raw'   => $bulan,
                'masuk'       => $masuk,
                'keluar'      => $keluarValue,
                'sisa'        => $sisa,
            ];
        }

        $this->rekapBulanan = $rekapTable;
        $this->totalMasuk   = $totalMasukSemua;
        $this->totalKeluar  = $totalKeluarSemua;
        $this->totalSisa    = $totalMasukSemua - $totalKeluarSemua;
    }

    // DETAIL
    public function showDetail($bulan)
    {
        $year = (int) $this->tahun;
        $this->detailBulan = $bulan;
        $this->detailLabelBulan = $this->namaBulan[$bulan] . ' ' . $year;

        $start = Carbon::create($year, $bulan, 1)->startOfMonth();
        $end   = Carbon::create($year, $bulan, 1)->endOfMonth();

        $this->detailPerHari = $this->hitungRekapHarianDalamBulan($start, $end);

        $this->detailTotalMasuk  = collect($this->detailPerHari)->sum('masuk');
        $this->detailTotalKeluar = collect($this->detailPerHari)->sum('keluar');
        $this->detailSisa        = $this->detailTotalMasuk - $this->detailTotalKeluar;

        $this->dispatch('open-detail-modal-bulanan');
    }

    private function hitungRekapHarianDalamBulan(Carbon $start, Carbon $end): array
    {
        $period = CarbonPeriod::create($start, $end);

        $masukKlinik = collect();
        $masukApotik = collect();

        if ($this->tipe !== 'keluar' && ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Klinik')) {
            $masukKlinik = TransaksiKlinik::whereBetween('tanggal_transaksi', [$start, $end])
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(total_tagihan_bersih) as total')
                ->groupBy('tanggal')
                ->pluck('total', 'tanggal');
        }

        if ($this->tipe !== 'keluar' && ($this->filterUnitUsaha === '' || $this->filterUnitUsaha === 'Apotik')) {
            $masukApotik = TransaksiApotik::whereBetween('tanggal', [$start, $end])
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->get()
                ->groupBy(fn ($item) => Carbon::parse($item->tanggal)->format('Y-m-d'))
                ->map(fn ($items) => $items->sum('total_harga'));
        }

        $masukLainnya = collect();
        if ($this->tipe !== 'keluar') {
            $masukLainnya = Pendapatanlainnya::whereBetween('tanggal_transaksi', [$start, $end])
                ->whereIn('status', ['lunas', 'belum lunas'])
                ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(total_tagihan) as total')
                ->groupBy('tanggal')
                ->pluck('total', 'tanggal');
        }

        $keluar = collect();
        if ($this->tipe !== 'masuk') {
            $keluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
                ->where('status', 'Disetujui')
                ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->when($this->filterJenisPengeluaran, fn ($q) => $q->where('jenis_pengeluaran', $this->filterJenisPengeluaran))
                ->selectRaw('DATE(tanggal_pengajuan) as tanggal, SUM(jumlah_uang) as total')
                ->groupBy('tanggal')
                ->pluck('total', 'tanggal');
        }

        $rekapHarian = [];

        foreach ($period as $index => $date) {
            $tglKey = $date->format('Y-m-d');

            $masuk = ($masukKlinik[$tglKey] ?? 0)
                + ($masukApotik[$tglKey] ?? 0)
                + ($masukLainnya[$tglKey] ?? 0);

            $keluarValue = $keluar[$tglKey] ?? 0;

            $rekapHarian[] = [
                'no'      => $index + 1,
                'tanggal' => $date->translatedFormat('d F Y'),
                'masuk'   => $masuk,
                'keluar'  => $keluarValue,
                'sisa'    => $masuk - $keluarValue,
            ];
        }

        return $rekapHarian;
    }

    public function unduh($bulan)
    {
        $year = (int) $this->tahun;
        $labelBulan = $this->namaBulan[$bulan] . ' ' . $year;

        $start = Carbon::create($year, $bulan, 1)->startOfMonth();
        $end   = Carbon::create($year, $bulan, 1)->endOfMonth();

        [$klinik, $apotik, $lainnya, $keluar] = $this->ambilData($start, $end, withPasien: true);

        $totalKlinik  = $klinik->sum('total_tagihan_bersih');
        $totalApotik  = $apotik->sum('total_harga');
        $totalLainnya = $lainnya->sum('total_tagihan');

        $totalMasuk  = $totalKlinik + $totalApotik + $totalLainnya;
        $totalKeluar = $keluar->sum('jumlah_uang');
        $sisa        = $totalMasuk - $totalKeluar;

        $pdf = Pdf::loadView('pdf.rekap-bulanan', [
            'labelBulan'   => $labelBulan,
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
            "rekap-bulanan-{$year}-{$bulan}.pdf"
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
