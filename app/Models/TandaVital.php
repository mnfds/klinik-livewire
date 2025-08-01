<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TandaVital extends Model
{
    protected $table = 'tanda_vitals';
    protected $guarded = ['id'];

    public function kajianAwal()
    {
        return $this->belongsTo(KajianAwal::class);
    }
}
