<?php

namespace App\Livewire\Jadwal;

use App\Models\Jadwal;
use App\Models\JamKerja;
use App\Models\User;
use Livewire\Component;

class Update extends Component
{
    public $userId;
    public $roleId;
    public $nama_user;
    public $tanggal;
    public $jamKerjaList = [];

    public function mount()
    {
        $this->jamKerjaList = collect();
    }

    #[\Livewire\Attributes\On('getupdatejadwal')]
    public function getUpdateJadwal($userId, $tanggal, $roleId)
    {
        $this->userId = $userId;
        $this->roleId = $roleId;
        $this->nama_user = User::where('id', $userId)->with(['biodata','dokter'])->first();
        logger('nama_user set: ' . ($this->nama_user?->biodata?->nama_lengkap ?? 'NULL'));
        $this->tanggal = $tanggal;
        $this->jamKerjaList = JamKerja::whereHas('jamkerjarole', function ($query) use ($roleId) {
            $query->where('role_id', $roleId);
        })->get();
        $this->dispatch('open-modal-shift');
    }

    public function saveShift($jamKerjaId)
    {
        $jamKerjaBaru = JamKerja::find($jamKerjaId);

        $jadwalLama = Jadwal::where('user_id', $this->userId)
            ->where('tanggal', $this->tanggal)
            ->with('jamkerja')
            ->first();

        $tipeShiftLama = $jadwalLama?->jamkerja?->tipe_shift;

        $jadwal = Jadwal::updateOrCreate(
            ['user_id' => $this->userId, 'tanggal' => $this->tanggal],
            ['jamkerja_id' => $jamKerjaId]
        );

        $jadwal->load('jamkerja');

        $this->dispatch('shift-updated',
            userId: $this->userId,
            tanggal: $this->tanggal,
            jadwal: $jadwal->toArray(),
            tipeShiftLama: $tipeShiftLama,
            tipeShiftBaru: $jamKerjaBaru->tipe_shift,
        );

        $this->dispatch('close-modal-shift');
    }

    public function hapusShift()
    {
        $jadwalLama = Jadwal::where('user_id', $this->userId)
            ->where('tanggal', $this->tanggal)
            ->with('jamkerja')
            ->first();

        $tipeShiftLama = $jadwalLama?->jamkerja?->tipe_shift;

        Jadwal::where('user_id', $this->userId)
            ->where('tanggal', $this->tanggal)
            ->delete();

        $this->dispatch('shift-updated',
            userId: $this->userId,
            tanggal: $this->tanggal,
            jadwal: null,
            tipeShiftLama: $tipeShiftLama,
            tipeShiftBaru: null,
        );

        $this->dispatch('close-modal-shift');
    }

    public function render()
    {
        return view('livewire.jadwal.update');
    }
}
