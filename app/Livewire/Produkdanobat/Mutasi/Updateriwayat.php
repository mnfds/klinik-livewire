<?php

namespace App\Livewire\Produkdanobat\Mutasi;

use App\Models\Biodata;
use App\Models\MutasiProdukDanObat;
use App\Models\ProdukDanObat;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Updateriwayat extends Component
{
    public $riwayat_id;
    public $produk_id, $tipe, $jumlah, $diajukan_oleh, $catatan;
    public $riwayat;

    public $listObat = [];
    public $listOrang = [];

    public function render()
    {
        return view('livewire.produkdanobat.mutasi.updateriwayat');
    }

    public function mount()
    {
        $this->listObat = ProdukDanObat::pluck('nama_dagang', 'id')->toArray();
        $this->listOrang = Biodata::pluck('nama_lengkap', 'id')->toArray();
    }

    #[\Livewire\Attributes\On('getupdateriwayatprodukdanobat')]
    public function getupdateriwayatprodukdanobat($rowId): void
    {
        $this->riwayat_id = $rowId;

        $riwayat = MutasiProdukDanObat::with('produkdanobat')->findOrFail($rowId);
        $this->produk_id   = $riwayat->produkdanobat->id;
        $this->tipe   = $riwayat->tipe;
        $this->jumlah   = $riwayat->jumlah;
        $this->diajukan_oleh   = $riwayat->diajukan_oleh;
        $this->catatan   = $riwayat->catatan;

        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate([
            'produk_id' => 'required',
            'tipe' => 'required',
            'jumlah' => 'required|numeric',
            'diajukan_oleh' => 'required',
            'catatan' => 'nullable',
        ]);
        if (! Gate::allows('akses', 'Riwayat Produk & Obat Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $riwayat = MutasiProdukDanObat::findOrFail($this->riwayat_id);
        $produkLama = ProdukDanObat::findOrFail($riwayat->produk_id);

        if ($riwayat->tipe === 'keluar') {
            $produkLama->stok += $riwayat->jumlah;
        } else { // masuk
            $produkLama->stok -= $riwayat->jumlah;
        }
        $produkLama->save();

        $riwayat->update([
            'produk_id'     => $this->produk_id,
            'tipe'          => $this->tipe,
            'jumlah'        => $this->jumlah,
            'diajukan_oleh' => $this->diajukan_oleh,
            'catatan'       => $this->catatan,
        ]);

        $produkBaru = ProdukDanObat::findOrFail($this->produk_id);
        if ($this->tipe === 'masuk') {
            $produkBaru->stok += $this->jumlah;
        } else { 
            $produkBaru->stok -= $this->jumlah;
        }
        $produkBaru->save();
        
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);

        // ❌ Tutup modal via JS
        $this->dispatch('closemodaleditmutasibahan');

        // 🔄 Reset form
        $this->reset();

        return redirect()->route('produk-obat.riwayat'); // untuk PowerGrid refresh
    }
}
