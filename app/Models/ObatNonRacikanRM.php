<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObatNonRacikanRM extends Model
{
    protected $table = 'obat_non_racikan_r_m_s';
    protected $guarded = ['id'];

    public function rekammedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }
}
