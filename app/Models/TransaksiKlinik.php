<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiKlinik extends Model
{
    protected $table = 'transaksi_kliniks';
    protected $guarded = ['id'];

    public function rekammedis()
    {
        return $this->belongsTo(RekamMedis::class, 'rekam_medis_id');
    }
    
    public function riwayatTransaksi()
    {
        return $this->hasMany(RiwayatTransaksiKlinik::class);
    }
}
