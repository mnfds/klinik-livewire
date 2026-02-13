<?php

namespace App\Livewire\Dokumen;

use App\Models\Dokumen;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Store extends Component
{
    public $nama, $lembaga, $tanggal_berlaku, $tanggal_tidak_berlaku, $reminder, $keterangan;
    
    public function render()
    {
        return view('livewire.dokumen.store');
    }

    public function store()
    {
        $this->validate([
            'nama' => 'required',
            'tanggal_berlaku' => 'required|date',
            'tanggal_tidak_berlaku' => 'required|date'
        ]);

        if (! Gate::allows('akses', 'Dokumen Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        Dokumen::create([
            'nama'                  => $this->nama,
            'lembaga'               => $this->lembaga,
            'tanggal_berlaku'       => $this->tanggal_berlaku,
            'tanggal_tidak_berlaku' => $this->tanggal_tidak_berlaku,
            'reminder'              => $this->reminder,
            'keterangan'            => $this->keterangan,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Dokumen Berhasil Ditambahkan'
        ]);
        $this->dispatch('storeModalDokumen');
        $this->reset();
        return redirect()->route('dokumen.data');
    }
}
