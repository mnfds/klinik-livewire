<?php

namespace App\Livewire\Inventaris;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Data extends Component
{
    public function render()
    {
        if (! Gate::allows('akses', 'Inventaris')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.inventaris.data');
    }
}
