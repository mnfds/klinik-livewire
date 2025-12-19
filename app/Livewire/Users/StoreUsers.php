<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Biodata;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Gate;

class StoreUsers extends Component
{
    use WithFileUploads;

    public $roles = [];
    // Untuk tabel users
    public $name, $email, $password, $role_id;

    // Untuk tabel biodata
    public $nama_lengkap, $nik, $ihs, $telepon, $alamat, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $mulai_bekerja;

    // Foto
    public $foto_wajah;
    public $foto_wajah_preview;

    public function mount()
    {
        $this->roles = \App\Models\Role::orderBy('nama_role')->pluck('nama_role', 'id')->toArray();
    }

    public function render()
    {
        return view('livewire.users.store-users');
    }

    public function store()
    {
            // dd([
            //     'name' => $this->name,
            //     'email' => $this->email,
            //     'password' => $this->password,
            //     'role_id' => $this->role_id,
            //     'nama_lengkap' => $this->nama_lengkap,
            //     'telepon' => $this->telepon,
            //     'alamat' => $this->alamat,
            //     'tempat_lahir' => $this->tempat_lahir,
            //     'tanggal_lahir' => $this->tanggal_lahir,
            //     'jenis_kelamin' => $this->jenis_kelamin,
            //     'mulai_bekerja' => $this->mulai_bekerja,
            //     'foto_wajah' => $this->foto_wajah,
            // ]);
        $this->validate([
            'name'           => ['required', 'string', 'max:255', 'unique:users'],
            'email'          => ['required', 'email', 'unique:users'],
            'password'       => ['required', 'min:6'],
            'role_id'        => ['nullable'],

            'nama_lengkap'   => ['required', 'string', 'max:255'],
            'nik'            => ['nullable', 'string'],
            'ihs'            => ['nullable', 'string', 'max:255'],
            'telepon'        => ['nullable', 'string', 'max:20'],
            'alamat'         => ['nullable', 'string'],
            'tempat_lahir'   => ['nullable', 'string'],
            'tanggal_lahir'  => ['nullable', 'date'],
            'jenis_kelamin'  => ['nullable', Rule::in(['L', 'P'])],
            'mulai_bekerja'  => ['nullable', 'date'],

            'foto_wajah'     => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);
        
        if (! Gate::allows('akses', 'Staff Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        // Upload foto jika ada
        $fotoPath = null;
        if ($this->foto_wajah) {
            $fotoPath = $this->foto_wajah->store('foto_wajah', 'public');
        }

        // Buat user baru
        $user = User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
            'role_id'  => $this->role_id,
        ]);
        event(new Registered($user));

        // Buat biodata
        $user->biodata()->create([
            'nama_lengkap'  => $this->nama_lengkap,
            'nik'           => $this->nik,
            'ihs'           => $this->ihs,
            'telepon'       => $this->telepon,
            'alamat'        => $this->alamat,
            'tempat_lahir'  => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir,
            'jenis_kelamin' => $this->jenis_kelamin,
            'mulai_bekerja' => $this->mulai_bekerja,
            'foto_wajah'    => $fotoPath,
        ]);

        // Flash message & reset
        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Data user berhasil disimpan.',
        ]);

        $this->resetForm();

        // Redirect ke index (optional)
        return redirect()->route('users.data');
    }

    public function resetForm()
    {
        $this->reset([
            'name', 'email', 'password', 'role_id',
            'nama_lengkap', 'telepon', 'alamat', 'tempat_lahir', 'tanggal_lahir',
            'jenis_kelamin', 'mulai_bekerja',
            'foto_wajah', 'foto_wajah_preview',
            'nik', 'ihs',
        ]);
    }
}
