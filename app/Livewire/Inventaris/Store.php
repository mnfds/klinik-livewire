<?php

namespace App\Livewire\Inventaris;

use App\Models\Inventaris;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Store extends Component
{
    public $kode_inventaris, $nama_barang, $jumlah, $lokasi, $tanggal_perolehan, $kondisi, $keterangan;

    public function render()
    {
        return view('livewire.inventaris.store');
    }

    public function store()
    {
        $this->validate([
            'nama_barang' => 'required',
            'jumlah' => 'required|integer',
            'lokasi' => 'required',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat'
        ]);

        if (! Gate::allows('akses', 'Inventaris Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        Inventaris::create([
            'kode_inventaris'   => $this->kode_inventaris,
            'nama_barang'       => $this->nama_barang,
            'jumlah'            => $this->jumlah,
            'lokasi'            => $this->lokasi,
            'tanggal_perolehan' => $this->tanggal_perolehan,
            'kondisi'           => $this->kondisi,
            'keterangan'        => $this->keterangan,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Inventaris Berhasil Ditambahkan'
        ]);
        $this->dispatch('storeModalInventaris');
        $this->reset();
        return redirect()->route('inventaris.data');

    }

}
