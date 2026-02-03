<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanBakuBesar extends Model
{
    use HasFactory;

    protected $table = 'bahan_baku_besars';
    protected $guarded = ['id'];

    public function transferbahanbaku()
    {
        return $this->hasMany(TransferBahanBaku::class, 'bahan_baku_besar_id');
    }

    public function mutasibahanbakubesar()
    {
        return $this->hasMany(MutasiBahanBakuBesar::class, 'bahan_baku_besar_id');
    }
}
