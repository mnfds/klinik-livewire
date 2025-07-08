<?php

namespace App\Livewire\Pelayanan;

use App\Models\Pelayanan;
use Livewire\Component;

class StorePelayanan extends Component
{
    public $nama_pelayanan;
    public $harga_pelayanan;
    public $deskripsi;

    public function render()
    {
        return view('livewire.pelayanan.store-pelayanan');
    }

    public function store()
    {
        $this->validate([
            'nama_pelayanan' => 'required',
            'harga_pelayanan' => 'required',
            'deskripsi' => 'nullable',
        ]);

        Pelayanan::create([
            'nama_pelayanan'   => $this->nama_pelayanan,
            'harga_pelayanan'   => $this->harga_pelayanan,
            'deskripsi'    => $this->deskripsi,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pelayanan berhasil ditambahkan.'
        ]);

        $this->dispatch('closeStoreModal');

        $this->reset();

        return redirect()->route('pelayanan.data');
    }

}
