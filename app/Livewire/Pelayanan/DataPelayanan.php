<?php

namespace App\Livewire\Pelayanan;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class DataPelayanan extends Component
{
    public function render()
    {
        if (! Gate::allows('akses', 'Pelayanan Data')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.pelayanan.data-pelayanan');
    }
}
