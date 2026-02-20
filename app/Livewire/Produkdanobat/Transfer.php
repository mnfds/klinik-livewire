<?php

namespace App\Livewire\Produkdanobat;

use App\Models\BahanBaku;
use App\Models\MutasiProdukDanObat;
use Livewire\Component;
use App\Models\ProdukDanObat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Transfer extends Component
{
    public $produk_id, $jumlah, $catatan;
    public $produk = [];

    // BAHAN
    public $nama_bahan, $kode_bahan, $lokasi_bahan, $expired_at_bahan, $reminder_bahan, $keterangan_bahan, $satuan_besar_bahan, $satuan_kecil_bahan;
    public $tipe_bahan = 'masuk';
    public $stok_besar_bahan = 0;
    public $pengali_bahan = 0;
    public $stok_kecil_bahan = 0;

    public function mount()
    {
        $this->produk = ProdukDanObat::all();
    }

    public function render()
    {
        return view('livewire.produkdanobat.transfer');
    }

    public function store()
    {
        $this->validate([
            'produk_id'     => 'required',
            'jumlah'        => 'required|numeric',
            'catatan'       => 'nullable',
        ]);
        if (! Gate::allows('akses', 'Persediaan Bahan Baku Masuk')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        $produk = ProdukDanObat::lockForUpdate()->findOrFail($this->produk_id);
        // AMBIL DATA PRODUK UNTUK KE BAHAN
        $this->nama_bahan = $produk->nama_dagang;
        $this->kode_bahan = $produk->kode;
        $this->stok_besar_bahan = 0;
        $this->satuan_besar_bahan = $produk->sediaan;
        $this->pengali_bahan = 1;
        $this->stok_kecil_bahan = $this->jumlah;
        $this->satuan_kecil_bahan = $produk->sediaan;
        $this->lokasi_bahan = $produk->lokasi;
        $this->expired_at_bahan = $produk->expired_at;
        $this->reminder_bahan = $produk->reminder;
        $this->keterangan_bahan = null;

        // CREATE BAHAN
        $bahanbaku = BahanBaku::create([
           'nama'            => $this->nama_bahan,
           'kode'            => $this->kode_bahan,
           'stok_besar'      => (int) $this->stok_besar_bahan,
           'satuan_besar'    => $this->satuan_besar_bahan,
           'pengali'         => (int) $this->pengali_bahan,
           'stok_kecil'      => (int) $this->stok_kecil_bahan,
           'satuan_kecil'    => $this->satuan_kecil_bahan,
           'lokasi'          => $this->lokasi_bahan,
           'expired_at'      => $this->expired_at_bahan,
           'reminder'        => (int) $this->reminder_bahan,
           'keterangan'      => $this->keterangan_bahan,
        ]);
        // CREATE MUTASI BAHAN INSTOCK
        $bahanbaku->mutasibahan()->create([
            'bahan_bakus_id'     => $bahanbaku->id,
            'tipe'               => 'masuk',
            'jumlah'             => $bahanbaku->stok_kecil,
            'satuan'             => $bahanbaku->satuan_kecil,
            'diajukan_oleh'      => Auth::user()->biodata?->nama_lengkap,
            'catatan'            => $this->catatan . ' (Ditransfer Persediaan Dari Apotik)',
        ]);
        // CREATE MUTASI PRODUK OUTSTOCK
        MutasiProdukDanObat::create([
            'produk_id'   => $this->produk_id,
            'tipe' => 'keluar',
            'jumlah'   => $this->jumlah,
            'catatan'   => $this->catatan . ' (Transfer Persediaan Ke Poli)',
            'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap,
        ]);

        $stokProduk = ProdukDanObat::findOrFail($this->produk_id);
        $stokTersisa = $stokProduk->stok - $this->jumlah;
        $stokProduk->update([
            'stok' => $stokTersisa,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data Produk/Obat berhasil Diperbarui.'
        ]);

        $this->reset();

        $this->dispatch('closetransferModal');

        return redirect()->route('produk-obat.data');
    }
}
