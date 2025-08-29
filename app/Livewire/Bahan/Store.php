<?php

namespace App\Livewire\Bahan;

use Livewire\Component;
use App\Models\BahanBaku;
use Illuminate\Support\Facades\Auth;

class Store extends Component
{
    public $nama, $kode, $satuan, $stok, $lokasi, $keterangan;
    public $bahanbaku_id, $jumlah, $diajukan_oleh, $catatan;
    public $tipe = 'masuk';
    public $bahanbaku;

    public function render()
    {
        return view('livewire.bahan.store');
    }

    public function store()
    {
        $this->validate([
            'nama'          => 'required',
            'stok'          => 'numeric|required',
            'satuan'        => 'required',
            'kode'          => 'nullable',
            'lokasi'        => 'nullable',
            'keterangan'    => 'nullable',
        ]);

        $bahanbaku = BahanBaku::create([
            'nama'   => $this->nama,
            'stok'   => $this->stok,
            'satuan'   => $this->satuan,
            'kode'   => $this->kode,
            'lokasi'   => $this->lokasi,
            'catatan'   => $this->keterangan,
        ]);

        $bahanbaku->mutasibahan()->create([
            'bahan_bakus_id'     => $bahanbaku->id,
            'tipe'     => $this->tipe,
            'jumlah'     => $bahanbaku->stok,
            'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data Bahan Baku berhasil Dibuat.'
        ]);

        $this->dispatch('closestoreModalBahan');

        $this->reset();

        return redirect()->route('bahanbaku.data');
    }
}
