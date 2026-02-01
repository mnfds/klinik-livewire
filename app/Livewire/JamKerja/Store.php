<?php

namespace App\Livewire\Jamkerja;

use Livewire\Component;
use App\Models\JamKerja;
use Illuminate\Support\Facades\Gate;

class Store extends Component
{
    public $nama_shift;
    public $tipe_shift;
    public $jam_mulai;
    public $jam_selesai;
    public $lewat_hari = false;

    public function render()
    {
        return view('livewire.jamkerja.store');
    }

    public function store()
    {
        $this->validate([
            'nama_shift'   => 'required|string',
            'tipe_shift'   => 'nullable|in:full,pagi,siang,malam,libur,mp',
            'jam_mulai'    => 'required|string',
            'jam_selesai'  => 'required|string',
            'lewat_hari'   => 'boolean',
        ]);
        if (! Gate::allows('akses', 'Jam Kerja Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        JamKerja::create([
            'nama_shift'   => $this->nama_shift,
            'tipe_shift'   => $this->tipe_shift,
            'jam_mulai'    => $this->jam_mulai,
            'jam_selesai'  => $this->jam_selesai,
            'lewat_hari'   => $this->lewat_hari,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Jam Kerja berhasil ditambahkan.'
        ]);

        $this->dispatch('closeStoreModal');

        $this->reset();

        return redirect()->route('jamkerja.data');
    }
}
