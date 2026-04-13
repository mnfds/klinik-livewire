<?php

namespace App\Livewire\Transaksi;

use App\Models\PasienTerdaftar;
// use App\Models\TransaksiKlinik;
use Livewire\Component;

class Mutasi extends Component
{
    public $transaksi;
    public $bundling;
    public $produk;
    public $layanan;
    public $treatment;
    public $obatNonRacik;
    public $obatRacik;
    public $produkTambahan;
    public $barangTambahan;
    public $bundlingUsageTreatment;
    public $bundlingUsagePelayanan;
    public $bundlingUsageProduk;

    public function render()
    {
        return view('livewire.transaksi.mutasi');
    }

    public function mount($id)
    {
        $pasien = PasienTerdaftar::with([
            'rekamMedis.transaksi',
            'rekamMedis.treatmentBundlingUsages.bundling',
            'rekamMedis.treatmentBundlingUsages.treatment',
            'rekamMedis.pelayananBundlingUsages.bundling',
            'rekamMedis.pelayananBundlingUsages.pelayanan',
            'rekamMedis.produkBundlingUsages.bundling',
            'rekamMedis.produkBundlingUsages.produk',
        ])->findOrFail($id);

        $rekamMedis      = $pasien->rekamMedis;
        $this->transaksi = $rekamMedis->transaksi;

        $riwayat = $this->transaksi->riwayatTransaksi()->get();

        $this->bundling       = $riwayat->where('jenis_item', 'bundling')->values();
        $this->produk         = $riwayat->where('jenis_item', 'produk')->values();
        $this->layanan        = $riwayat->where('jenis_item', 'pelayanan')->values();
        $this->treatment      = $riwayat->where('jenis_item', 'treatment')->values();
        $this->obatNonRacik   = $riwayat->where('jenis_item', 'obat_non_racik')->values();
        $this->obatRacik      = $riwayat->where('jenis_item', 'obat_racik')->values();
        $this->produkTambahan = $riwayat->where('jenis_item', 'produk_tambahan')->values();
        $this->barangTambahan = $riwayat->where('jenis_item', 'barang_tambahan')->values();

        $this->bundlingUsageTreatment = $rekamMedis->treatmentBundlingUsages->where('is_pembelian_baru', false)->values();
        $this->bundlingUsagePelayanan = $rekamMedis->pelayananBundlingUsages->where('is_pembelian_baru', false)->values();
        $this->bundlingUsageProduk    = $rekamMedis->produkBundlingUsages->where('is_pembelian_baru', false)->values();
    }
}
