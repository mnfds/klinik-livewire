<?php

namespace App\Livewire\Bahan;

use Livewire\Component;
use App\Models\BahanBaku;
use App\Models\MutasiBahanbaku;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Take extends Component
{
    public $bahan_baku_id, $jumlah, $diajukan_oleh, $catatan;
    public $tipe = 'keluar';

    public $bahan = [];

    public function mount()
    {
        $this->bahan = BahanBaku::all();
    }

    public function render()
    {
        return view('livewire.bahan.take');
    }

    public function store()
    {
        $this->validate([
            'bahan_baku_id'   => 'required',
            'jumlah'   => 'required|numeric',
            'catatan'   => 'nullable',
        ]);
        if (! Gate::allows('akses', 'Persediaan Bahan Baku Keluar')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        MutasiBahanbaku::create([
            'bahan_baku_id'   => $this->bahan_baku_id,
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

        $this->dispatch('closetakeModalBahanbaku');

        return redirect()->route('bahanbaku.data');
    }
}
