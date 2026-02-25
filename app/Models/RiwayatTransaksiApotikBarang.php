<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatTransaksiApotikBarang extends Model
{
    use HasFactory;

    protected $table = 'riwayat_transaksi_apotik_barangs';
    protected $guarded = ['id'];

    public function transaksi()
    {
        return $this->belongsTo(TransaksiApotik::class, 'transaksi_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
