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
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate   = now()->endOfMonth()->format('Y-m-d');
        $this->loadData();
    }

    public function tanggalDipilih()
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
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate   = now()->endOfMonth()->format('Y-m-d');
        $this->loadData();
    }

    private function loadData(): void
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        [$labelsTanggal, $rekapMasuk, $rekapKeluar, $rekapTable] = $this->hitungRekap($start, $end);

        $this->rekapHarian = $rekapTable;

        $this->totalMasuk  = collect($rekapMasuk)->sum();
        $this->totalKeluar = collect($rekapKeluar)->sum();
        $this->totalSisa   = $this->totalMasuk - $this->totalKeluar;

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
                ->selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(total_dibayarkan) as total')
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

        $labelsTanggal = [];
        $rekapMasuk    = [];
        $rekapKeluar   = [];
        $rekapTable    = [];

        foreach ($period as $index => $date) {
            $tglKey = $date->format('Y-m-d');

            $masuk = ($masukKlinik[$tglKey] ?? 0)
                   + ($masukApotik[$tglKey] ?? 0)
                   + ($masukLainnya[$tglKey] ?? 0);

            $keluarValue = $keluar[$tglKey] ?? 0;
            $sisa = $masuk - $keluarValue;

            $labelsTanggal[] = $date->format('d');
            $rekapMasuk[]    = $masuk;
            $rekapKeluar[]   = $keluarValue;

            $rekapTable[] = [
                'no'          => $index + 1,
                'tanggal'     => $date->translatedFormat('d F Y'),
                'tanggal_raw' => $date->format('Y-m-d'),
                'masuk'       => $masuk,
                'keluar'      => $keluarValue,
                'sisa'        => $sisa,
            ];
        }

        return [$labelsTanggal, $rekapMasuk, $rekapKeluar, $rekapTable];
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
            $apotik = TransaksiApotik::with(['riwayat.produk', 'riwayatBarang.barang'])
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

        $this->detailMasuk = [
            'klinik'  => $klinik,
            'apotik'  => $apotik,
            'lainnya' => $lainnya,
        ];

        // =====================
        // UANG KELUAR
        // =====================
        $keluar = collect();
        if ($this->tipe !== 'masuk') {
            $keluar = Uangkeluar::whereBetween('tanggal_pengajuan', [$start, $end])
                ->where('status', 'Disetujui')
                ->when($this->filterUnitUsaha, fn ($q) => $q->where('unit_usaha', $this->filterUnitUsaha))
                ->when($this->filterMetodePembayaran, fn ($q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
                ->when($this->filterJenisPengeluaran, fn ($q) => $q->where('jenis_pengeluaran', $this->filterJenisPengeluaran))
                ->get();
        }

        $this->detailKeluar = $keluar;

        // =====================
        // TOTAL
        // =====================
        $totalKlinik  = $klinik->sum('total_tagihan_bersih');
        $totalApotik  = $apotik->sum('total_harga');
        $totalLainnya = $lainnya->sum('total_dibayarkan');

        $this->detailTotalMasuk  = $totalKlinik + $totalApotik + $totalLainnya;
        $this->detailTotalKeluar = $keluar->sum('jumlah_uang');
        $this->detailSisa        = $this->detailTotalMasuk - $this->detailTotalKeluar;

        $this->dispatch('open-detail-modal');
    }

    public function unduh($tanggal)
    {
        $start = Carbon::parse($tanggal)->startOfDay();
        $end   = Carbon::parse($tanggal)->endOfDay();

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
            $apotik = TransaksiApotik::with(['riwayat.produk', 'riwayatBarang.barang', 'pasien'])
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

        $totalKlinik  = $klinik->sum('total_tagihan_bersih');
        $totalApotik  = $apotik->sum('total_harga');
        $totalLainnya = $lainnya->sum('total_dibayarkan');

        $totalMasuk  = $totalKlinik + $totalApotik + $totalLainnya;
        $totalKeluar = $keluar->sum('jumlah_uang');
        $sisa        = $totalMasuk - $totalKeluar;

        $pdf = Pdf::loadView('pdf.rekap-harian', [
            'tanggal'      => $tanggal,
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
            "rekap-harian-{$tanggal}.pdf"
        );
    }
}
