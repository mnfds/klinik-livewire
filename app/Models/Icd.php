<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Icd extends Model
{
    use HasFactory;

    protected $table = 'icds';
    protected $guarded = ['id'];
}
