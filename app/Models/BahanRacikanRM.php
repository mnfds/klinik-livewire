<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanRacikanRM extends Model
{
    protected $table = 'bahan_racikan_r_m_s';
    protected $guarded = ['id'];

    public function obatRacikan()
    {
        return $this->belongsTo(ObatRacikanRM::class, 'obat_racikan_id');
    }
}
