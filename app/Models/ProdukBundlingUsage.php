<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukBundlingUsage extends Model
{
    protected $table = 'produk_bundling_usages';
    protected $guarded = ['id'];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function rekamMedis()
    {
        return $this->belongsTo(RekamMedis::class, 'rekam_medis_id');
    }

    public function bundling()
    {
        return $this->belongsTo(Bundling::class);
    }

    public function produk()
    {
        return $this->belongsTo(ProdukDanObat::class, 'produk_obat_id');
    }
}
