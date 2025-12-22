<?php

namespace App\Livewire\Apotik;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Kasir extends Component
{
    public function render()
    {
        if (! Gate::allows('akses', 'Transaksi Apotik')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }

        return view('livewire.apotik.kasir');
    }
}
