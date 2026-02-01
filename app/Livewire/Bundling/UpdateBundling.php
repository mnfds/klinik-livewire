<?php

namespace App\Livewire\Bundling;

use Livewire\Component;
use App\Models\Bundling;
use App\Models\Pelayanan;
use App\Models\Treatment;
use Livewire\Attributes\On;
use App\Models\ProdukDanObat;
use App\Models\PelayananBundling;
use App\Models\TreatmentBundling;
use App\Models\ProdukObatBundling;
use Illuminate\Support\Facades\Gate;

class UpdateBundling extends Component
{
    public $bundlingId;

    public $nama, $deskripsi, $harga, $diskon, $potongan, $harga_bersih;
    public $treatmentInputs = [];
    public $pelayananInputs = [];
    public $produkInputs = [];

    public $treatmentList = [];
    public $pelayananList = [];
    public $produkObatList = [];

    public $potongan_show, $harga_show, $harga_bersih_show;

    public function mount()
    {
        $this->treatmentList = Treatment::select('id', 'nama_treatment')->orderBy('nama_treatment')->get();
        $this->pelayananList = Pelayanan::select('id', 'nama_pelayanan')->orderBy('nama_pelayanan')->get();
        $this->produkObatList = ProdukDanObat::select('id', 'nama_dagang')->orderBy('nama_dagang')->get();

    }

    #[On('editBundling')]
    public function editBundling($rowId)
    {
        $this->reset(['treatmentInputs', 'pelayananInputs', 'produkInputs']); // reset agar tidak dobel
        $this->bundlingId = $rowId;

        $bundling = Bundling::with(['treatmentBundlings', 'pelayananBundlings', 'produkObatBundlings'])->findOrFail($rowId);

        $this->nama = $bundling->nama;
        $this->deskripsi = $bundling->deskripsi;
        $this->harga = $bundling->harga;
        $this->potongan = $bundling->potongan ?? 0;
        $this->diskon = $bundling->diskon ?? 0;
        $this->harga_bersih = $bundling->harga_bersih;

        $this->potongan_show = (int) preg_replace('/\D/', '', $this->potongan);
        $this->harga_show = (int) preg_replace('/\D/', '', $this->harga);
        $this->harga_bersih_show = (int) preg_replace('/\D/', '', $this->harga_bersih);

        $this->treatmentInputs = $bundling->treatmentBundlings->map(function ($item) {
            return [
                'treatments_id' => $item->treatments_id,
                'jumlah' => $item->jumlah ?? 1,
            ];
        })->toArray();

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

    public function addTreatmentRow()
    {
        $this->treatmentInputs[] = ['treatments_id' => null, 'jumlah' => 1];
    }

    public function removeTreatmentRow($index)
    {
        unset($this->treatmentInputs[$index]);
        $this->treatmentInputs = array_values($this->treatmentInputs);
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
            'harga' => 'required|numeric|min:1',
            'potongan' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0|max:100',
            'harga_bersih' => 'required|numeric|min:1',

            'treatmentInputs.*.treatments_id' => 'nullable|exists:treatments,id',
            'treatmentInputs.*.jumlah' => 'nullable|numeric|min:1',

            'pelayananInputs.*.pelayanan_id' => 'nullable|exists:pelayanans,id',
            'pelayananInputs.*.jumlah' => 'nullable|numeric|min:1',

            'produkInputs.*.produk_id' => 'nullable|exists:produk_dan_obats,id',
            'produkInputs.*.jumlah' => 'nullable|numeric|min:1',
        ]);
        
        if (! Gate::allows('akses', 'Paket Bundling Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $bundling = Bundling::findOrFail($this->bundlingId);
        $bundling->update([
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi,
            'harga' => $this->harga,
            'potongan' => $this->potongan,
            'diskon' => $this->diskon,
            'harga_bersih' => $this->harga_bersih,
        ]);

        TreatmentBundling::where('bundling_id', $bundling->id)->delete();
        PelayananBundling::where('bundling_id', $bundling->id)->delete();
        ProdukObatBundling::where('bundling_id', $bundling->id)->delete();

        foreach ($this->treatmentInputs as $item) {
            if ($item['treatments_id']) {
                TreatmentBundling::create([
                    'bundling_id' => $bundling->id,
                    'treatments_id' => $item['treatments_id'],
                    'jumlah' => $item['jumlah'] ?? 1,
                ]);
            }
        }

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