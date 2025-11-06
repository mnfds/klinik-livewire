<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentBundlingUsage extends Model
{
    protected $table = 'treatment_bundling_usages';
    protected $guarded = ['id'];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function rekamMedis()
    {
        return $this->belongsTo(RekamMedis::class, 'rekam_medis_id');
    }

    public function bundling()
    {
        return $this->belongsTo(Bundling::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class, 'treatments_id');
    }
}
