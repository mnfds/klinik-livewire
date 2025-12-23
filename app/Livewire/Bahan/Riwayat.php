<?php

namespace App\Livewire\Bahan;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Gate;

#[Layout('layouts.app')]
class Riwayat extends Component
{
    public function render()
    {
        if (! Gate::allows('akses', 'Persediaan Riwayat Bahan Baku')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.bahan.riwayat');
    }
}
