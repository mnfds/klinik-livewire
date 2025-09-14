<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TreatmentBundlingRM extends Model
{
    use HasFactory;

    protected $table = 'treatment_bundling_r_m_s';
    protected $guarded = ['id'];
    
    public function bundling()
    {
        return $this->belongsTo(Bundling::class, 'bundling_id');
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class, 'treatments_id');
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    // Accessor untuk sisa
    public function getSisaAttribute()
    {
        return $this->jumlah_awal - $this->jumlah_terpakai;
    }
}
