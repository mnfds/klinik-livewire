<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;

class UpdateUsers extends Component
{
    use WithFileUploads;
    public User $user;

    // $name -> username
    public $name, $email, $password, $role_id;
    public $nama_lengkap, $nik, $ihs, $telepon, $alamat, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $mulai_bekerja;
    public $foto_wajah, $foto_wajah_preview;
    public $roles = [];

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role_id = $user->role_id;
        $biodata = $user->biodata;
        $this->nama_lengkap = $biodata->nama_lengkap ?? '';
        $this->nik = $biodata->nik ?? '';
        $this->ihs = $biodata->ihs ?? '';
        $this->telepon = $biodata->telepon ?? '';
        $this->alamat = $biodata->alamat ?? '';
        $this->tempat_lahir = $biodata->tempat_lahir ?? '';
        $this->tanggal_lahir = $biodata->tanggal_lahir ?? '';
        $this->jenis_kelamin = $biodata->jenis_kelamin ?? '';
        $this->mulai_bekerja = $biodata->mulai_bekerja ?? '';
        $this->foto_wajah_preview = $biodata->foto_wajah ?? null;

        $this->roles = \App\Models\Role::orderBy('nama_role')->pluck('nama_role', 'id')->toArray();
        // dd([$user,$biodata]);
    }  

    public function render()
    {
        return view('livewire.users.update-users');
    }

    public function update(Request $request, User $user)
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $this->user->id,
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'role_id' => 'required|exists:roles,id',
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'nullable',
            'ihs' => 'nullable',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:L,P',
            'mulai_bekerja' => 'nullable|date',
            'foto_wajah' => 'nullable|image|max:2048',
        ]);

        if (! Gate::allows('akses', 'Staff Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $this->role_id,
        ]);

        $fotoPath = $this->foto_wajah
            ? $this->foto_wajah->store('foto_wajah', 'public')
            : $this->foto_wajah_preview;

        if ($this->foto_wajah) {
            // Simpan foto baru
            $fotoPath = $this->foto_wajah->store('foto_wajah', 'public');

            // Hapus foto lama jika ada
            if (optional($this->user->biodata)->foto_wajah) {
                Storage::disk('public')->delete($this->user->biodata->foto_wajah);
            }
        } else {
            // Pakai foto lama
            $fotoPath = $this->foto_wajah_preview;
        }

        $this->user->biodata()->updateOrCreate([], [
            'nama_lengkap'   => $this->nama_lengkap,
            'nik'            => $this->nik,
            'ihs'            => $this->ihs,
            'telepon'        => $this->telepon,
            'alamat'         => $this->alamat,
            'tempat_lahir'   => $this->tempat_lahir,
            'tanggal_lahir'  => $this->tanggal_lahir,
            'jenis_kelamin'  => $this->jenis_kelamin,
            'mulai_bekerja'  => $this->mulai_bekerja,
            'foto_wajah'     => $fotoPath,
        ]);

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Data user berhasil diperbarui.',
        ]);
        return redirect()->route('users.data');
    }

    public function kirimUlangVerifikasi()
    {
        $user = User::find($this->user->id ?? null); // pastikan user ada
        
        if (! Gate::allows('akses', 'Verifikasi Email')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        if ($user && !$user->hasVerifiedEmail()) {
            event(new Registered($user)); // kirim ulang email verifikasi
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Email verifikasi dikirim ulang.']);
        } else {
            $this->dispatch('toast', ['type' => 'info', 'message' => 'Email sudah terverifikasi.']);
        }
    }

    public function kirimResetPassword()
    {
        $user = User::find($this->user->id ?? null); // atau berdasarkan email

        if (! Gate::allows('akses', 'Reset Password')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        if ($user) {
            Password::sendResetLink(['email' => $user->email]);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Link reset password dikirim.']);
        } else {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'User tidak ditemukan.']);
        }
    }
}