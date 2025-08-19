<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PasienTerdaftar extends Model
{
    use HasFactory;

    protected $table = 'pasien_terdaftars';
    protected $guarded = ['id'];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function poliklinik()
    {
        return $this->belongsTo(PoliKlinik::class, 'poli_id');
    }
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }

    public function kajianAwal()
    {
        return $this->hasOne(KajianAwal::class);
    }

    public function rekamMedis()
    {
        return $this->hasOne(RekamMedis::class);
    }
}
