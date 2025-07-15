<?php

namespace App\Livewire\JamKerja;

use App\Models\JamKerja;
use Livewire\Component;

class StoreJamKerja extends Component
{
    public $nama_shift;
    public $tipe_shift;
    public $jam_mulai;
    public $jam_selesai;
    public $lewat_hari = false;

    public function render()
    {
        return view('livewire.jam-kerja.store-jam-kerja');
    }

    public function store()
    {
        $this->validate([
            'nama_shift'   => 'nullable|string',
            'tipe_shift'   => 'nullable|in:full,pagi,siang,malam,libur,mp',
            'jam_mulai'    => 'nullable|string',
            'jam_selesai'  => 'nullable|string',
            'lewat_hari'   => 'boolean',
        ]);

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
