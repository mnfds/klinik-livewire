<?php

namespace App\Livewire\Dokumen;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Data extends Component
{
    public function render()
    {
        if (! Gate::allows('akses', 'Dokumen')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.dokumen.data');
    }
}
