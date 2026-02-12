<?php

namespace App\Livewire\Produkdanobat;

use Livewire\Component;
use App\Models\ProdukDanObat;
use Illuminate\Support\Facades\Gate;

class Store extends Component
{
    public $nama_dagang, $golongan, $kode, $sediaan, $harga_dasar, $stok;
    public $expired_at,$reminder, $batch, $lokasi, $supplier, $harga_bersih;
    public $diskon = 0;
    public $potongan = 0;

    public function render()
    {
        return view('livewire.produkdanobat.store');
    }

    public function store()
    {

        $this->validate([
            'nama_dagang'   => 'required|string',
            'golongan'      => 'required|in:Skincare,Obat Bebas,Obat Bebas Terbatas,Obat Keras,Obat Narkotika,Obat Psikotropika,Obat fitofarmaka,OHT (Obat Herbal Terstandar),Jamu,Lain - Lain',
            'kode'          => 'required|string|unique:produk_dan_obats,kode',
            'sediaan'       => 'required|in:Pcs,Pot,Tablet,Botol,Sachet,Strip,Box,Paket,Kapsul,Sirup,Salep,Injeksi,Tube',
            'harga_dasar'   => 'required|integer|min:0',
            'potongan'      => 'nullable|integer|min:0',
            'diskon'        => 'nullable|min:0|max:100',
            'stok'          => 'required|integer|min:0',
            'expired_at'    => 'nullable|date',
            'reminder'      => 'nullable|integer',
            'batch'         => 'nullable|string',
            'lokasi'        => 'nullable|string',
            'supplier'      => 'nullable|string',
        ]);

        if (! Gate::allows('akses', 'Persediaan Produk & Obat Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        // Hitung harga bersih
        $harga     = (float) ($this->harga_dasar ?? 0);
        $potongan  = (float) ($this->potongan ?? 0);
        $diskon    = (float) ($this->diskon ?? 0);

        // Hitung diskon
        $diskonNominal = ($harga * $diskon) / 100;
        $hargaSetelahDiskon = max(0, $harga - $diskonNominal);
        $this->harga_bersih = max(0, $hargaSetelahDiskon - $potongan);

        ProdukDanObat::create([
            'nama_dagang' => $this->nama_dagang,
            'golongan' => $this->golongan,
            'kode' => $this->kode,
            'sediaan' => $this->sediaan,
            'harga_dasar' => $this->harga_dasar,
            'potongan' => $this->potongan,
            'diskon' => $this->diskon,
            'harga_bersih' => $this->harga_bersih,
            'stok' => $this->stok,
            'expired_at' => $this->expired_at,
            'reminder' => $this->reminder,
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
