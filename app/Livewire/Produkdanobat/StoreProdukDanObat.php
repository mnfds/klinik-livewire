<?php

namespace App\Livewire\Produkdanobat;

use App\Models\ProdukDanObat;
use Livewire\Component;

class StoreProdukDanObat extends Component
{
    public $nama_dagang, $kode, $sediaan, $harga_jual, $stok;
    public $expired_at, $batch, $lokasi, $supplier;

    public function render()
    {
        return view('livewire.produkdanobat.store-produk-dan-obat');
    }

    public function store()
    {
        $this->validate([
            'nama_dagang' => 'required|string',
            'kode' => 'required|string|unique:produk_dan_obats,kode',
            'sediaan' => 'required|string',
            'harga_jual' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'expired_at' => 'nullable|date',
            'batch' => 'nullable|string',
            'lokasi' => 'nullable|string',
            'supplier' => 'nullable|string',
        ]);

        ProdukDanObat::create([
            'nama_dagang' => $this->nama_dagang,
            'kode' => $this->kode,
            'sediaan' => $this->sediaan,
            'harga_jual' => $this->harga_jual,
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

