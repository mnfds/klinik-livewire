<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

class DataUsers extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search']; // agar pencarian tetap di URL

    public function updatingSearch()
    {
        $this->resetPage(); // reset ke halaman 1 saat search berubah
    }

    public function render()
    {
        if (! Gate::allows('akses', 'Staff Data')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }

        $users = User::with('biodata')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhereHas('biodata', function ($q) {
                          $q->where('nama_lengkap', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.users.data-users', compact('users'));
    }
}
