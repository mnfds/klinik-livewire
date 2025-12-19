<?php

namespace App\Livewire\Jamkerja;

use Livewire\Component;
use App\Models\JamKerja;
use Illuminate\Support\Facades\Gate;

class Update extends Component
{
    public $jamKerjaId;

    public $nama_shift;
    public $tipe_shift;
    public $jam_mulai;
    public $jam_selesai;
    public $lewat_hari;

    #[\Livewire\Attributes\On('getupdate')]
    public function getupdate($rowId): void
    {
        $this->jamKerjaId = $rowId;

        $jamKerja = JamKerja::findOrFail($rowId);

        $this->nama_shift   = $jamKerja->nama_shift;
        $this->tipe_shift   = $jamKerja->tipe_shift;
        $this->jam_mulai    = $jamKerja->jam_mulai;
        $this->jam_selesai  = $jamKerja->jam_selesai;
        $this->lewat_hari   = $jamKerja->lewat_hari;

        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate([
            'nama_shift'   => 'nullable|string',
            'tipe_shift'   => 'nullable|in:full,pagi,siang,malam,libur,mp',
            'jam_mulai'    => 'nullable|string',
            'jam_selesai'  => 'nullable|string',
            'lewat_hari'   => 'boolean',
        ]);
        
        if (! Gate::allows('akses', 'Jam Kerja Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $jamKerja = JamKerja::findOrFail($this->jamKerjaId);

        $jamKerja->forcefill([
            'nama_shift'   => $this->nama_shift,
            'tipe_shift'   => $this->tipe_shift,
            'jam_mulai'    => $this->jam_mulai,
            'jam_selesai'  => $this->jam_selesai,
            'lewat_hari'   => $this->lewat_hari,
        ])->save();

        // dd($jamKerja);

        // 🔔 Toast alert
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);

        // ❌ Tutup modal via JS
        $this->dispatch('closeModal');

        // 🔄 Reset form
        $this->reset();
        
        return redirect()->route('jamkerja.data');
    }

    public function render()
    {
        return view('livewire.jamkerja.update');
    }
}
