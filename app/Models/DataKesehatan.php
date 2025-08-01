<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataKesehatan extends Model
{
    protected $table = 'data_kesehatans';
    protected $guarded = ['id'];

    public function kajianAwal()
    {
        return $this->belongsTo(KajianAwal::class);
    }
}
