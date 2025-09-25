<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObatRacikanFinal extends Model
{
    protected $table = 'obat_racikan_finals';
    protected $guarded = ['id'];

    public function rekammedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }

    public function bahanRacikanFinal()
    {
        return $this->hasMany(BahanRacikanFinal::class, 'obat_racikan_final_id');
    }
}
