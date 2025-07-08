<?php

namespace App\Livewire\Pelayanan;

use App\Models\Pelayanan;
use Livewire\Component;

class UpdatePelayanan extends Component
{
    public $pelayananId;
    public $nama_pelayanan,$harga_pelayanan,$deskripsi;


    #[\Livewire\Attributes\On('editPelayanan')]
    public function editPelayanan($rowId): void
    {
        $this->pelayananId = $rowId;

        $pelayanan = Pelayanan::findOrFail($rowId);

        $this->nama_pelayanan = $pelayanan->nama_pelayanan;
        $this->harga_pelayanan = $pelayanan->harga_pelayanan;
        $this->deskripsi = $pelayanan->deskripsi;

        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate([
            'nama_pelayanan' => 'required',
            'harga_pelayanan' => 'required',
            'deskripsi' => 'nullable',
        ]);

        Pelayanan::where('id', $this->pelayananId)->update([
            'nama_pelayanan' => $this->nama_pelayanan,
            'harga_pelayanan' => $this->harga_pelayanan,
            'deskripsi' => $this->deskripsi,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);

        // ❌ Tutup modal via JS
        $this->dispatch('closeModal');

        // 🔄 Reset form
        $this->reset();

        return redirect()->route('pelayanan.data'); // untuk PowerGrid refresh
    }

    public function render()
    {
        return view('livewire.pelayanan.update-pelayanan');
    }
}
