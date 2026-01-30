<?php

namespace App\Livewire\Lembur;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Data extends Component
{
    public function render()
    {
        if (! Gate::allows('akses', 'Pengajuan Lembur')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.lembur.data');
    }
}
