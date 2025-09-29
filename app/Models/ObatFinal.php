<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObatFinal extends Model
{
    protected $table = 'obat_finals';
    protected $guarded = ['id'];

    public function rekammedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }
    
    public function obatRacikanFinals()
    {
        return $this->hasMany(ObatRacikanFinal::class);
    }

    public function obatNonRacikanFinals()
    {
        return $this->hasMany(ObatNonRacikanFinal::class);
    }
}
