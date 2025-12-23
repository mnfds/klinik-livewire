<?php

namespace App\Livewire\Satusehat\Praktisi;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Data extends Component
{
    public function render()
    {
        if (! Gate::allows('akses', 'Praktisi Satu Sehat')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.satusehat.praktisi.data');
    }
}
