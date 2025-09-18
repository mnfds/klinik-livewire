<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatTransaksiApotik extends Model
{
    use HasFactory;

    protected $table = 'riwayat_transaksi_apotiks';
    protected $guarded = ['id'];

    public function transaksi()
    {
        return $this->belongsTo(TransaksiApotik::class, 'transaksi_id');
    }

    public function produk()
    {
        return $this->belongsTo(ProdukDanObat::class, 'produk_id');
    }
}
