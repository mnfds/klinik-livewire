<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $table = 'organizations';
    protected $guarded = ['id'];
    
    public function polikliniks()
    {
        return $this->hasMany(PoliKlinik::class, 'organization_id');
    }
}
