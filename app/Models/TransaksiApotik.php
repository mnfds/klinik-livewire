<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiApotik extends Model
{
    use HasFactory;

    protected $table = 'transaksi_apotiks';
    protected $guarded = ['id'];

    public function riwayat()
    {
        return $this->hasMany(RiwayatTransaksiApotik::class, 'transaksi_id');
    }

    public function riwayatBarang()
    {
        return $this->hasMany(RiwayatTransaksiApotikBarang::class, 'transaksi_id');
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

}
