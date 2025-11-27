<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    protected $table = 'locations';
    protected $guarded = ['id'];

    public function polikliniks()
    {
        return $this->hasMany(PoliKlinik::class, 'location_id');
    }
}
