<?php

namespace App\Livewire\Barang;

use App\Models\Barang;
use Livewire\Component;
use App\Models\MutasiBarang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
        if (! Gate::allows('akses', 'Persediaan Barang Keluar')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        MutasiBarang::create([
            'barang_id'   => $this->barang_id,
            'tipe' => $this->tipe,
            'jumlah'   => $this->jumlah,
            'catatan'   => $this->catatan,
            'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap,
        ]);

        $stokBarang = Barang::findOrFail($this->barang_id);
        $stokTersisa = $stokBarang->stok - $this->jumlah;
        $stokBarang->update([
            'stok' => $stokTersisa,
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
