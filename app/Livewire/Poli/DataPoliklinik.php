<?php

namespace App\Livewire\Poli;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class DataPoliklinik extends Component
{    
    public function render()
    {
        if (! Gate::allows('akses', 'Poliklinik Data')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.poli.data-poliklinik');
    }
}
