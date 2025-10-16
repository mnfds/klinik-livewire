<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservasi extends Model
{
    use HasFactory;

    protected $table = 'reservasis';
    protected $guarded = ['id'];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function poliklinik()
    {
        return $this->belongsTo(PoliKlinik::class, 'poli_id');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }
}
