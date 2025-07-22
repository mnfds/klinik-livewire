<?php

namespace App\Livewire\Pasien;

use App\Models\Pasien;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateDefault extends Component
{
    use WithFileUploads;

    public $huruf_awal;
    public $no_register;
    public $nik;
    public $no_ihs;
    public $nama;
    public $alamat;
    public $no_telp;
    public $jenis_kelamin;
    public $agama;
    public $profesi;
    public $tanggal_lahir;
    public $status;
    public $foto_pasien;
    public $deskripsi;

    public $foto_pasien_preview;

    public function render()
    {
        return view('livewire.pasien.create-default');
    }

    public function generateNoRegister()
    {
        $this->validate([
            'no_register' => 'required|alpha|size:1',
        ]);

        $prefix = strtoupper($this->no_register);
        $count = \App\Models\Pasien::where('no_register', 'like', $prefix . '-%')->count();
        $this->no_register = $prefix . '-' . ($count + 1);
    }

    public function store()
    {
        
        $this->validate([
            'no_register'     => 'required|string|max:255',
            'nik'             => 'nullable|string|max:255',
            'no_ihs'          => 'nullable|string|max:255',
            'nama'            => 'required|string|max:255',
            'alamat'          => 'nullable|string|max:255',
            'no_telp'         => 'nullable|string|max:255',
            'jenis_kelamin'   => 'required|in:Laki-laki,Wanita',
            'agama'           => 'nullable|string|max:255',
            'profesi'         => 'nullable|string|max:255',
            'tanggal_lahir'   => 'nullable|date',
            'status'          => 'nullable|string|max:255',
            'foto_pasien'     => 'nullable|image|max:2048', // maksimal 2MB
            'deskripsi'       => 'nullable|string',
        ]);

        // Upload foto jika ada
        $fotoPath = null;
        if ($this->foto_pasien) {
            $fotoPath = $this->foto_pasien->store('foto_pasien', 'public');
        }

        Pasien::create([
            'no_register'   => $this->no_register,
            'nik'           => $this->nik,
            'no_ihs'        => $this->no_ihs,
            'nama'          => $this->nama,
            'alamat'        => $this->alamat,
            'no_telp'       => $this->no_telp,
            'jenis_kelamin' => $this->jenis_kelamin,
            'agama'         => $this->agama,
            'profesi'       => $this->profesi,
            'tanggal_lahir' => $this->tanggal_lahir,
            'status'        => $this->status,
            'foto_pasien'   => $fotoPath,
            'deskripsi'     => $this->deskripsi,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data pasien berhasil ditambahkan.',
        ]);

        $this->reset();

        return redirect()->route('pasien.data');
    }
}
