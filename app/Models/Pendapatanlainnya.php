<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pendapatanlainnya extends Model
{
    use HasFactory;

    protected $table = 'pendapatanlainnyas';
    protected $guarded = ['id'];
}
