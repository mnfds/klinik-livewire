<?php

namespace App\Livewire\Dokumen;

use App\Models\Dokumen;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Update extends Component
{
    public $dokumen_id;
    public $nama, $lembaga, $tanggal_berlaku, $tanggal_tidak_berlaku, $reminder, $keterangan;

    public function render()
    {
        return view('livewire.dokumen.update');
    }

    #[\Livewire\Attributes\On('getupdatedokumen')]
    public function getupdatedokumen($rowId): void
    {
        $this->dokumen_id = $rowId;

        $dokumen = Dokumen::findOrFail($this->dokumen_id);

        $this->nama                     = $dokumen->nama;
        $this->lembaga                  = $dokumen->lembaga;
        $this->tanggal_berlaku          = $dokumen->tanggal_berlaku;
        $this->tanggal_tidak_berlaku    = $dokumen->tanggal_tidak_berlaku;
        $this->reminder                 = $dokumen->reminder;
        $this->keterangan               = $dokumen->keterangan;

        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required',
            'tanggal_berlaku' => 'required|date',
            'tanggal_tidak_berlaku' => 'required|date'
        ]);

        if (! Gate::allows('akses', 'Dokumen Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        Dokumen::where('id', $this->dokumen_id)->update([
            'nama'                  => $this->nama,
            'lembaga'               => $this->lembaga,
            'tanggal_berlaku'       => $this->tanggal_berlaku,
            'tanggal_tidak_berlaku' => $this->tanggal_tidak_berlaku,
            'reminder'              => $this->reminder,
            'keterangan'            => $this->keterangan,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
        $this->dispatch('closemodaleditditerima');
        $this->reset();

        return redirect()->route('dokumen.data'); // untuk PowerGrid refresh
    }
}
