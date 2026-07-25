<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kolestrol extends Model
{
    protected $table = 'kolestrols';
    protected $guarded = ['id'];

    public function kajianAwal()
    {
        return $this->belongsTo(KajianAwal::class);
    }
}
