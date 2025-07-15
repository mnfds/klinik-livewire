<?php

namespace App\Livewire\Role;

use App\Models\Role;
use App\Models\Akses;
use Livewire\Component;
use App\Models\RoleAkses;
use Illuminate\Support\Facades\Log;

class Updateakses extends Component
{
    public $roleAksesId;
    public $nama_role;
    public $allAkses;
    public $selectedAkses = [];

    public function mount()
    {
        // Inisialisasi kosong agar tidak null saat pertama kali render
        $this->allAkses = collect();
    }

    #[\Livewire\Attributes\On('getupdaterole')]
    public function getupdaterole($rowId): void
    {
        $this->roleAksesId = $rowId;

        $role = Role::findOrFail($rowId);
        $this->nama_role = $role->nama_role;

        // Ambil semua akses dan urutkan berdasarkan nomor grup
        $this->allAkses = Akses::orderBy('nomor_group_akses')->get();

        // Ambil akses yang sudah dimiliki role
        $this->selectedAkses = RoleAkses::where('role_id', $rowId)->pluck('akses_id')->toArray();

        // Buka modal
        $this->dispatch('openModal');
    }

    public function getGroupedAksesProperty()
    {
        // Cegah error jika $allAkses belum diisi
        return $this->allAkses
            ? $this->allAkses->groupBy('nomor_group_akses')
            : collect();
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

            // Simpan akses baru
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
                'message' => 'Terjadi kesalahan saat memperbarui akses.'
            ]);
        }

        return redirect()->route('role-akses.data');
    }

    public function render()
    {
        return view('livewire.role.updateakses');
    }
}
