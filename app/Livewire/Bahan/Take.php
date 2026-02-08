<?php

namespace App\Livewire\Bahan;

use Livewire\Component;
use App\Models\BahanBaku;
use App\Models\MutasiBahanbaku;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Take extends Component
{
    public $bahan_baku_id, $jumlah, $satuan, $diajukan_oleh, $catatan;
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
        $bahanbakukecil = BahanBaku::findOrFail($this->bahan_baku_id);

        MutasiBahanbaku::create([
            'bahan_baku_id'   => $this->bahan_baku_id,
            'tipe' => $this->tipe,
            'jumlah'   => $this->jumlah,
            'satuan'   => $bahanbakukecil->satuan_kecil,
            'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap,
            'catatan'   => $this->catatan,
            ]);
            
        $stokTersisa = $bahanbakukecil->stok_kecil - $this->jumlah;
        $bahanbakukecil->update([
            'stok_kecil' => (int) $stokTersisa,
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
