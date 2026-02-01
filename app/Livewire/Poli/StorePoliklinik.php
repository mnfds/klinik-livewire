<?php

namespace App\Livewire\Poli;

use Livewire\Component;
use App\Models\PoliKlinik;
use Illuminate\Support\Facades\Gate;

class StorePoliklinik extends Component
{
    public $nama_poli;
    public $kode;

    public function render()
    {
        return view('livewire.poli.store-poliklinik');
    }

    public function store()
    {
        $this->validate([
            'nama_poli'   => 'required|string',
            'kode'   => 'required',
        ]);
        if (! Gate::allows('akses', 'Poliklinik Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        PoliKlinik::create([
            'nama_poli'   => $this->nama_poli,
            'kode'   => $this->kode,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Poliklinik berhasil ditambahkan.'
        ]);

        $this->dispatch('closestoreModalPoli');

        $this->reset();

        return redirect()->route('poliklinik.data');
    }
}
