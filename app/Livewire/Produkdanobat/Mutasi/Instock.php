<?php

namespace App\Livewire\Produkdanobat\Mutasi;

use App\Models\MutasiProdukDanObat;
use App\Models\ProdukDanObat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Instock extends Component
{
    public $produk_id, $jumlah, $diajukan_oleh, $catatan;
    public $tipe = 'masuk';

    public $produkobat = [];

    public function mount()
    {
        $this->produkobat = ProdukDanObat::all();
    }

    public function render()
    {
        return view('livewire.produkdanobat.mutasi.instock');
    }

    public function store()
    {
        $this->validate([
            'produk_id'   => 'required',
            'jumlah'   => 'required|numeric',
            'catatan'   => 'nullable',
        ]);
        if (! Gate::allows('akses', 'Persediaan Produk & Obat Masuk')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        MutasiProdukDanObat::create([
            'produk_id'   => $this->produk_id,
            'tipe' => $this->tipe,
            'jumlah'   => $this->jumlah,
            'catatan'   => $this->catatan,
            'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap,
        ]);
        
        $stokProduk = ProdukDanObat::findOrFail($this->produk_id);
        $stokTersisa = $stokProduk->stok + $this->jumlah;
        $stokProduk->update([
            'stok' => $stokTersisa,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data Produk/Obat berhasil Diperbarui.'
        ]);

        
        $this->reset();

        $this->dispatch('pg:eventRefresh-DishTable');

        $this->dispatch('closeoutstockModalProdukDanObat');

        return redirect()->route('produk-obat.data');
    }
}
