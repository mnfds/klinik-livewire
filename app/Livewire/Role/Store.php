<?php

namespace App\Livewire\Role;

use App\Models\Role;
use App\Models\Akses;
use App\Models\RoleAkses;
use Livewire\Component;
use Illuminate\Support\Facades\DB;


class Store extends Component
{
    public $nama_role;
    public $selectedAkses = [];
    public $allAkses;

    public function mount()
    {
        $this->allAkses = Akses::orderBy('nomor_group_akses')->get();
    }

    public function getGroupedAksesProperty()
    {
        return $this->allAkses
            ? $this->allAkses->groupBy('nomor_group_akses')
            ->sortKeys()
            : collect();
    }
    
    public function store()
    {
        $this->validate([
            'nama_role' => 'required|string|max:255',
            'selectedAkses' => 'nullable|array',
            'selectedAkses.*' => 'exists:akses,id',
        ]);


        DB::beginTransaction();
        try {
            // 1. Simpan role baru
            $role = Role::create([
                'nama_role' => $this->nama_role,
            ]);

            // 2. Simpan akses role
            if (!empty($this->selectedAkses)) {
                foreach ($this->selectedAkses as $aksesId) {
                    RoleAkses::create([
                        'role_id' => $role->id,
                        'akses_id' => $aksesId,
                    ]);
                }
            }

            DB::commit();

             $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Role berhasil ditambahkan.'
            ]);

            $this->dispatch('closestoreModalRole');

            $this->reset();

        } catch (\Throwable $e) {
            DB::rollBack();
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Gagal menambahkan role.',
                ]);
        }
        return redirect()->route('role-akses.data');
    }

    public function render()
    {
        return view('livewire.role.store');
    }
}