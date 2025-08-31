<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TreatmentBundling extends Model
{
    use HasFactory;

    protected $table = 'treatment_bundlings';
    protected $guarded = ['id'];

    public function bundling()
    {
        return $this->belongsTo(Bundling::class, 'bundling_id');
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class, 'treatments_id');
    }
}
