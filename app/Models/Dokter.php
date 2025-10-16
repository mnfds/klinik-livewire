<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dokter extends Model
{
    use HasFactory;

    protected $table = 'dokters';
    protected $guarded = ['id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dokterpoli()
    {
        return $this->hasMany(DokterPoli::class);
    }

    public function pasienTerdaftars()
    {
        return $this->hasMany(PasienTerdaftar::class, 'dokter_id');
    }

    public function reservasis()
    {
        return $this->hasMany(Reservasi::class, 'dokter_id');
    }
}
