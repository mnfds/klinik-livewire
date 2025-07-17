<?php

namespace App\Livewire\Barang;

use App\Models\Barang;
use App\Models\MutasiBarang;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Take extends Component
{
    public $barang_id, $jumlah, $diajukan_oleh, $catatan;
    public $tipe = 'keluar';

    public $barang = [];

    public function mount()
    {
        $this->barang = Barang::all();
    }

    public function render()
    {
        return view('livewire.barang.take');
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

        
        $this->reset();

        $this->dispatch('pg:eventRefresh-DishTable');

        $this->dispatch('closetakeModalBarang');

        return redirect()->route('barang.data');
    }
}
