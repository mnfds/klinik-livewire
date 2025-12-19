<?php

namespace App\Livewire\Pelayanan;

use Livewire\Component;
use App\Models\Pelayanan;
use Illuminate\Support\Facades\Gate;

class StorePelayanan extends Component
{
    public $nama_pelayanan;
    public $harga_pelayanan;
    public $deskripsi;
    public $diskon = 0;
    public $potongan = 0;
    public $harga_bersih;

    public function render()
    {
        return view('livewire.pelayanan.store-pelayanan');
    }

    public function store()
    {
        // dd([
        //     'harga_pelayanan' => $this->harga_pelayanan,
        //     'potongan' => $this->potongan,
        //     'diskon' => $this->diskon,
        //     'harga_bersih' => $this->harga_bersih,
        // ]);
        $this->validate([
            'nama_pelayanan'  => 'required',
            'harga_pelayanan' => 'required',
            'diskon'          => 'nullable|min:0|max:100',
            'potongan'        => 'nullable|min:0|integer',
            'deskripsi'       => 'nullable',
        ]);
        if (! Gate::allows('akses', 'Pelayanan Medis Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

         // Hitung harga bersih
        $harga = (float) $this->harga_pelayanan;
        $diskon = (float) $this->diskon ?? 0;
        $potongan = (float) $this->potongan ?? 0;

        // Harga Setelah Potogan Nominal
        $hargaSetelahPotongan = Max(0, $harga - $potongan);
        // Hitung Diskon Dalam Nominal
        $diskonNominal = ($hargaSetelahPotongan * $diskon) / 100;
        // Harga Bersih
        $this->harga_bersih = max(0, $hargaSetelahPotongan - $diskonNominal);

        Pelayanan::create([
            'nama_pelayanan'   => $this->nama_pelayanan,
            'harga_pelayanan'  => $this->harga_pelayanan,
            'diskon'           => $this->diskon,
            'potongan'         => $this->potongan,
            'harga_bersih'     => $this->harga_bersih,
            'deskripsi'        => $this->deskripsi,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pelayanan Medis berhasil ditambahkan.'
        ]);

        $this->dispatch('closeStoreModalPelayanan');

        $this->reset();

        return redirect()->route('pelayanan.data');
    }

}
