<?php

namespace App\Livewire\Barang;

use App\Models\Barang;
use Livewire\Component;
use App\Models\MutasiBarang;
use Illuminate\Support\Facades\Auth;

class Restok extends Component
{
    public $barang_id, $jumlah, $diajukan_oleh, $catatan;
    public $tipe = 'masuk';

    public $barang = [];

    public function mount()
    {
        $this->barang = Barang::all();
    }

    public function render()
    {
        return view('livewire.barang.restok');
    }

    public function store()
    {
        $this->validate([
            'barang_id'   => 'required',
            'jumlah'   => 'required|numeric',
            'catatan'   => 'nullable',
        ]);

        MutasiBarang::create([
            'barang_id'   => $this->barang_id,
            'tipe' => $this->tipe,
            'jumlah'   => $this->jumlah,
            'catatan'   => $this->catatan,
            'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data Barang berhasil Diperbarui.'
        ]);

        $this->dispatch('closerestockModalBarang');

        $this->reset();

        return redirect()->route('barang.data');
    }
}
