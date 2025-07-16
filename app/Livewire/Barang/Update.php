<?php

namespace App\Livewire\Barang;

use App\Models\Barang;
use Livewire\Component;

class Update extends Component
{
    public $barang_id;
    public $nama, $kode, $satuan, $lokasi, $keterangan;
    public $barang;


    #[\Livewire\Attributes\On('getupdatebarang')]
    public function getupdatebarang($rowId): void
    {
        $this->barang_id = $rowId;

        $barang = Barang::findOrFail($rowId);

        $this->nama   = $barang->nama;
        $this->kode   = $barang->kode;
        $this->satuan   = $barang->satuan;
        $this->lokasi   = $barang->lokasi;
        $this->keterangan   = $barang->keterangan;

        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required',
            'satuan' => 'required',
            'kode' => 'nullable',
            'lokasi' => 'nullable',
            'keterangan' => 'nullable',
        ]);

        Barang::where('id', $this->barang_id)->update([
            'nama' => $this->nama,
            'satuan' => $this->satuan,
            'kode' => $this->kode,
            'lokasi' => $this->lokasi,
            'keterangan' => $this->keterangan,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);

        // ❌ Tutup modal via JS
        $this->dispatch('closemodaleditbarang');

        // 🔄 Reset form
        $this->reset();

        return redirect()->route('barang.data'); // untuk PowerGrid refresh
    }

    public function render()
    {
        return view('livewire.barang.update');
    }
}
