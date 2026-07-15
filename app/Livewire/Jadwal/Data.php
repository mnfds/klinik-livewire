<?php

namespace App\Livewire\Jadwal;

use App\Models\Role;
use Livewire\Component;

class Data extends Component
{
    public $role;
    public $thisMonth;
    public $selectedRole;

    public function render()
    {
        return view('livewire.jadwal.data');
    }

    public function mount()
    {
        $this->role = Role::all();
        $this->thisMonth = today()->format('Y-m');
    }
}
