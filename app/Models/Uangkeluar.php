<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Uangkeluar extends Model
{
    use HasFactory;

    protected $table = 'uangkeluars';
    protected $guarded = ['id'];
}
