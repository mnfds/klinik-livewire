<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelayananBundlingUsage extends Model
{
    protected $table = 'pelayanan_bundling_usages';
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

    public function pelayanan()
    {
        return $this->belongsTo(Pelayanan::class);
    }
}
