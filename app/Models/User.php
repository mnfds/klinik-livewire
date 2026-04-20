<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Biodata;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    public function biodata()
    {
        return $this->hasOne(Biodata::class);
    }

    public function dokter()
    {
        return $this->hasOne(Dokter::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function izinkeluar()
    {
        // USER ATAU STAFF YANG SEDANG KELUAR
        return $this->hasMany(Izinkeluar::class, 'user_id');
    }

    public function izinkeluarDisetujui()
    {
        // USER ATAU STAFF YANG MENYETUJUI IZIN UNTUK KELUAR
        return $this->hasMany(Izinkeluar::class, 'disetujui_oleh');
    }

    public function lembur()
    {
        // USER ATAU STAFF YANG SEDANG KELUAR
        return $this->hasMany(lembur::class, 'user_id');
    }

    public function lemburDisetujui()
    {
        // USER ATAU STAFF YANG MENYETUJUI IZIN UNTUK KELUAR
        return $this->hasMany(Lembur::class, 'disetujui_oleh');
    }

    public function userakses()
    {
        return $this->hasMany(UserAkses::class);
    }

    //helper untuk akses role
    public function hasAkses(string $namaAkses): bool
    {
        if (! $this->role && ! $this->userakses()->exists()) {
            return false;
        }

        // Cek akses dari role
        $dariRole = $this->role
            ? $this->role
                ->aksesrole()
                ->whereHas('akses', function ($query) use ($namaAkses) {
                    $query->where('nama_akses', $namaAkses);
                })
                ->exists()
            : false;

        // Cek akses individual
        $dariIndividu = $this->userakses()
            ->whereHas('akses', function ($query) use ($namaAkses) {
                $query->where('nama_akses', $namaAkses);
            })
            ->exists();

        // Union — cukup salah satu yang punya akses
        return $dariRole || $dariIndividu;
    }
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
