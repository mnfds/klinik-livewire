<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pasien extends Model
{
    use HasFactory;

    protected $table = 'pasiens';
    protected $guarded = ['id'];

    public function kunjungan()
    {
        return $this->hasMany(PasienTerdaftar::class);
    }

    public function reservasis()
    {
        return $this->hasMany(Reservasi::class);
    }

    public function pelayananBundlings()
    {
        return $this->hasMany(PelayananBundlingRM::class);
    }

    public function produkObatBundlings()
    {
        return $this->hasMany(ProdukObatBundlingRM::class);
    }

    public function treatmentBundlings()
    {
        return $this->hasMany(TreatmentBundlingRM::class);
    }
    
    public function transaksiApotiks()
    {
        return $this->hasMany(TransaksiApotik::class, 'pasien_id');
    }
}
