<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosaRM extends Model
{
    protected $table = 'diagnosa_r_m_s';
    protected $guarded = ['id'];

    public function rekammedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }
}
