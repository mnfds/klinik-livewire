<?php

namespace App\Livewire\Users;

use App\Models\Jadwal;
use App\Models\Kuotalibur;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UpdateUsers extends Component
{
    use WithFileUploads;
    public User $user;

    // $name -> username
    public $name, $email, $password, $role_id;
    public $nama_lengkap, $nik, $ihs, $telepon, $alamat, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $mulai_bekerja;
    public $foto_wajah, $foto_wajah_preview;
    public $nama_kerabat, $status_kerabat, $telepon_kerabat;
    public $user_code_qr;
    public $kuota_sisa,$kuota_diberi, $tanggal;
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
        $this->tanggal_lahir = $biodata->tanggal_lahir ?? null;
        $this->jenis_kelamin = $biodata->jenis_kelamin ?? '';
        $this->mulai_bekerja = $biodata->mulai_bekerja ?? null;
        $this->foto_wajah_preview = $biodata->foto_wajah ?? null;
        $this->nama_kerabat = $biodata->nama_kerabat ?? '';
        $this->telepon_kerabat = $biodata->telepon_kerabat ?? '';
        $this->status_kerabat = $biodata->status_kerabat ?? '';
        $this->user_code_qr = $biodata->user_code_qr ?? '';

        $this->roles = \App\Models\Role::orderBy('nama_role')->pluck('nama_role', 'id')->toArray();
        // dd([$user,$biodata]);
    }
    
    public function generateQrCodeUser(): string
    {
        return QrCode::size(200)
            ->errorCorrection('H')
            ->generate($this->user_code_qr);
    }

    public function render()
    {
        return view('livewire.users.update-users',[
            'qrUserImage' => $this->generateQrCodeUser(),
        ]);
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
            'nama_kerabat'   => 'nullable|string|max:255',
            'telepon_kerabat'=> 'nullable|string|max:20',
            'status_kerabat' => 'nullable|string',
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
            'nama_kerabat'   => $this->nama_kerabat,
            'telepon_kerabat'=> $this->telepon_kerabat,
            'status_kerabat' => $this->status_kerabat,
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
        dd($user);
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

    private function hitungTerpakai($userId, $bulan, $tahun)
    {
        $today = today();
        $bulanIni = Carbon::create($tahun, $bulan, 1);

        $cutoff = match (true) {
            $bulanIni->isSameMonth($today) && $bulanIni->isSameYear($today) => $today,
            $bulanIni->lt($today) => $bulanIni->copy()->endOfMonth(),
            default => $bulanIni->copy()->startOfMonth()->subDay(),
        };

        return Jadwal::where('user_id', $this->user->id)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->whereDate('tanggal', '<=', $cutoff)
            ->whereHas('jamkerja', fn ($q) => $q->where('tipe_shift', 'libur'))
            ->count();
    }

    public function updatedTanggal($value)
    {
        if (! $value) {
            return;
        }

        $bulanDipilih = Carbon::createFromFormat('Y-m', $value);
        $today = today();

        // Batasi: hanya boleh input untuk bulan berjalan
        if (! $bulanDipilih->isSameMonth($today) || ! $bulanDipilih->isSameYear($today)) {
            $this->addError('tanggal', 'Kuota libur hanya bisa diinput untuk bulan berjalan (' . $today->format('F Y') . ').');
            $this->reset(['tanggal', 'kuota_diberi', 'kuota_sisa']);
            return;
        }

        $bulanLalu = $bulanDipilih->copy()->subMonth();

        $kuotaLalu = Kuotalibur::where('user_id', $this->user->id)
            ->where('bulan', $bulanLalu->month)
            ->where('tahun', $bulanLalu->year)
            ->first();

        $dimilikiLalu = $kuotaLalu->kuota_dimiliki ?? 0;
        $sisaCarryLalu = $kuotaLalu->kuota_sisa_bulan_sebelumnya ?? 0;
        $totalLalu = $dimilikiLalu + $sisaCarryLalu;

        // aman, karena $bulanLalu pasti sudah lewat sepenuhnya
        $terpakaiLalu = $this->hitungTerpakai($this->user->id, $bulanLalu->month, $bulanLalu->year);

        $this->kuota_sisa = max(0, $totalLalu - $terpakaiLalu);

        $kuotaBulanIni = Kuotalibur::where('user_id', $this->user->id)
            ->where('bulan', $bulanDipilih->month)
            ->where('tahun', $bulanDipilih->year)
            ->first();

        if ($kuotaBulanIni) {
            $this->kuota_diberi = $kuotaBulanIni->kuota_dimiliki;
            $this->kuota_sisa = $kuotaBulanIni->kuota_sisa_bulan_sebelumnya;
        }
    }

    public function storeLibur()
    {
        $this->validate([
            'tanggal' => 'required',
            'kuota_diberi' => 'required|integer|min:0',
            'kuota_sisa' => 'required|integer|min:0',
        ]);

        $bulanDipilih = Carbon::createFromFormat('Y-m', $this->tanggal);
        $today = today();

        if (! $bulanDipilih->isSameMonth($today) || ! $bulanDipilih->isSameYear($today)) {
            $this->addError('tanggal', 'Kuota libur hanya bisa diinput untuk bulan berjalan.');
            return;
        }

        Kuotalibur::updateOrCreate(
            [
                'user_id' => $this->user->id,
                'bulan' => $bulanDipilih->month,
                'tahun' => $bulanDipilih->year,
            ],
            [
                'kuota_dimiliki' => $this->kuota_diberi,
                'kuota_sisa_bulan_sebelumnya' => $this->kuota_sisa,
            ]
        );

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Kuota Libur berhasil ditambahkan.'
        ]);
        $this->reset(['tanggal', 'kuota_diberi', 'kuota_sisa']);
    }
}