<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PoliKlinik extends Model
{
    use HasFactory;

    protected $table = 'poli_kliniks';
    protected $guarded = ['id'];

    public function dokterpoli(){
        return $this->hasMany(DokterPoli::class);
    }

    public function antrians()
    {
        return $this->hasMany(NomorAntrian::class, 'poli_id');
    }
}
