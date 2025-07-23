<?php

namespace App\Livewire\Pasien;

use App\Models\Pasien;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Update extends Component
{
    use WithFileUploads;

    public $pasienId;

    public $no_register, $nik, $no_ihs, $nama, $alamat, $no_telp;
    public $jenis_kelamin, $agama, $profesi, $tanggal_lahir, $status;
    public $foto_pasien, $new_foto_pasien, $foto_pasien_preview;
    public $deskripsi;

    public function mount($id)
    {
        $this->pasienId = $id;

        $pasien = Pasien::findOrFail($id);

        // Isi field-field dari model
        $this->no_register         = $pasien->no_register;
        $this->nik                 = $pasien->nik;
        $this->no_ihs              = $pasien->no_ihs;
        $this->nama                = $pasien->nama;
        $this->alamat              = $pasien->alamat;
        $this->no_telp             = $pasien->no_telp;
        $this->jenis_kelamin       = $pasien->jenis_kelamin;
        $this->agama               = $pasien->agama;
        $this->profesi             = $pasien->profesi;
        $this->tanggal_lahir       = $pasien->tanggal_lahir;
        $this->status              = $pasien->status;
        $this->foto_pasien         = $pasien->foto_pasien;
        $this->foto_pasien_preview = $pasien->foto_pasien;
        $this->deskripsi           = $pasien->deskripsi;
    }

    public function generateNoRegister()
    {
        $this->validate([
            'no_register' => 'required|alpha|size:1',
        ]);

        $prefix = strtoupper($this->no_register);

        $count = Pasien::where('no_register', 'like', "$prefix-%")->count();

        $this->no_register = $prefix . '-' . ($count + 1);
    }

    public function render()
    {
        return view('livewire.pasien.update');
    }

    public function update()
    {
        // Validasi input
        $validated = $this->validate([
            'no_register'     => 'required|string|max:10',
            'nik'             => 'nullable|string|max:20',
            'no_ihs'          => 'nullable|string|max:20',
            'nama'            => 'required|string|max:100',
            'alamat'          => 'nullable|string|max:255',
            'no_telp'         => 'nullable|string|max:20',
            'jenis_kelamin'   => 'required|in:Laki-laki,Wanita',
            'agama'           => 'nullable|string|max:50',
            'profesi'         => 'nullable|string|max:50',
            'tanggal_lahir'   => 'nullable|date',
            'status'          => 'nullable|string|max:50',
            'deskripsi'       => 'nullable|string|max:500',
            'foto_pasien'     => 'nullable|image|max:1024', // maksimal 1MB
        ]);

        // Ambil data pasien yang akan diupdate
        $pasien = Pasien::findOrFail($this->pasienId);

        // Simpan foto baru jika ada
        if ($this->foto_pasien) {
            // Hapus foto lama jika ada
            if ($pasien->foto_pasien && Storage::disk('public')->exists($pasien->foto_pasien)) {
                Storage::disk('public')->delete($pasien->foto_pasien);
            }

            // Simpan foto baru
            $path = $this->foto_pasien->store('foto_pasien', 'public');
            $validated['foto_pasien'] = $path;
        } else {
            // Jika tidak upload baru, gunakan yang lama
            $validated['foto_pasien'] = $pasien->foto_pasien;
        }

        // Update data pasien
        $pasien->update($validated);

        // Notifikasi atau redirect
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data pasien berhasil diperbarui.',
        ]);
        return redirect()->route('pasien.data');
    }

}
