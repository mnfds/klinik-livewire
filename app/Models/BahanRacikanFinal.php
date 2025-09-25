<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanRacikanFinal extends Model
{
    protected $table = 'bahan_racikan_finals';
    protected $guarded = ['id'];

    public function obatRacikanFinal()
    {
        return $this->belongsTo(ObatRacikanFinal::class, 'obat_racikan_final_id');
    }
}
