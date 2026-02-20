<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LayananBahan extends Model
{
    use HasFactory;

    protected $table = 'layanan_bahans';
    protected $guarded = ['id'];


    public function pelayanan()
    {
        return $this->belongsTo(Pelayanan::class, 'pelayanan_id');
    }

    public function bahanbaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }
}
