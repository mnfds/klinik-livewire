<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataEstetika extends Model
{
    protected $table = 'data_estetikas';
    protected $guarded = ['id'];

    public function kajianAwal()
    {
        return $this->belongsTo(KajianAwal::class);
    }
}
