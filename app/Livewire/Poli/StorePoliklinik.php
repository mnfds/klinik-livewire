<?php

namespace App\Livewire\Poli;

use App\Models\PoliKlinik;
use Livewire\Component;

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
            'kode'   => 'nullable',
        ]);

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
