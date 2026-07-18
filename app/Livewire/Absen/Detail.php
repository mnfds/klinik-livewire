<?php

namespace App\Livewire\Absen;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
        if (! Gate::allows('akses', 'Absen Detail')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.absen.detail');
    }
}
