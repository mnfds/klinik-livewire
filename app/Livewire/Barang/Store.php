<?php

namespace App\Livewire\Barang;

use App\Models\Barang;
use Livewire\Component;
use App\Models\MutasiBarang;
use Illuminate\Support\Facades\Auth;

class Store extends Component
{
    public $nama, $kode, $satuan, $stok, $lokasi, $keterangan;
    public $barang_id, $jumlah, $diajukan_oleh, $catatan;
    public $tipe = 'masuk';
    public $barang;

    public function render()
    {
        return view('livewire.barang.store');
    }

    public function store()
    {
        $this->validate([
            'nama'          => 'required',
            'stok'          => 'numeric|required',
            'satuan'        => 'required',
            'kode'          => 'nullable',
            'lokasi'        => 'nullable',
            'keterangan'    => 'nullable',
        ]);

        $barang = Barang::create([
            'nama'   => $this->nama,
            'stok'   => $this->stok,
            'satuan'   => $this->satuan,
            'kode'   => $this->kode,
            'lokasi'   => $this->lokasi,
            'keterangan'   => $this->keterangan,
        ]);

        $barang->mutasi()->create([
            'barang_id'     => $barang->id,
            'tipe'     => $this->tipe,
            'jumlah'     => $barang->stok,
            'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data Barang berhasil Diperbarui.'
        ]);

        $this->dispatch('closestoreModalPoli');

        $this->reset();

        return redirect()->route('barang.data');
    }
}
