<?php

namespace App\Livewire\Pelayanan;

use App\Models\Pelayanan;
use Livewire\Component;

class StorePelayanan extends Component
{
    public $nama_pelayanan;
    public $harga_pelayanan;
    public $deskripsi;
    public $diskon = 0;
    public $harga_bersih;

    public function render()
    {
        return view('livewire.pelayanan.store-pelayanan');
    }

    public function store()
    {
        $this->validate([
            'nama_pelayanan'  => 'required',
            'harga_pelayanan' => 'required',
            'diskon'          => 'nullable|min:0|max:100',
            'deskripsi'       => 'nullable',
        ]);

         // Hitung harga bersih
        $harga = (float) $this->harga_pelayanan;
        $diskon = (float) $this->diskon;

        $diskonNominal = ($harga * $diskon) / 100;
        $this->harga_bersih = $harga - $diskonNominal;

        Pelayanan::create([
            'nama_pelayanan'   => $this->nama_pelayanan,
            'harga_pelayanan'  => $this->harga_pelayanan,
            'diskon'           => $this->diskon,
            'harga_bersih'     => $this->harga_bersih,
            'deskripsi'        => $this->deskripsi,
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
