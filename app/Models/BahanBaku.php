<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BahanBaku extends Model
{
    use HasFactory;

    protected $table = 'bahan_bakus';
    protected $guarded = ['id'];

    public function treatmentbahan()
    {
        return $this->hasMany(TreatmentBahan::class);
    }

    public function mutasibahan()
    {
        return $this->hasMany(MutasiBahanbaku::class);
    }
}
