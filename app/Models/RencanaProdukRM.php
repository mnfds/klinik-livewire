<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RencanaProdukRM extends Model
{
    protected $table = 'rencana_produk_r_m_s';
    protected $guarded = ['id'];

    public function rekammedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }

    public function produk()
    {
        return $this->belongsTo(ProdukDanObat::class, 'produk_id');
    }
}
