<?php

namespace App\Livewire\Pasien;

use App\Models\Pasien;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\getAccessToken;
use App\Services\GetPatientByNik;
use App\Services\GetPatientByNikBirthdateGender;
use App\Services\SatuSehatService;
use Illuminate\Support\Facades\Log;

class CreateByNama extends Component
{
    use WithFileUploads;
    public $token; // Token satu sehat

    public $nik;
    public $no_ihs;
    public $name; // simpan nama dengan sensor dari satusehat
    
    public $nama; // input nama tanpa sensor
    public $huruf_awal;
    public $no_register;
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

    public function mount(getAccessToken $tokenService)
    {
        $this->token = $tokenService->getAccessToken();
    }
    
    public function render()
    {
        return view('livewire.pasien.create-by-nama');
    }

    public function searchNameBirtdateGender(GetPatientByNikBirthdateGender $patientService)
    {
        $this->validate([
            'nama' => 'required|string|min:2',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Wanita',
        ]);

        $gender = match ($this->jenis_kelamin) {
            'Laki-laki' => 'male',
            'Wanita' => 'female',
            default => null,
        };

        try {
            $patient = $patientService->handle(
                $this->nama,
                $this->tanggal_lahir,
                $gender
            );

            $this->no_ihs = $patient['no_ihs'];
            $this->name = $patient['nama'];

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Data Pasien Berhasil Ditemukan Pada Satu Sehat.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Data Pasien Tidak Ditemukan Pada Satu Sehat.',
            ]);
        }
    }

    public function generateNoRegister()
    {
        $this->validate([
            'no_register' => 'required|alpha|size:1',
        ]);

        $prefix = strtoupper($this->no_register);
        $count = \App\Models\Pasien::where('no_register', 'like', $prefix . '-%')->count();
        $this->no_register = $prefix . '-' . ($count + 1);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'No Rekam Medis Berhasil Dibuat',
        ]);

    }  

    public function store()
    {
        
        $this->validate([
            'no_register'     => 'required|string|max:255',
            'nik'             => 'required|string|max:255',
            'no_ihs'          => 'required|string|max:255',
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
