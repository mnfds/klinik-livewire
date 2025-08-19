<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RencananaBundlingRM extends Model
{
    protected $table = 'rencana_bundling_r_m_s';
    protected $guarded = ['id'];

    public function rekammedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }
}
