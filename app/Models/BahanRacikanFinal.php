<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanRacikanFinal extends Model
{
    protected $table = 'bahan_racikan_finals';
    protected $guarded = ['id'];

    public function obatRacikanFinal()
    {
        return $this->belongsTo(ObatRacikanFinal::class);
    }

    public function produk()
    {
        return $this->belongsTo(ProdukDanObat::class, 'produk_id');
    }
}
