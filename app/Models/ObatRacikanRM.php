<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObatRacikanRM extends Model
{
    protected $table = 'obat_racikan_r_m_s';
    protected $guarded = ['id'];

    public function rekammedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }

    public function bahanRacikan()
    {
        return $this->hasMany(BahanRacikanRM::class);
    }
}
