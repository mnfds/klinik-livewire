<?php

namespace App\Livewire\Role;

use App\Models\Role;
use App\Models\Akses;
use Livewire\Component;
use App\Models\RoleAkses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateAksesRole extends Component
{
    public $roleAksesId;
    public $nama_role;
    public $allAkses = [];
    public $selectedAkses = [];

    #[\Livewire\Attributes\On('editaksesrole')]
    public function editaksesrole($rowId): void
    {
        $this->roleAksesId = $rowId;
        // dd($this->roleAksesId);
        $role = Role::findOrFail($rowId);

        $this->nama_role = $role->nama_role;
        $this->allAkses = Akses::all()->toArray();
        $this->selectedAkses = RoleAkses::where('role_id', $rowId)->pluck('akses_id')->toArray();

        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate([
            'roleAksesId' => 'required|exists:roles,id',
            'selectedAkses' => 'nullable|array',
            'selectedAkses.*' => 'exists:akses,id',
        ], [
            'selectedAkses.*.exists' => 'Beberapa akses yang dipilih tidak valid.',
        ]);

        try {
            // Hapus akses lama
            RoleAkses::where('role_id', $this->roleAksesId)->delete();

            // Simpan akses yang dipilih
            if (!empty($this->selectedAkses)) {
                foreach ($this->selectedAkses as $aksesId) {
                    RoleAkses::create([
                        'role_id' => $this->roleAksesId,
                        'akses_id' => $aksesId,
                    ]);
                }
            }

            // Feedback ke user
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Hak akses berhasil diperbarui.'
            ]);
            
            $this->dispatch('closeModal');
            
            $this->reset(['roleAksesId', 'selectedAkses', 'allAkses']);

        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan akses role', [
                'role_id' => $this->roleAksesId,
                'selected_akses' => $this->selectedAkses,
                'error' => $e->getMessage(),
            ]);
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Hak akses berhasil diperbarui.'
            ]);

        }
        return redirect()->route('role-akses.data');
    }

    public function render()
    {
        return view('livewire.role.update-akses-role');
    }
}
