<?php

namespace App\Livewire\Bundling;

use Livewire\Component;
use App\Models\Bundling;
use App\Models\Pelayanan;
use App\Models\ProdukDanObat;
use Livewire\Volt\Compilers\Mount;

class StoreBundling extends Component
{
    // Untuk select list
    public $pelayananList = [];
    public $produkObatList = [];

    // Untuk input form
    public $nama;
    public $deskripsi;
    public $harga;
    public $diskon;
    public $harga_bersih;
    public $pelayanan = [];      // multiple select
    public $produk_obat = [];    // multiple select

    public function mount()
    {
        $this->pelayananList = Pelayanan::select('id', 'nama_pelayanan')->orderBy('nama_pelayanan')->get();
        $this->produkObatList = ProdukDanObat::select('id', 'nama_dagang')->orderBy('nama_dagang')->get();
    }

    public function render()
    {
        return view('livewire.bundling.store-bundling');
    }

    public function store()
    {
        $validated = $this->validate([
            'nama'          => 'required|string|max:255',
            'deskripsi'     => 'nullable|string|max:1000',
            'harga'         => 'required|numeric|min:0',
            'diskon'        => 'required|numeric|min:0|max:100',
            'harga_bersih'  => 'required|numeric|min:0',
            'pelayanan'     => 'nullable',
            'produk_obat'   => 'nullable',
        ]);

        // Handle multiselect kosong yang dikirim sebagai string kosong atau array kosong
        $pelayanan = is_string($validated['pelayanan']) ? json_decode($validated['pelayanan'], true) : $validated['pelayanan'];
        $produkObat = is_string($validated['produk_obat']) ? json_decode($validated['produk_obat'], true) : $validated['produk_obat'];

        $pelayanan = is_array($pelayanan) ? $pelayanan : [];
        $produkObat = is_array($produkObat) ? $produkObat : [];

        $bundling = Bundling::create([
            'nama'          => $validated['nama'],
            'deskripsi'     => $validated['deskripsi'],
            'harga'         => $validated['harga'],
            'diskon'        => $validated['diskon'],
            'harga_bersih'  => $validated['harga_bersih'],
        ]);

        foreach ($pelayanan as $pelayananId) {
            $bundling->pelayananBundlings()->create([
                'pelayanan_id' => $pelayananId
            ]);
        }

        foreach ($produkObat as $produkId) {
            $bundling->produkObatBundlings()->create([
                'produk_id' => $produkId
            ]);
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