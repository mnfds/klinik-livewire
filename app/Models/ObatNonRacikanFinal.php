<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObatNonRacikanFinal extends Model
{
    protected $table = 'obat_non_racikan_finals';
    protected $guarded = ['id'];

    public function rekammedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }
}
