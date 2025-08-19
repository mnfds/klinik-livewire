<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataKesehatanRM extends Model
{
    protected $table = 'data_kesehatan_r_m_s';
    protected $guarded = ['id'];

    public function rekamMedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }
}
