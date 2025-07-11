<?php

namespace App\Livewire\Bundling;

use Livewire\Component;
use App\Models\Bundling;
use App\Models\Pelayanan;
use App\Models\ProdukDanObat;

class StoreBundling extends Component
{
    // List untuk pilihan select
    public $pelayananList = [];
    public $produkObatList = [];

    // Input bundling utama
    public $nama;
    public $deskripsi;
    public $harga;
    public $diskon;
    public $harga_bersih;

    // Form dinamis
    public $pelayananInputs = [['pelayanan_id' => null, 'jumlah' => 1]];
    public $produkInputs = [['produk_id' => null, 'jumlah' => 1]];

    public function mount()
    {
        $this->pelayananList = Pelayanan::select('id', 'nama_pelayanan')->orderBy('nama_pelayanan')->get();
        $this->produkObatList = ProdukDanObat::select('id', 'nama_dagang')->orderBy('nama_dagang')->get();
    }

    public function render()
    {
        return view('livewire.bundling.store-bundling');
    }

    public function addPelayananRow()
    {
        $this->pelayananInputs[] = ['pelayanan_id' => null, 'jumlah' => 1];
    }

    public function removePelayananRow($index)
    {
        unset($this->pelayananInputs[$index]);
        $this->pelayananInputs = array_values($this->pelayananInputs);
    }

    public function addProdukRow()
    {
        $this->produkInputs[] = ['produk_id' => null, 'jumlah' => 1];
    }

    public function removeProdukRow($index)
    {
        unset($this->produkInputs[$index]);
        $this->produkInputs = array_values($this->produkInputs);
    }

    public function store()
    {
        $this->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'harga' => 'required|numeric|min:0',
            'diskon' => 'required|numeric|min:0|max:100',
            'harga_bersih' => 'required|numeric|min:0',

            'pelayananInputs.*.pelayanan_id' => 'nullable|exists:pelayanans,id',
            'pelayananInputs.*.jumlah' => 'nullable|numeric|min:1',

            'produkInputs.*.produk_id' => 'nullable|exists:produk_dan_obats,id',
            'produkInputs.*.jumlah' => 'nullable|numeric|min:1',
        ]);

        $bundling = Bundling::create([
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi,
            'harga' => $this->harga,
            'diskon' => $this->diskon,
            'harga_bersih' => $this->harga_bersih,
        ]);

        // Simpan pelayanan
        foreach ($this->pelayananInputs as $item) {
            if ($item['pelayanan_id']) {
                $bundling->pelayananBundlings()->create([
                    'pelayanan_id' => $item['pelayanan_id'],
                    'jumlah' => $item['jumlah'] ?? 1,
                ]);
            }
        }

        // Simpan produk
        foreach ($this->produkInputs as $item) {
            if ($item['produk_id']) {
                $bundling->produkObatBundlings()->create([
                    'produk_id' => $item['produk_id'],
                    'jumlah' => $item['jumlah'] ?? 1,
                ]);
            }
        }

        $this->reset();
        $this->dispatch('closeStoreModalBundling');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Bundling berhasil ditambahkan.'
        ]);

        return redirect()->route('bundling.data');
    }
}