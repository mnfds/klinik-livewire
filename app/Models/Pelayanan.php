<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pelayanan extends Model
{
    use HasFactory;

    protected $table = 'pelayanans';
    protected $guarded = ['id'];

    public function rencanaLayanan()
    {
        return $this->hasMany(RencanaLayananRM::class);
    }

    public function layananbahan()
    {
        return $this->hasMany(LayananBahan::class,'pelayanan_id');
    }

    public function pelayananBundlings()
    {
        return $this->hasMany(PelayananBundling::class, 'pelayanan_id');
    }
}
