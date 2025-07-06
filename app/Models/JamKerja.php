<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JamKerja extends Model
{
    use HasFactory;

    protected $table = 'jam_kerjas';
    protected $guarded = ['id'];
}
