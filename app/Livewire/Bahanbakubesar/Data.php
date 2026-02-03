<?php

namespace App\Livewire\Bahanbakubesar;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Data extends Component
{
    public function render()
    {
        if (! Gate::allows('akses', 'Persediaan Bahan Baku')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.bahanbakubesar.data');
    }
}
