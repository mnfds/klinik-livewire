<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RencanaTreatmentRM extends Model
{
    protected $table = 'rencana_treatment_r_m_s';
    protected $guarded = ['id'];

    public function rekammedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }
}
