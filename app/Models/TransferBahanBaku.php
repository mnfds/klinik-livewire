<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferBahanBaku extends Model
{
    use HasFactory;

    protected $table = 'transfer_bahan_bakus';
    protected $guarded = ['id'];

    public function bahanbakubesar()
    {
        return $this->belongsTo(bahanbakubesar::class, 'bahan_baku_besar_id');
    }

    public function bahanbaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }
}
