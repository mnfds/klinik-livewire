<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatPelayananBundlingRM extends Model
{
    use HasFactory;

    protected $table = 'riwayat_pelayanan_bundling';
    protected $guarded = ['id'];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function bundling()
    {
        return $this->belongsTo(Bundling::class);
    }

    public function pelayanan()
    {
        return $this->belongsTo(Pelayanan::class);
    }
}
