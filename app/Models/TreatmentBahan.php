<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TreatmentBahan extends Model
{
    use HasFactory;

    protected $table = 'treatment_bahans';
    protected $guarded = ['id'];


    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function bahanbaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }
}
