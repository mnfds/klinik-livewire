<?php

namespace App\Livewire\Uangkeluar;

use App\Models\Uangkeluar;
use Livewire\Component;

class Approval extends Component
{
    public $pending_id;

    public function render()
    {
        return view('livewire.uangkeluar.approval');
    }

    #[\Livewire\Attributes\On('getdisetujui')]
    public function getdisetujui($rowId)
    {
        $this->pending_id = $rowId;
        dd($this->pending_id);
        Uangkeluar::where('id', $this->pending_id)->update([
            'status' => 'Disetujui',
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
        $this->reset();
        return redirect()->route('uangkeluar.data');
    }
}
