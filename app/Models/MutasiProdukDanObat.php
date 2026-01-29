<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MutasiProdukDanObat extends Model
{
    use HasFactory;

    protected $table = 'mutasi_produk_dan_obats';
    protected $guarded = ['id'];
    
    public function produkdanobat()
    {
        return $this->belongsTo(ProdukDanObat::class, 'produk_id');
    }
}
