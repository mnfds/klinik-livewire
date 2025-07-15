<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MutasiBarang extends Model
{
    use HasFactory;

    protected $table = 'mutasi_barangs';
    protected $guarded = ['id'];
    
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
