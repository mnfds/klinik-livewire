<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermintaanReservasi extends Model
{
    use HasFactory;

    protected $table = 'permintaan_reservasis';

    protected $guarded = ['id'];

    public function poliklinik()
    {
        return $this->belongsTo(PoliKlinik::class, 'poli_id');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }
}
