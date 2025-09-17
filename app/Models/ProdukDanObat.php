<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProdukDanObat extends Model
{
    use HasFactory;

    protected $table = 'produk_dan_obats';
    protected $guarded = ['id'];

    public function rencanaProdukdanObat()
    {
        return $this->hasMany(RencanaProdukRM::class, 'produk_id');
    }

    public function produkObatBundlings()
    {
        return $this->hasMany(ProdukObatBundling::class, 'produk_id');
    }
}
