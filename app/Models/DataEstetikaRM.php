<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataEstetikaRM extends Model
{
    protected $table = 'data_estetika_r_m_s';
    protected $guarded = ['id'];

    public function rekammedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }
}
