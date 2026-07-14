<?php

namespace App\Livewire\Absen;

use App\Models\Absen;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Update extends Component
{
    public $absen_id;
    public $tanggal_absen, $jam_masuk, $jam_pulang, $keterangan;
    public $nama;

    #[\Livewire\Attributes\On('getUpdateAbsen')]
    public function getUpdateAbsen($rowId): void
    {
        $this->absen_id = $rowId;
        
        $absen = Absen::findOrFail($rowId);
        $this->tanggal_absen = $absen->tanggal_absen;
        $this->jam_masuk  = $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') : null;
        $this->jam_pulang = $absen->jam_pulang ? \Carbon\Carbon::parse($absen->jam_pulang)->format('H:i') : null;
        $this->keterangan    = $absen->keterangan;
        $this->nama = $absen->user->biodata->nama_lengkap;

        $this->dispatch('modalUpdateAbsen');
    }
    
    public function update()
    {
        $absen = Absen::findOrFail($this->absen_id);

        $this->validate([
            'tanggal_absen' => [
                'required',
                'date',
                Rule::unique('absens')
                    ->where(fn ($query) => $query->where('user_id', $absen->user_id))
                    ->ignore($this->absen_id),
            ],
            'jam_masuk'     => 'required',
            'jam_pulang'    => 'nullable',
            'keterangan'    => 'nullable',
        ], [
            'tanggal_absen.unique' => 'Staff ini sudah memiliki data absen pada tanggal tersebut.',
        ]);

        if (! Gate::allows('akses', 'Jadwal')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $absen->update([
            'tanggal_absen'  => $this->tanggal_absen,
            'jam_masuk'      => $this->jam_masuk,
            'jam_pulang'     => $this->jam_pulang,
            'keterangan'     => $this->keterangan,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);

        $this->dispatch('closemodalUpdateAbsen');
        $this->reset();
        return redirect()->route('absen.data'); // untuk PowerGrid refresh
    }

    public function render()
    {
        return view('livewire.absen.update');
    }
}
