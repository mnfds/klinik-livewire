<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiBahanBakuBesar extends Model
{
    use HasFactory;

    protected $table = 'mutasi_bahan_baku_besars';
    protected $guarded = ['id'];
    
    public function bahanbakubesar()
    {
        return $this->belongsTo(BahanBakuBesar::class, 'bahan_baku_besar_id');
    }
}
