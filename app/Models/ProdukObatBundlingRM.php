<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProdukObatBundlingRM extends Model
{
    use HasFactory;

    protected $table = 'produk_obat_bundling_r_m_s';
    protected $guarded = ['id'];
    
    public function bundling()
    {
        return $this->belongsTo(Bundling::class, 'bundling_id');
    }

    public function produk()
    {
        return $this->belongsTo(ProdukDanObat::class, 'produk_obat_id');
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    // Accessor untuk sisa
    public function getSisaAttribute()
    {
        return $this->jumlah_awal - $this->jumlah_terpakai;
    }
}
