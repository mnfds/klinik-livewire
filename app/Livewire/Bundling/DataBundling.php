<?php

namespace App\Livewire\Bundling;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class DataBundling extends Component
{
    public function render()
    {
        if (! Gate::allows('akses', 'Paket Bundling Data')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.bundling.data-bundling');
    }
}
