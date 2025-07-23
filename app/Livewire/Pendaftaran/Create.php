<?php

namespace App\Livewire\Pendaftaran;

use App\Models\Pasien;
use Livewire\Component;

class Create extends Component
{

    public ?int $id = null;
    public ?Pasien $pasien = null;

    public $nama, $nik, $alamat;

    public function mount($id = null)
    {
        $this->id = $id;

        // Optional: Ambil data berdasarkan ID jika ada
        if ($this->id) {
            $this->pasien = Pasien::find($this->id);

            if ($this->pasien) {
                // Isi form dengan data pasien
                $this->nama = $this->pasien->nama;
                $this->nik = $this->pasien->nik;
                $this->alamat = $this->pasien->alamat;
            }
        }
    }

    public function render()
    {
        return view('livewire.pendaftaran.create');
    }
}
