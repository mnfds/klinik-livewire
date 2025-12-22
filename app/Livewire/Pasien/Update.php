<?php

namespace App\Livewire\Pasien;

use App\Models\Pasien;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class Update extends Component
{
    use WithFileUploads;

    public $pasienId;

    public $no_register, $nik, $no_ihs, $nama, $alamat, $no_telp;
    public $jenis_kelamin, $agama, $profesi, $tanggal_lahir, $status;
    public $foto_pasien;    // path / file foto lama
    public $new_foto_pasien;         // file baru (upload)
    public $foto_pasien_preview;     // untuk preview
    public $deskripsi;

    public function mount($id)
    {
        $this->pasienId = $id;

        $pasien = Pasien::findOrFail($id);

        // Set data dari model ke properti
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

    public function update()
    {
        // Validasi input
        $validated = $this->validate([
            'no_register'      => 'required|string|max:10',
            'nik'              => 'nullable|string|max:20',
            'no_ihs'           => 'nullable|string|max:20',
            'nama'             => 'required|string|max:100',
            'alamat'           => 'nullable|string|max:255',
            'no_telp'          => 'nullable|string|max:20',
            'jenis_kelamin'    => 'required|in:Laki-laki,Wanita',
            'agama'            => 'nullable|string|max:50',
            'profesi'          => 'nullable|string|max:50',
            'tanggal_lahir'    => 'nullable|date',
            'status'           => 'nullable|string|max:50',
            'deskripsi'        => 'nullable|string|max:500',
            'new_foto_pasien'  => 'nullable|image|max:1024', // maksimal 1MB
        ]);
        
        if (! Gate::allows('akses', 'Pasien Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $pasien = Pasien::findOrFail($this->pasienId);

        // Jika ada upload baru
        if ($this->new_foto_pasien) {
            // Hapus file lama
            if ($pasien->foto_pasien && Storage::disk('public')->exists($pasien->foto_pasien)) {
                Storage::disk('public')->delete($pasien->foto_pasien);
            }

            // Simpan file baru
            $path = $this->new_foto_pasien->store('foto_pasien', 'public');
            $validated['foto_pasien'] = $path;
        } else {
            // Gunakan path lama
            $validated['foto_pasien'] = $this->foto_pasien;
        }

        // Update data
        $pasien->update($validated);

        // Beri notifikasi dan redirect
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data pasien berhasil diperbarui.',
        ]);

        return redirect()->route('pasien.data');
    }

    public function render()
    {
        return view('livewire.pasien.update');
    }
}
