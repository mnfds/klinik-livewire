<?php

namespace App\Livewire\Jadwal;

use App\Models\Role;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Data extends Component
{
    public $role;
    public $thisMonth;
    public $selectedRole;

    public function render()
    {
        if (! Gate::allows('akses', 'Jadwal')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.jadwal.data');
    }

    public function mount()
    {
        // $this->role = Role::all();
        // $this->role = Role::whereNot('id', 1)->get(); //pengecuali untuk 1 role
        $this->role = Role::whereNotIn('id', [1, 2])->get(); //pengecuali untuk beberapa role
        $this->thisMonth = today()->format('Y-m');
    }
}
