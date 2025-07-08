<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pelayanan extends Model
{
    use HasFactory;

    protected $table = 'pelayanans';
    protected $guarded = ['id'];
}
