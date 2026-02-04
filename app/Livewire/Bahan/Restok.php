<?php

namespace App\Livewire\Bahan;

use Livewire\Component;
use App\Models\BahanBaku;
use App\Models\MutasiBahanbaku;
use App\Models\MutasiBahanBakuBesar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Restok extends Component
{
    public $bahan_baku_id, $diajukan_oleh, $catatan;
    public $stok = 0;
    public $jumlah = 0;
    public $pengali = 0;
    public $tipe = 'masuk';

    public $bahan = [];

    public function mount()
    {
        $this->bahan = BahanBaku::all();
    }

    public function updatedBahanBakuId($id)
    {
        if (!$id) {
            $this->pengali = 0;
            $this->stok = 0;
            return;
        }

        $bahan = BahanBaku::with('transferbahanbaku')->find($id);
        $this->pengali = $bahan->transferbahanbaku->first()->pengali ?? 0;

        $this->hitungStok();
    }

    public function updatedJumlah()
    {
        $this->hitungStok();
    }

    public function updatedPengali()
    {
        $this->hitungStok();
    }

    private function hitungStok()
    {
        $this->stok = (int) $this->jumlah * (int) $this->pengali;
    }

    public function render()
    {
        return view('livewire.bahan.restok');
    }

    public function store()
    {
        $this->validate([
            'bahan_baku_id'   => 'required',
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

        $bahanbakukecil = MutasiBahanbaku::create([
            'bahan_baku_id'   => $this->bahan_baku_id,
            'tipe' => $this->tipe,
            'jumlah'   => $this->stok,
            'catatan'   => $this->catatan,
            'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap,
        ]);
        
        $bahan = BahanBaku::with('transferbahanbaku')->find($this->bahan_baku_id);
        $bahanbakubesar_id = $bahan->transferbahanbaku->first()->bahan_baku_besar_id ?? null;
        MutasiBahanBakuBesar::create([
            'bahan_baku_besar_id'   => $bahanbakubesar_id,
            'tipe' => 'keluar',
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

        $this->dispatch('closerestockModalBahanbaku');

        return redirect()->route('bahanbaku.data');
    }
}
