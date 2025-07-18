<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NomorAntrian extends Model
{
    use HasFactory;

    protected $table = 'nomor_antrians';
    protected $guarded = ['id'];

    public function poli()
    {
        return $this->belongsTo(PoliKlinik::class, 'poli_id');
    }

}
