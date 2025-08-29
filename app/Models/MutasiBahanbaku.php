<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MutasiBahanbaku extends Model
{
    use HasFactory;

    protected $table = 'mutasi_bahanbakus';
    protected $guarded = ['id'];
    
    public function bahanbaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }
}
