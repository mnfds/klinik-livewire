<?php

namespace App\Livewire\Produkdanobat;

use App\Models\ProdukDanObat;
use Livewire\Component;

class UpdateProdukDanObat extends Component
{
    public $produkDanObatId;
    public $nama_dagang,$kode,$sediaan,$harga_dasar, $harga_bersih;
    public $stok,$expired_at,$batch,$lokasi,$supplier;
    public $diskon = 0;

    public $harga_dasar_show;
    public $harga_bersih_show;

    #[\Livewire\Attributes\On('editProdukDanObat')]
    public function editProdukDanObat($rowId): void
    {
        $this->produkDanObatId = $rowId;

        $produkObat = ProdukDanObat::findOrFail($rowId);

        $this->nama_dagang   =   $produkObat->nama_dagang;
        $this->kode          =   $produkObat->kode;
        $this->sediaan       =   $produkObat->sediaan;
        $this->harga_dasar   =   $produkObat->harga_dasar;
        $this->diskon        =   $produkObat->diskon ?? 0;
        $this->harga_bersih  =   $produkObat->harga_bersih ?? $produkObat->harga_dasar;
        $this->stok          =   $produkObat->stok;
        $this->expired_at    =   $produkObat->expired_at;
        $this->batch         =   $produkObat->batch;
        $this->lokasi        =   $produkObat->lokasi;
        $this->supplier      =   $produkObat->supplier;

        $this->harga_dasar_show = (int) preg_replace('/\D/', '', $this->harga_dasar);
        $this->harga_bersih_show = (int) preg_replace('/\D/', '', $this->harga_bersih);


        $this->dispatch('openModal');
    }

    public function updated($property)
    {
        if (in_array($property, ['harga_dasar', 'diskon'])) {
            $harga  = (int) $this->harga_dasar;
            $diskon = (int) $this->diskon;

            $this->harga_bersih = $harga - ($harga * $diskon / 100);
        }
    }

    public function update()
    {
        $this->validate([
            'nama_dagang'   => 'required|string|max:255',
            'kode'          => 'required|string|max:100',
            'sediaan'       => 'required|string|max:100',
            'harga_dasar'   => 'required|numeric|min:0',
            'diskon'        => 'nullable|min:0|max:100',
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
            'harga_dasar'   => $this->harga_dasar,
            'diskon'        => $this->diskon,
            'harga_bersih'  => $this->harga_bersih,
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
