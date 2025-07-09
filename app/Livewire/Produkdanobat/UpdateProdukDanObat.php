<?php

namespace App\Livewire\Produkdanobat;

use App\Models\ProdukDanObat;
use Livewire\Component;

class UpdateProdukDanObat extends Component
{
    public $produkDanObatId;
    public $nama_dagang,$kode,$sediaan,$harga_jual;
    public $stok,$expired_at,$batch,$lokasi,$supplier;

    #[\Livewire\Attributes\On('editProdukDanObat')]
    public function editProdukDanObat($rowId): void
    {
        $this->produkDanObatId = $rowId;

        $produkObat = ProdukDanObat::findOrFail($rowId);

        $this->nama_dagang   =   $produkObat->nama_dagang;
        $this->kode   =   $produkObat->kode;
        $this->sediaan   =   $produkObat->sediaan;
        $this->harga_jual   =   $produkObat->harga_jual;
        $this->stok   =   $produkObat->stok;
        $this->expired_at   =   $produkObat->expired_at;
        $this->batch   =   $produkObat->batch;
        $this->lokasi   =   $produkObat->lokasi;
        $this->supplier   =   $produkObat->supplier;

        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate([
            'nama_dagang'   => 'required|string|max:255',
            'kode'          => 'required|string|max:100',
            'sediaan'       => 'required|string|max:100',
            'harga_jual'    => 'required|numeric|min:0',
            'stok'          => 'required|integer|min:0',
            'expired_at'    => 'nullable|date',
            'batch'         => 'nullable|string|max:100',
            'lokasi'        => 'nullable|string|max:100',
            'supplier'      => 'nullable|string|max:255',
        ]);

        ProdukDanObat::where('id', $this->produkDanObatId)->update([
            'nama_dagang'   => $this->nama_dagang,
            'kode'          => $this->kode,
            'sediaan'       => $this->sediaan,
            'harga_jual'    => $this->harga_jual,
            'stok'          => $this->stok,
            'expired_at'    => $this->expired_at,
            'batch'         => $this->batch,
            'lokasi'        => $this->lokasi,
            'supplier'      => $this->supplier,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data produk berhasil diperbarui.'
        ]);

        $this->dispatch('closeModal');

        $this->reset();

        return redirect()->route('produk-obat.data'); // pastikan route ini benar
    }

    public function render()
    {
        return view('livewire.produkdanobat.update-produk-dan-obat');
    }
}
