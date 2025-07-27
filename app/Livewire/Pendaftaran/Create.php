<?php

namespace App\Livewire\Pendaftaran;

use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\PasienTerdaftar;
use App\Models\PoliKlinik;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public ?int $id = null;
    public ?Pasien $pasien = null;
    public $poli;
    public $dokter;

    // Properti untuk input form
    public $no_register, $nik, $no_ihs, $nama, $alamat, $no_telp;
    public $jenis_kelamin, $agama, $profesi, $tanggal_lahir;
    public $status, $foto_pasien, $deskripsi;

    public $jenis_kunjungan, $tanggal_kunjungan;
    public $poli_id, $dokter_id;
    public $foto_pasien_preview; //show

    public function mount($id = null)
    {
        $this->id = $id;

        $this->poli = PoliKlinik::where('status', true)->get();
        $this->dokter = Dokter::all();
        if ($this->id) {
            
            $this->pasien = Pasien::find($this->id);

            if ($this->pasien) {
                $this->no_register     = $this->pasien->no_register;
                $this->nik             = $this->pasien->nik;
                $this->no_ihs          = $this->pasien->no_ihs;
                $this->nama            = $this->pasien->nama;
                $this->alamat          = $this->pasien->alamat;
                $this->no_telp         = $this->pasien->no_telp;
                $this->jenis_kelamin   = $this->pasien->jenis_kelamin;
                $this->agama           = $this->pasien->agama;
                $this->profesi         = $this->pasien->profesi;
                $this->tanggal_lahir   = $this->pasien->tanggal_lahir;
                $this->status          = $this->pasien->status;
                $this->foto_pasien_preview     = $this->pasien->foto_pasien;
                $this->deskripsi       = $this->pasien->deskripsi;
            }
        }
    }

    public function submit()
    {
        $validatedData = $this->validate([
            'id'                => 'required',
            'poli_id'           => 'required',
            'tanggal_kunjungan' => 'required|date',
            'jenis_kunjungan'   => 'required|in:sehat,sakit',
        ]);

        PasienTerdaftar::create([
            'pasien_id'         => $this->id,
            'poli_id'           => $this->poli_id,
            'tanggal_kunjungan' => $this->tanggal_kunjungan,
            'jenis_kunjungan'   => $this->jenis_kunjungan,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pasien berhasil terdaftar.'
        ]);

        return redirect()->route('pendaftaran.data'); // atau ke route tujuanmu
    }

    public function render()
    {
        return view('livewire.pendaftaran.create');
    }
}
