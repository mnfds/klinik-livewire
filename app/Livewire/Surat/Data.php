<?php

namespace App\Livewire\Surat;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Data extends Component
{
    public function render()
    {
        if (! Gate::allows('akses', 'Surat Keterangan Data')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.surat.data');
    }
}
