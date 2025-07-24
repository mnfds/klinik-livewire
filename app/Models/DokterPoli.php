<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DokterPoli extends Model
{
    use HasFactory;

    protected $table = 'dokter_polis';
    protected $guarded = ['id'];


    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }

    public function poli()
    {
        return $this->belongsTo(PoliKlinik::class);
    }
}
