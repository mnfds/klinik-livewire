<?php

namespace App\Livewire\Produkdanobat;

use Livewire\Component;
use App\Models\ProdukDanObat;
use Illuminate\Support\Facades\Gate;

class Update extends Component
{
    public $produkDanObatId;
    public $nama_dagang,$golongan,$kode,$sediaan,$harga_dasar, $harga_bersih;
    public $stok,$expired_at,$reminder,$batch,$lokasi,$supplier;
    public $diskon = 0;
    public $potongan = 0;

    public $potongan_show;
    public $harga_dasar_show;
    public $harga_bersih_show;

    #[\Livewire\Attributes\On('getupdateprodukobat')]
    public function getupdateprodukobat($rowId): void
    {
        $this->produkDanObatId = $rowId;

        $produkObat = ProdukDanObat::findOrFail($rowId);

        $this->nama_dagang   =   $produkObat->nama_dagang;
        $this->golongan      =   $produkObat->golongan;
        $this->kode          =   $produkObat->kode;
        $this->sediaan       =   $produkObat->sediaan;
        $this->harga_dasar   =   $produkObat->harga_dasar;
        $this->potongan      =   $produkObat->potongan ?? 0;
        $this->diskon        =   $produkObat->diskon ?? 0;
        $this->harga_bersih  =   $produkObat->harga_bersih ?? $produkObat->harga_dasar;
        $this->stok          =   $produkObat->stok;
        $this->expired_at    =   $produkObat->expired_at;
        $this->reminder      =   $produkObat->reminder;
        $this->batch         =   $produkObat->batch;
        $this->lokasi        =   $produkObat->lokasi;
        $this->supplier      =   $produkObat->supplier;

        $this->potongan_show = (int) preg_replace('/\D/', '', $this->potongan);
        $this->harga_dasar_show = (int) preg_replace('/\D/', '', $this->harga_dasar);
        $this->harga_bersih_show = (int) preg_replace('/\D/', '', $this->harga_bersih);


        $this->dispatch('openModal');
    }

    public function updated($property)
    {
        if (in_array($property, ['harga_dasar', 'diskon', 'potongan'])) {
            $harga  = (int) $this->harga_dasar;
            $diskon = (int) $this->diskon;
            $potongan = (int) $this->potongan;

            $hargaSetelahPotongan = max(0, $harga - $potongan);

            $diskonNominal = ($hargaSetelahPotongan * $diskon) / 100;

            $this->harga_bersih = max(0, $hargaSetelahPotongan - $diskonNominal);
        }
    }

    public function update()
    {
        $this->validate([
            'nama_dagang'   => 'required|string|max:255',
            'golongan'      => 'required|string|max:255',
            'kode'          => 'required|string|max:100',
            'sediaan'       => 'required|string|max:100',
            'harga_dasar'   => 'required|numeric|min:0',
            'potongan'      => 'nullable|numeric|min:0',
            'diskon'        => 'nullable|min:0|max:100',
            'stok'          => 'required|integer|min:0',
            'expired_at'    => 'nullable|date',
            'reminder'      => 'nullable|integer',
            'batch'         => 'nullable|string|max:100',
            'lokasi'        => 'nullable|string|max:100',
            'supplier'      => 'nullable|string|max:255',
        ]);
        if (! Gate::allows('akses', 'Persediaan Produk & Obat Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        ProdukDanObat::where('id', $this->produkDanObatId)->update([
            'nama_dagang'   => $this->nama_dagang,
            'golongan'      => $this->golongan,
            'kode'          => $this->kode,
            'sediaan'       => $this->sediaan,
            'harga_dasar'   => $this->harga_dasar,
            'potongan'      => $this->potongan,
            'diskon'        => $this->diskon,
            'harga_bersih'  => $this->harga_bersih,
            'stok'          => $this->stok,
            'expired_at'    => $this->expired_at,
            'reminder'      => $this->reminder,
            'batch'         => $this->batch,
            'lokasi'        => $this->lokasi,
            'supplier'      => $this->supplier,
        ]);
        if (! Gate::allows('akses', 'Produk & Obat Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data produk berhasil diperbarui.'
        ]);

        $this->dispatch('closeModalProdukObat');

        $this->reset();

        return redirect()->route('produk-obat.data'); // pastikan route ini benar
    }

    public function render()
    {
        return view('livewire.produkdanobat.update');
    }
}
