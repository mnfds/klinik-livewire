<?php

namespace App\Livewire\Absen;

use App\Models\User;
use Livewire\Component;

class Detail extends Component
{
    public $userId;
    public $nama_staff;

    public function mount($id): void
    {
        $this->userId = $id;
        $this->nama_staff = User::find($id)?->biodata?->nama_lengkap ?? '-';
    }

    public function render()
    {
        return view('livewire.absen.detail');
    }
}
