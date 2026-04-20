<?php

namespace App\Livewire\Role;

use App\Models\Akses;
use App\Models\User;
use App\Models\UserAkses;
use Livewire\Component;

class Data extends Component
{
    public $selectedUserId = null;
    public $selectedUser = null;
    public $aksesGrouped = [];
    public $roleAkses = [];     // akses dari role user
    public $individu = [];      // akses individual user

    public function updatedSelectedUserId($value)
    {
        if (!$value) {
            $this->reset(['selectedUser', 'aksesGrouped', 'roleAkses', 'individu']);
            return;
        }

        $this->selectedUser = User::with([
            'role.aksesrole',
            'userakses'
        ])->find($value);

        if (!$this->selectedUser) return;

        // Langsung pluck akses_id dari aksesrole
        $this->roleAkses = $this->selectedUser->role
            ? $this->selectedUser->role->aksesrole->pluck('akses_id')->toArray()
            : [];

        // Langsung pluck akses_id dari userakses
        $this->individu = $this->selectedUser->userakses
            ->pluck('akses_id')
            ->toArray();

        $grouped = Akses::orderBy('nomor_group_akses')
            ->orderBy('id')
            ->get()
            ->groupBy('nomor_group_akses')
            ->map(fn($items) => $items->toArray())
            ->toArray();

        // Urutkan key (nomor_group_akses) dari kecil ke besar
        ksort($grouped);

        $this->aksesGrouped = $grouped;
    }

    public function toggleAkses(int $aksesId)
    {
        if (!$this->selectedUser) return;

        // Kalau akses dari role, tidak bisa diubah
        if (in_array($aksesId, $this->roleAkses)) return;

        $existing = UserAkses::where('user_id', $this->selectedUser->id)
            ->where('akses_id', $aksesId)
            ->first();

        if ($existing) {
            $existing->delete();
            $this->individu = array_diff($this->individu, [$aksesId]);
        } else {
            UserAkses::create([
                'user_id' => $this->selectedUser->id,
                'akses_id' => $aksesId,
            ]);
            $this->individu[] = $aksesId;
        }
    }

    public function render()
    {
        return view('livewire.role.data', [
            'users' => User::with('role')->orderBy('name')->get(),
        ]);
    }
}
