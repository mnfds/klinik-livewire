<?php

namespace App\Livewire\Poli;

use App\Models\PoliKlinik;
use Livewire\Component;

class UpdatePoliklinik extends Component
{
    public $poliId;
    public $nama_poli, $kode;

    #[\Livewire\Attributes\On('editPoli')]
    public function editpoli($rowId): void
    {
        $this->poliId = $rowId;

        $poli = PoliKlinik::findOrFail($rowId);

        $this->nama_poli   = $poli->nama_poli;
        $this->kode   = $poli->kode;

        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate([
            'nama_poli' => 'required',
            'kode' => 'required',
        ]);

        PoliKlinik::where('id', $this->poliId)->update([
            'nama_poli' => $this->nama_poli,
            'kode' => $this->kode,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);

        // ❌ Tutup modal via JS
        $this->dispatch('closeModal');

        // 🔄 Reset form
        $this->reset();

        return redirect()->route('poliklinik.data'); // untuk PowerGrid refresh
    }

    public function render()
    {
        return view('livewire.poli.update-poliklinik');
    }
}

