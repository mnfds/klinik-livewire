<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObatNonRacikanFinal extends Model
{
    protected $table = 'obat_non_racikan_finals';
    protected $guarded = ['id'];
    
    public function obatFinal()
    {
        return $this->belongsTo(ObatFinal::class);
    }

    public function produk()
    {
        return $this->belongsTo(ProdukDanObat::class, 'produk_id');
    }
}
