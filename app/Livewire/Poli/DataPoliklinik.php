<?php

namespace App\Livewire\Poli;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class DataPoliklinik extends Component
{
    public function mount()
    {
        Gate::authorize('akses', 'Poliklinik Data');
    }
    
    public function render()
    {
        return view('livewire.poli.data-poliklinik');
    }
}
