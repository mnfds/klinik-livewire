<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemeriksaanFisik extends Model
{
    protected $table = 'pemeriksaan_fisiks';
    protected $guarded = ['id'];

    public function kajianAwal()
    {
        return $this->belongsTo(KajianAwal::class);
    }
}
