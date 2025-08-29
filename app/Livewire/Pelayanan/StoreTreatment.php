<?php

namespace App\Livewire\Pelayanan;

use App\Models\Treatment;
use Livewire\Component;

class StoreTreatment extends Component
{
    public $nama_treatment;
    public $harga_treatment;
    public $deskripsi;
    public $diskon = 0;
    public $harga_bersih;

    public function render()
    {
        return view('livewire.pelayanan.store-treatment');
    }

    public function store()
    {
        $this->validate([
            'nama_treatment'  => 'required',
            'harga_treatment' => 'required',
            'diskon'          => 'nullable|min:0|max:100',
            'deskripsi'       => 'nullable',
        ]);

         // Hitung harga bersih
        $harga = (float) $this->harga_treatment;
        $diskon = (float) $this->diskon;

        $diskonNominal = ($harga * $diskon) / 100;
        $this->harga_treatment = $harga - $diskonNominal;

        Treatment::create([
            'nama_treatment'   => $this->nama_treatment,
            'harga_treatment'  => $this->harga_treatment,
            'diskon'           => $this->diskon,
            'harga_bersih'     => $this->harga_bersih,
            'deskripsi'        => $this->deskripsi,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pelayanan Estetika berhasil ditambahkan.'
        ]);

        $this->dispatch('closeStoreModalPelayananEstetika');

        $this->reset();

        return redirect()->route('pelayanan.data');
    }

}
