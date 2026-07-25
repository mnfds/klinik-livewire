<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KolestrolRM extends Model
{
    protected $table = 'kolestrol_r_m_s';
    protected $guarded = ['id'];

    public function rekammedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }
}
