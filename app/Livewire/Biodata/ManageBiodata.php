<?php

namespace App\Livewire\Biodata;

use App\Models\Biodata;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\TemporaryUploadedFile;

class ManageBiodata extends Component
{
    use WithFileUploads;

    public $nama_lengkap, $nik, $ihs, $alamat, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $telepon, $mulai_bekerja;

    public $foto_wajah; // untuk file upload baru
    public $foto_wajah_preview; // untuk tampilan foto lama

    public function mount()
    {
        $biodata = Auth::user()->biodata;

        if ($biodata) {
            $this->nama_lengkap = $biodata->nama_lengkap;
            $this->nik = $biodata->nik;
            $this->ihs = $biodata->ihs;
            $this->alamat = $biodata->alamat;
            $this->tempat_lahir = $biodata->tempat_lahir;
            $this->tanggal_lahir = $biodata->tanggal_lahir;
            $this->jenis_kelamin = $biodata->jenis_kelamin;
            $this->telepon = $biodata->telepon;
            $this->mulai_bekerja = $biodata->mulai_bekerja;
            $this->foto_wajah_preview = $biodata->foto_wajah;
        }
    }

    public function save()
    {
        $this->validate([
            'nama_lengkap'   => 'required|string|max:255',
            'nik'            => 'nullable|string',
            'ihs'            => 'nullable|string',
            'alamat'         => 'nullable|string|max:255',
            'tempat_lahir'   => 'nullable|string|max:255',
            'tanggal_lahir'  => 'nullable|date',
            'jenis_kelamin'  => 'required|in:L,P',
            'telepon'        => 'nullable|string|max:20',
            'mulai_bekerja'  => 'nullable|date',
            'foto_wajah'     => 'nullable|image|max:1024', // max 1MB
        ]);

        $user = Auth::user();
        $biodata = $user->biodata;

        $data = [
            'nama_lengkap'   => $this->nama_lengkap,
            'nik'            => $this->nik,
            'ihs'            => $this->ihs,
            'alamat'         => $this->alamat,
            'tempat_lahir'   => $this->tempat_lahir,
            'tanggal_lahir'  => $this->tanggal_lahir,
            'jenis_kelamin'  => $this->jenis_kelamin,
            'telepon'        => $this->telepon,
            'mulai_bekerja'  => $this->mulai_bekerja,
        ];

        // Simpan file foto jika diunggah
        if ($this->foto_wajah) {
            // Hapus foto lama jika ada
            if ($biodata && $biodata->foto_wajah && Storage::disk('public')->exists($biodata->foto_wajah)) {
                Storage::disk('public')->delete($biodata->foto_wajah);
            }

            // Simpan foto baru
            $path = $this->foto_wajah->store('foto_wajah', 'public');
            $data['foto_wajah'] = $path;
        }

        if ($biodata) {
            $biodata->update($data);
        } else {
            $data['user_id'] = $user->id;
            Biodata::create($data);
        }

        session()->flash('message', 'Biodata berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.biodata.manage-biodata');
    }
}
