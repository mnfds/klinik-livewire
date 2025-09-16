<?php

namespace App\Livewire\Bahan;

use App\Models\BahanBaku;
use Livewire\Component;

class Update extends Component
{
    public $bahan_id;
    public $nama, $kode, $satuan, $lokasi, $keterangan;
    public $bahan;


    #[\Livewire\Attributes\On('getupdatebahanbaku')]
    public function getupdatebahanbaku($rowId): void
    {
        $this->bahan_id = $rowId;

        $bahan = BahanBaku::findOrFail($rowId);

        $this->nama   = $bahan->nama;
        $this->kode   = $bahan->kode;
        $this->satuan   = $bahan->satuan;
        $this->lokasi   = $bahan->lokasi;
        $this->keterangan   = $bahan->keterangan;

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

        BahanBaku::where('id', $this->bahan_id)->update([
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
        $this->dispatch('closemodaleditbahanbaku');

        // 🔄 Reset form
        $this->reset();

        return redirect()->route('bahanbaku.data'); // untuk PowerGrid refresh
    }

    public function render()
    {
        return view('livewire.bahan.update');
    }
}
