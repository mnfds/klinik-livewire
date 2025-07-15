<?php

namespace App\Livewire\Produkdanobat;

use Livewire\Component;
use App\Models\ProdukDanObat;

class Store extends Component
{
    public $nama_dagang, $kode, $sediaan, $harga_dasar, $stok;
    public $expired_at, $batch, $lokasi, $supplier, $harga_bersih;
    public $diskon = 0;

    public function render()
    {
        return view('livewire.produkdanobat.store');
    }

    public function store()
    {
        $this->validate([
            'nama_dagang' => 'required|string',
            'kode' => 'required|string|unique:produk_dan_obats,kode',
            'sediaan' => 'required|string',
            'harga_dasar' => 'required|integer|min:0',
            'diskon' => 'nullable|min:0|max:100',
            'stok' => 'required|integer|min:0',
            'expired_at' => 'nullable|date',
            'batch' => 'nullable|string',
            'lokasi' => 'nullable|string',
            'supplier' => 'nullable|string',
        ]);

        // Hitung harga bersih
        $harga = (float) $this->harga_dasar;
        $diskon = (float) $this->diskon;

        $diskonNominal = ($harga * $diskon) / 100;
        $this->harga_bersih = $harga - $diskonNominal;


        ProdukDanObat::create([
            'nama_dagang' => $this->nama_dagang,
            'kode' => $this->kode,
            'sediaan' => $this->sediaan,
            'harga_dasar' => $this->harga_dasar,
            'diskon' => $this->diskon,
            'harga_bersih' => $this->harga_bersih,
            'stok' => $this->stok,
            'expired_at' => $this->expired_at,
            'batch' => $this->batch,
            'lokasi' => $this->lokasi,
            'supplier' => $this->supplier,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Produk/Obat berhasil ditambahkan.'
        ]);

        $this->dispatch('closeStoreModal');

        $this->reset();

        return redirect()->route('produk-obat.data');
    }
}
