<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TandaVitalRM extends Model
{
    protected $table = 'tanda_vital_r_m_s';
    protected $guarded = ['id'];

    public function rekammedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }
}
