<?php

namespace App\Livewire\Satusehat\Organisasi;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Data extends Component
{
    public function render()
    {
        if (! Gate::allows('akses', 'Organisasi Satu Sehat')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.satusehat.organisasi.data');
    }
}
