<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KajianAwal extends Model
{
    protected $table = 'kajian_awals';
    protected $guarded = ['id'];

    public function pasienTerdaftar()
    {
        return $this->belongsTo(PasienTerdaftar::class);
    }

    public function pemeriksaanFisik()
    {
        return $this->hasOne(PemeriksaanFisik::class);
    }

    public function tandaVital()
    {
        return $this->hasOne(TandaVital::class);
    }

    public function dataKesehatan()
    {
        return $this->hasOne(DataKesehatan::class);
    }

    public function dataEstetika()
    {
        return $this->hasOne(DataEstetika::class);
    }

}
