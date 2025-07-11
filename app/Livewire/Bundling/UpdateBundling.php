<?php

namespace App\Livewire\Bundling;

use App\Models\Bundling;
use App\Models\Pelayanan;
use App\Models\ProdukDanObat;
use App\Models\PelayananBundling;
use App\Models\ProdukObatBundling;
use Livewire\Component;
use Livewire\Attributes\On;

class UpdateBundling extends Component
{
    public $bundlingId;

    public $nama, $deskripsi, $harga, $diskon, $harga_bersih;
    public $pelayananInputs = [];
    public $produkInputs = [];

    public $pelayananList = [];
    public $produkObatList = [];

    public function mount()
    {
        $this->pelayananList = Pelayanan::select('id', 'nama_pelayanan')->orderBy('nama_pelayanan')->get();
        $this->produkObatList = ProdukDanObat::select('id', 'nama_dagang')->orderBy('nama_dagang')->get();
    }

    #[On('editBundling')]
    public function editBundling($rowId)
    {
        $this->reset(['pelayananInputs', 'produkInputs']); // reset agar tidak dobel
        $this->bundlingId = $rowId;

        $bundling = Bundling::with(['pelayananBundlings', 'produkObatBundlings'])->findOrFail($rowId);

        $this->nama = $bundling->nama;
        $this->deskripsi = $bundling->deskripsi;
        $this->harga = $bundling->harga;
        $this->diskon = $bundling->diskon;
        $this->harga_bersih = $bundling->harga_bersih;

        $this->pelayananInputs = $bundling->pelayananBundlings->map(function ($item) {
            return [
                'pelayanan_id' => $item->pelayanan_id,
                'jumlah' => $item->jumlah ?? 1,
            ];
        })->toArray();

        $this->produkInputs = $bundling->produkObatBundlings->map(function ($item) {
            return [
                'produk_id' => $item->produk_id,
                'jumlah' => $item->jumlah ?? 1,
            ];
        })->toArray();

        $this->dispatch('openModalEditBundling');
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

    public function update()
    {
        $this->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'diskon' => 'required|numeric|min:0|max:100',
            'harga_bersih' => 'required|numeric|min:0',

            'pelayananInputs.*.pelayanan_id' => 'nullable|exists:pelayanans,id',
            'pelayananInputs.*.jumlah' => 'nullable|numeric|min:1',

            'produkInputs.*.produk_id' => 'nullable|exists:produk_dan_obats,id',
            'produkInputs.*.jumlah' => 'nullable|numeric|min:1',
        ]);

        $bundling = Bundling::findOrFail($this->bundlingId);
        $bundling->update([
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi,
            'harga' => $this->harga,
            'diskon' => $this->diskon,
            'harga_bersih' => $this->harga_bersih,
        ]);

        PelayananBundling::where('bundling_id', $bundling->id)->delete();
        ProdukObatBundling::where('bundling_id', $bundling->id)->delete();

        foreach ($this->pelayananInputs as $item) {
            if ($item['pelayanan_id']) {
                PelayananBundling::create([
                    'bundling_id' => $bundling->id,
                    'pelayanan_id' => $item['pelayanan_id'],
                    'jumlah' => $item['jumlah'] ?? 1,
                ]);
            }
        }

        foreach ($this->produkInputs as $item) {
            if ($item['produk_id']) {
                ProdukObatBundling::create([
                    'bundling_id' => $bundling->id,
                    'produk_id' => $item['produk_id'],
                    'jumlah' => $item['jumlah'] ?? 1,
                ]);
            }
        }

        $this->dispatch('closeModalEditBundling');
        $this->dispatch('pg:eventRefresh-default');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Bundling berhasil ditambahkan.'
        ]);

        return redirect()->route('bundling.data');
    }

    public function render()
    {
        return view('livewire.bundling.update-bundling');
    }
}