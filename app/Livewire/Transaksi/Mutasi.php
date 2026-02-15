<?php

namespace App\Livewire\Transaksi;

use App\Models\TransaksiKlinik;
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

    public function render()
    {
        return view('livewire.transaksi.mutasi');
    }

    public function mount($id){
        $this->transaksi = TransaksiKlinik::with(['riwayatTransaksi'])->findOrFail($id);
        $this->bundling =  $this->transaksi->riwayatTransaksi->where('jenis_item', 'bundling')->values();
        $this->produk =  $this->transaksi->riwayatTransaksi->where('jenis_item', 'produk')->values();
        $this->layanan =  $this->transaksi->riwayatTransaksi->where('jenis_item', 'pelayanan')->values();
        $this->treatment =  $this->transaksi->riwayatTransaksi->where('jenis_item', 'treatment')->values();
        $this->obatNonRacik =  $this->transaksi->riwayatTransaksi->where('jenis_item', 'obat_non_racik')->values();
        $this->obatRacik =  $this->transaksi->riwayatTransaksi->where('jenis_item', 'obat_racik')->values();
        $this->produkTambahan =  $this->transaksi->riwayatTransaksi->where('jenis_item', 'produk_tambahan')->values();
    }
}
