<?php

namespace App\Livewire\Bundling;

use App\Models\Bundling;
use App\Models\Pelayanan;
use App\Models\ProdukDanObat;
use App\Models\PelayananBundling;
use App\Models\ProdukObatBundling;
use Livewire\Component;

class UpdateBundling extends Component
{
    public $bundlingId;

    public $nama, $deskripsi, $harga, $diskon;
    public array $selectedPelayananIds = [];
    public array $selectedProdukIds = [];

    public function editBundling($rowId)
    {
        $this->bundlingId = $rowId;

        $bundling = Bundling::with(['pelayananBundlings', 'produkObatBundlings'])->findOrFail($rowId);

        $this->nama = $bundling->nama;
        $this->deskripsi = $bundling->deskripsi;
        $this->harga = $bundling->harga;
        $this->diskon = $bundling->diskon;

        $this->selectedPelayananIds = $bundling->pelayananBundlings->pluck('pelayanan_id')->toArray();
        $this->selectedProdukIds = $bundling->produkObatBundlings->pluck('produk_id')->toArray();

        $this->dispatch('openModalEditBundling');
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'diskon' => 'nullable|integer|min:0|max:100',
        ]);

        $hargaBersih = $this->harga - ($this->harga * $this->diskon / 100);

        $bundling = Bundling::findOrFail($this->bundlingId);
        $bundling->update([
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi,
            'harga' => $this->harga,
            'diskon' => $this->diskon,
            'harga_bersih' => $hargaBersih,
        ]);

        // ✅ Parse JSON dari Livewire
        $pelayananItems = collect($this->pelayananItems)->map(function ($item) {
            return is_string($item) ? json_decode($item, true) : $item;
        });

        $produkItems = collect($this->produkItems)->map(function ($item) {
            return is_string($item) ? json_decode($item, true) : $item;
        });

        // ✅ Hapus semua data relasi lama
        PelayananBundling::where('bundling_id', $bundling->id)->delete();
        ProdukObatBundling::where('bundling_id', $bundling->id)->delete();

        foreach ($this->selectedPelayananIds as $pelayananId) {
            PelayananBundling::create([
                'bundling_id' => $bundling->id,
                'pelayanan_id' => $pelayananId,
                'jumlah' => 1, // default
            ]);
        }

        foreach ($this->selectedProdukIds as $produkId) {
            ProdukObatBundling::create([
                'bundling_id' => $bundling->id,
                'produk_id' => $produkId,
                'jumlah' => 1, // default
            ]);
        }

        $this->dispatch('closeModalEditBundling');
        $this->dispatch('pg:eventRefresh-default');
        session()->flash('success', 'Bundling berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.bundling.update-bundling', [
            'listPelayanan' => Pelayanan::all(),
            'listProduk' => ProdukDanObat::all(),
        ]);
    }
}
