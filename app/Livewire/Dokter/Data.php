<?php

namespace App\Livewire\Dokter;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Data extends Component
{
    public function render()
    {
        if (! Gate::allows('akses', 'Dokter Data')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.dokter.data');
    }
}
