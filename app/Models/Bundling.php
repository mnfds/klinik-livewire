<?php

namespace App\Models;

use App\Models\TreatmentBundling;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bundling extends Model
{
    use HasFactory;

    protected $table = 'bundlings';
    protected $guarded = ['id'];

    public function treatmentBundlings()
    {
        return $this->hasMany(TreatmentBundling::class, 'bundling_id');
    }

    public function pelayananBundlings()
    {
        return $this->hasMany(PelayananBundling::class, 'bundling_id');
    }

    public function produkObatBundlings()
    {
        return $this->hasMany(ProdukObatBundling::class, 'bundling_id');
    }

    public function rencanaBundling()
    {
        return $this->hasMany(RencananaBundlingRM::class);
    }
}