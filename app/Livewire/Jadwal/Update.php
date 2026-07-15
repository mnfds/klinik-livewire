<?php

namespace App\Livewire\Jadwal;

use App\Models\Jadwal;
use App\Models\JamKerja;
use Livewire\Component;

class Update extends Component
{
    public $userId;
    public $tanggal;
    public $jamKerjaList = [];

    public function mount()
    {
        $this->jamKerjaList = JamKerja::all();
    }

    #[\Livewire\Attributes\On('getupdatejadwal')]
    public function getUpdateJadwal($userId, $tanggal)
    {
        $this->userId = $userId;
        $this->tanggal = $tanggal;
        $this->dispatch('open-modal-shift');
    }

    public function saveShift($jamKerjaId)
    {
        $jadwal = Jadwal::updateOrCreate(
            ['user_id' => $this->userId, 'tanggal' => $this->tanggal],
            ['jamkerja_id' => $jamKerjaId]
        );

        // kabari parent Table biar update in-memory / refresh grid
        $this->dispatch('shift-updated', userId: $this->userId, tanggal: $this->tanggal, jadwal: $jadwal->load('jamkerja')->toArray());
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Jadwal Kerja Berhasil Ditambahkan.'
        ]);
        $this->dispatch('close-modal-shift');
    }

    public function hapusShift()
    {
        Jadwal::where('user_id', $this->userId)->where('tanggal', $this->tanggal)->delete();

        $this->dispatch('shift-updated', userId: $this->userId, tanggal: $this->tanggal, jadwal: null);
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Jadwal Kerja Berhasil Dihapus.'
        ]);
        $this->dispatch('close-modal-shift');
    }

    public function render()
    {
        return view('livewire.jadwal.update');
    }
}
