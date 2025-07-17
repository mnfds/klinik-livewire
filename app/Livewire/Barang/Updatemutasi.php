<?php

namespace App\Livewire\Barang;

use App\Models\User;
use App\Models\Barang;
use App\Models\Biodata;
use Livewire\Component;
use App\Models\MutasiBarang;

class Updatemutasi extends Component
{
    public $mutasi_id;
    public $barang_id, $tipe, $jumlah, $diajukan_oleh, $catatan;
    public $mutasi;

    public $listBarang = [];
    public $listOrang = [];

    public function mount()
    {
        $this->listBarang = Barang::pluck('nama', 'id')->toArray();
        $this->listOrang = Biodata::pluck('nama_lengkap', 'id')->toArray();
    }

    #[\Livewire\Attributes\On('getupdatemutasi')]
    public function getupdatemutasi($rowId): void
    {
        $this->mutasi_id = $rowId;

        $mutasi = MutasiBarang::with('barang')->findOrFail($rowId);
        $this->barang_id   = $mutasi->barang->id;
        $this->tipe   = $mutasi->tipe;
        $this->jumlah   = $mutasi->jumlah;
        $this->diajukan_oleh   = $mutasi->diajukan_oleh;
        $this->catatan   = $mutasi->catatan;

        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate([
            'barang_id' => 'required',
            'tipe' => 'required',
            'jumlah' => 'required|numeric',
            'diajukan_oleh' => 'required',
            'catatan' => 'nullable',
        ]);

        $data = MutasiBarang::where('id', $this->mutasi_id)->update([
            'barang_id' => $this->barang_id,
            'tipe' => $this->tipe,
            'jumlah' => $this->jumlah,
            'diajukan_oleh' => $this->diajukan_oleh,
            'catatan' => $this->catatan,
        ]);


        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);

        // ❌ Tutup modal via JS
        $this->dispatch('closemodaleditbarang');

        // 🔄 Reset form
        $this->reset();

        return redirect()->route('barang.riwayat'); // untuk PowerGrid refresh
    }

    public function render()
    {
        return view('livewire.barang.updatemutasi');
    }
}
