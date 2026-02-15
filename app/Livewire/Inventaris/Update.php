<?php

namespace App\Livewire\Inventaris;

use App\Models\Inventaris;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Update extends Component
{
    public $inventaris_id;
    public $kode_inventaris, $nama_barang, $jumlah, $lokasi, $tanggal_perolehan, $kondisi, $keterangan;
    
    public function render()
    {
        return view('livewire.inventaris.update');
    }

    #[\Livewire\Attributes\On('getupdateinventaris')]
    public function getupdateinventaris($rowId): void
    {
        $this->inventaris_id = $rowId;

        $inventaris = Inventaris::findOrFail($this->inventaris_id);

        $this->nama_barang          = $inventaris->nama_barang;
        $this->jumlah               = $inventaris->jumlah;
        $this->lokasi               = $inventaris->lokasi;
        $this->tanggal_perolehan    = $inventaris->tanggal_perolehan;
        $this->kondisi              = $inventaris->kondisi;
        $this->keterangan           = $inventaris->keterangan;

        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate([
            'nama_barang' => 'required',
            'jumlah' => 'required|integer',
            'lokasi' => 'required',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat'
        ]);

        if (! Gate::allows('akses', 'Inventaris Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        Inventaris::where('id', $this->inventaris_id)->update([
            'nama_barang'       => $this->nama_barang,
            'jumlah'            => $this->jumlah,
            'lokasi'            => $this->lokasi,
            'tanggal_perolehan' => $this->tanggal_perolehan,
            'kondisi'           => $this->kondisi,
            'keterangan'        => $this->keterangan,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
        $this->dispatch('modaleditinventaris');
        $this->reset();

        return redirect()->route('inventaris.data'); // untuk PowerGrid refresh
    }
}
