<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProdukObatBundling extends Model
{
    use HasFactory;

    protected $table = 'produk_obat_bundlings';
    protected $guarded = ['id'];

    public function bundling()
    {
        return $this->belongsTo(Bundling::class, 'bundling_id');
    }

    public function produk()
    {
        return $this->belongsTo(ProdukDanObat::class, 'produk_id');
    }
}