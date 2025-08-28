<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Treatment extends Model
{
    use HasFactory;

    protected $table = 'treatments';
    protected $guarded = ['id'];

    public function rencanaTreatement()
    {
        return $this->hasMany(RencanaTreatmentRM::class);
    }

    public function treatmentbahan()
    {
        return $this->hasMany(TreatmentBahan::class);
    }
}
