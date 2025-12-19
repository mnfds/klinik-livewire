<?php

namespace App\Livewire\Dokter;

use App\Models\Dokter;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Detail extends Component
{
    public $id;
    public $dokter;
    public $poli = [];

    public function mount($id)
    {
        $this->id = $id;
        $this->dokter = Dokter::findOrFail($id);
        $this->poli = $this->dokter->dokterpoli()->with('poli')->get();
    }
    public function render()
    {
        if (! Gate::allows('akses', 'Dokter Detail')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.dokter.detail');
    }
}
