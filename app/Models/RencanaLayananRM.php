<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RencanaLayananRM extends Model
{
    protected $table = 'rencana_layanan_r_m_s';
    protected $guarded = ['id'];

    public function rekammedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }
}
