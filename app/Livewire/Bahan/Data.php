<?php

namespace App\Livewire\Bahan;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;

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
        return view('livewire.bahan.data');
    }
}
