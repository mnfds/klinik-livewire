<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barangs';
    protected $guarded = ['id'];
    
    public function mutasi()
    {
        return $this->hasMany(MutasiBarang::class);
    }

    public function riwayatTransaksi()
    {
        return $this->hasMany(RiwayatTransaksiApotik::class, 'barang_id');
    }
}
