<?php

namespace App\Livewire\Pelayanan;

use Livewire\Component;
use App\Models\Treatment;
use Illuminate\Support\Facades\Gate;

class StoreTreatment extends Component
{
    public $nama_treatment;
    public $harga_treatment;
    public $deskripsi;
    public $diskon = 0;
    public $potongan = 0;
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
            'potongan'        => 'nullable|min:0|max:100',
            'diskon'          => 'nullable|min:0|max:100',
            'deskripsi'       => 'nullable',
        ]);
        if (! Gate::allows('akses', 'Pelayanan Estetika Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

         // Hitung harga bersih
        $harga = (float) $this->harga_treatment;
        $potongan = (float) $this->potongan;
        $diskon = (float) $this->diskon;

        // Harga Setelah Potogan Nominal
        $hargaSetelahPotongan = Max(0, $harga - $potongan);
        // Hitung Diskon Dalam Nominal
        $diskonNominal = ($hargaSetelahPotongan * $diskon) / 100;
        // Harga Bersih
        $this->harga_bersih = max(0, $hargaSetelahPotongan - $diskonNominal);

        Treatment::create([
            'nama_treatment'   => $this->nama_treatment,
            'harga_treatment'  => $this->harga_treatment,
            'potongan'         => $this->potongan,
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
