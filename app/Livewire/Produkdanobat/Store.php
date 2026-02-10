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
            'nama_dagang' => 'required|string',
            'golongan' => 'required|string',
            'kode' => 'required|string|unique:produk_dan_obats,kode',
            'sediaan' => 'required|string',
            'harga_dasar' => 'required|integer|min:0',
            'potongan' => 'required|integer|min:0',
            'diskon' => 'nullable|min:0|max:100',
            'stok' => 'required|integer|min:0',
            'expired_at' => 'nullable|date',
            'reminder' => 'nullable|integer',
            'batch' => 'nullable|string',
            'lokasi' => 'nullable|string',
            'supplier' => 'nullable|string',
        ]);

        if (! Gate::allows('akses', 'Persediaan Produk & Obat Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        // Hitung harga bersih
        $harga = (float) $this->harga_dasar;
        $potongan = (float) $this->potongan;
        $diskon = (float) $this->diskon ?? 0;

        // Harga setelah potongan nominal
        $hargaSetelahPotongan = max(0, $harga - $potongan);

        // Hitung diskon dalam nominal
        $diskonNominal = ($hargaSetelahPotongan * $diskon) / 100;

        // Harga bersih akhir
        $this->harga_bersih = max(0, $hargaSetelahPotongan - $diskonNominal);

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
