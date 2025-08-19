<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemeriksaanKulitRM extends Model
{
    protected $table = 'pemeriksaan_kulit_r_m_s';
    protected $guarded = ['id'];

    public function rekammedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }
}
