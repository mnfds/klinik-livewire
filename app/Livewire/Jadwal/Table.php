<?php

namespace App\Livewire\Jadwal;

use App\Models\Jadwal;
use App\Models\JamKerja;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Table extends Component
{
    public $bulan;
    public $role;
    public $users;
    public $jadwal;
    public $tanggal;
    public $editUserId = null;
    public $editTanggal = null;
    public $jamKerjaList = [];

    public function render()
    {
        return view('livewire.jadwal.table');
    }

    public function mount($bulan, $role)
    {
        $this->bulan = $bulan;
        $this->role = $role ?: auth()->User()->role->nama_role;

        $roleId = Role::where('nama_role', $this->role)->value('id');
        $this->users = User::where('role_id', $roleId)->with(['biodata', 'dokter'])->get();

        $this->tanggal = Carbon::createFromFormat('Y-m', $this->bulan);
        $this->jadwal = Jadwal::whereIn('user_id', $this->users->pluck('id'))
            ->whereYear('tanggal', $this->tanggal->year)
            ->whereMonth('tanggal', $this->tanggal->month)
            ->with('jamkerja')
            ->get()
            ->groupBy('user_id')
            ->map(fn ($items) => $items->toArray())
            ->toArray();

        $this->jamKerjaList = JamKerja::all();
    }

    public function editShift($userId, $tanggal)
    {
        $this->editUserId = $userId;
        $this->editTanggal = $tanggal;
        $this->dispatch('getupdatejadwal', userId: $this->editUserId, tanggal: $this->editTanggal)
        ->to(\App\Livewire\Jadwal\Update::class);
    }

    #[\Livewire\Attributes\On('shift-updated')]
    public function refreshShift($userId, $tanggal, $jadwal)
    {
        if ($jadwal) {
            $this->jadwal[$userId][$tanggal] = $jadwal;
        } else {
            unset($this->jadwal[$userId][$tanggal]);
        }
    }
}
