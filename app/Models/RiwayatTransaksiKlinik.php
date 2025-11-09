<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatTransaksiKlinik extends Model
{
    protected $table = 'riwayat_transaksi_kliniks';
    protected $guarded = ['id'];

    public function transaksi()
    {
        return $this->belongsTo(TransaksiKlinik::class);
    }
}
