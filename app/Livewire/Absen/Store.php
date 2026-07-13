<?php

namespace App\Livewire\Absen;

use App\Models\Absen;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Store extends Component
{
    public $user_id, $tanggal_absen, $jam_masuk, $jam_pulang, $keterangan;
    public $staff;

    public function mount(){
        $this->staff = User::with('biodata')->whereHas('biodata')->get();
    }

    public function store()
    {
        $this->validate([
            'user_id' => 'required',
            'tanggal_absen' => 'required|date',
            'jam_masuk' => 'required',
            'jam_pulang' => 'nullable',
            'keterangan' => 'nullable',
        ]);

        if (! Gate::allows('akses', 'Jadwal')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        Absen::create([
            'user_id'        => $this->user_id,
            'tanggal_absen'  => $this->tanggal_absen,
            'jam_masuk'      => $this->jam_masuk,
            'jam_pulang'     => $this->jam_pulang,
            'keterangan'     => $this->keterangan,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Absen Berhasil Ditambahkan'
        ]);
        $this->dispatch('storeAbsen');
        $this->reset();
        return redirect()->route('absen.data');
    }

    public function render()
    {
        return view('livewire.absen.store');
    }
}
