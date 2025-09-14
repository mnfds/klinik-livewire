<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatProdukObatBundlingRM extends Model
{

    use HasFactory;

    protected $table = 'riwayat_produk_obat_bundling';
    protected $guarded = ['id'];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function bundling()
    {
        return $this->belongsTo(Bundling::class);
    }

    public function produk()
    {
        return $this->belongsTo(ProdukDanObat::class, 'produk_id');
    }
}
