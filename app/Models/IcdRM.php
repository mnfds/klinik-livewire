<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IcdRM extends Model
{
    protected $table = 'icd_r_m_s';
    protected $guarded = ['id'];

    public function rekammedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }
}
