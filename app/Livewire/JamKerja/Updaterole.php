<?php

namespace App\Livewire\Jamkerja;

use App\Models\JamKerja;
use App\Models\JamKerjaRole;
use App\Models\Role;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Updaterole extends Component
{
    public $jamkerjaRoleId;
    public $nama_jamkerja;
    public $allRole;
    public $selectedRole = [];

    public function mount()
    {
        $this->allRole = collect();
    }

    #[\Livewire\Attributes\On('getupdaterole')]
    public function getupdaterole($rowId): void
    {
        $this->jamkerjaRoleId = $rowId;

        $jamkerja = JamKerja::findOrFail($rowId);
        $this->nama_jamkerja = $jamkerja->nama_shift;

        $this->allRole = Role::orderBy('id')->get();

        $this->selectedRole = JamKerjaRole::where('jamkerja_id', $rowId)->pluck('role_id')->toArray();

        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate([
            'jamkerjaRoleId' => 'required|exists:jam_kerjas,id',
            'selectedRole' => 'nullable|array',
            'selectedRole.*' => 'exists:roles,id',
        ], [
            'selectedRole.*.exists' => 'Beberapa Role yang dipilih tidak valid.',
        ]);

        try {
            JamKerjaRole::where('jamkerja_id', $this->jamkerjaRoleId)->delete();

            if (!empty($this->selectedRole)) {
                foreach ($this->selectedRole as $roleId) {
                    JamKerjaRole::create([
                        'jamkerja_id' => $this->jamkerjaRoleId,
                        'role_id' => $roleId,
                    ]);
                }
            }

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Role berhasil diperbarui.'
            ]);

            $this->dispatch('closeModal');

            $this->reset(['jamkerjaRoleId', 'selectedRole', 'allRole']);
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan Jam Kerja Berdasarkan Role', [
                'jamkerja_id' => $this->jamkerjaRoleId,
                'selected_role' => $this->selectedRole,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui akses.'
            ]);
        }

        return redirect()->route('jamkerja.data');

    }

    public function render()
    {
        return view('livewire.jamkerja.updaterole');
    }
}
