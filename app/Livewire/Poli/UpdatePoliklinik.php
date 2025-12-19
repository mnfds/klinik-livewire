<?php

namespace App\Livewire\Poli;

use Livewire\Component;
use App\Models\PoliKlinik;
use Illuminate\Support\Facades\Gate;

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

        if (! Gate::allows('akses', 'Poliklinik Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

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

