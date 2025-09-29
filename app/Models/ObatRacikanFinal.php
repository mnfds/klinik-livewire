<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObatRacikanFinal extends Model
{
    protected $table = 'obat_racikan_finals';
    protected $guarded = ['id'];

    public function obatFinal()
    {
        return $this->belongsTo(ObatFinal::class);
    }

    public function bahanRacikanFinals()
    {
        return $this->hasMany(BahanRacikanFinal::class);
    }
}