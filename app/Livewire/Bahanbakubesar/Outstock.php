<?php

namespace App\Livewire\Bahanbakubesar;

use App\Models\BahanBakuBesar;
use App\Models\MutasiBahanBakuBesar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Outstock extends Component
{
    public $bahan_baku_besar_id, $jumlah, $diajukan_oleh, $catatan;
    public $tipe = 'keluar';

    public $bahan = [];

    public function mount()
    {
        $this->bahan = BahanBakuBesar::all();
    }

    public function render()
    {
        return view('livewire.bahanbakubesar.outstock');
    }

    public function store()
    {
        $this->validate([
            'bahan_baku_besar_id'   => 'required',
            'jumlah'   => 'required|numeric',
            'catatan'   => 'nullable',
        ]);
        if (! Gate::allows('akses', 'Persediaan Bahan Baku Masuk')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        MutasiBahanBakuBesar::create([
            'bahan_baku_besar_id'   => $this->bahan_baku_besar_id,
            'tipe' => $this->tipe,
            'jumlah'   => $this->jumlah,
            'catatan'   => $this->catatan,
            'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data Bahan Baku berhasil Diperbarui.'
        ]);

        $this->reset();

        $this->dispatch('pg:eventRefresh-DishTable');

        $this->dispatch('closeOutstockModalBahanbakubesar');

        return redirect()->route('bahanbakubesar.data');
    }
}
