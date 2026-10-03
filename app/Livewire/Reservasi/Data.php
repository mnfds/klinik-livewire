<?php

namespace App\Livewire\Reservasi;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Data extends Component
{
    public function render()
    {
        if (! Gate::allows('akses', 'Reservasi Data')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.reservasi.data');
    }
}
