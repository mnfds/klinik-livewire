<?php

namespace App\Livewire\Dokter;

use App\Models\User;
use App\Models\Dokter;
use Livewire\Component;
use App\Models\DokterPoli;
use App\Models\PoliKlinik;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class Create extends Component
{
    use WithFileUploads;

    public $foto_wajah;
    public $foto_wajah_preview;
    public $ttd_digital;
    public $ttd_digital_preview;

    // Poli
    public $id_poli;
    public $poli = [];

    // Akun
    public $name;
    public $email;
    public $password;

    // Biodata Dokter
    public $nama_dokter;
    public $nik;
    public $alamat_dokter;
    public $telepon;
    public $jenis_kelamin;

    public $tingkat_pendidikan;
    public $institusi;
    public $tahun_kelulusan;

    public $ihs;
    public $no_str;
    public $surat_izin_pratik;
    public $masa_berlaku_sip;

    public function mount()
    {
        $this->poli = PoliKlinik::orderBy('nama_poli')->pluck('nama_poli', 'id')->toArray();
    }

    public function store()
    {
        $data = $this->validate([
            // Akun
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',

            // Dokter
            'nama_dokter' => 'required|string|max:255',
            'nik' => 'nullable|string|max:255',
            'alamat_dokter' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:L,P',

            'tingkat_pendidikan' => 'nullable',
            'institusi' => 'nullable',
            'tahun_kelulusan' => 'nullable',

            'ihs' => 'nullable',
            'no_str' => 'nullable',
            'surat_izin_pratik' => 'nullable',
            'masa_berlaku_sip' => 'nullable|date',

            'foto_wajah' => 'nullable|image|max:1024',
            'ttd_digital' => 'nullable|file|max:1024',
        ]);
        
        if (! Gate::allows('akses', 'Dokter Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        // dd($data);

        $fotoPath = $this->foto_wajah
            ? $this->foto_wajah->store('foto_wajah', 'public')
            : null;

        $ttdPath = $this->ttd_digital
            ? $this->ttd_digital->store('ttd_digital_dokter', 'public')
            : null;

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role_id' => 2, // Role dokter
        ]);

        event(new Registered($user));



        $dokter = Dokter::create([
            'user_id' => $user->id,
            'nama_dokter' => $this->nama_dokter,
            'nik' => $this->nik,
            'ihs' => $this->ihs,
            'alamat_dokter' => $this->alamat_dokter,
            'telepon' => $this->telepon,
            'jenis_kelamin' => $this->jenis_kelamin,
            'tingkat_pendidikan' => $this->tingkat_pendidikan,
            'institusi' => $this->institusi,
            'tahun_kelulusan' => $this->tahun_kelulusan,
            'no_str' => $this->no_str,
            'surat_izin_pratik' => $this->surat_izin_pratik,
            'masa_berlaku_sip' => $this->masa_berlaku_sip,
            'foto_wajah' => $fotoPath,
            'ttd_digital' => $ttdPath,
        ]);

        DokterPoli::create([
            'dokter_id' => $dokter->id,
            'poli_id' => $this->id_poli,
        ]);

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Data Dokter berhasil disimpan.',
        ]);

        $this->resetForm();

        return redirect()->route('dokter.data');
    }

    public function resetForm()
    {
        $this->reset([
            'name', 'email', 'password', 'id_poli',
            'nama_dokter', 'nik', 'telepon', 'alamat_dokter','jenis_kelamin',
            'tingkat_pendidikan', 'institusi', 'tahun_kelulusan',
            'no_str', 'ihs', 'surat_izin_pratik', 'masa_berlaku_sip',
            'foto_wajah', 'foto_wajah_preview',
            'ttd_digital', 'ttd_digital_preview',
        ]);
    }

    public function render()
    {
        return view('livewire.dokter.create');
    }
}