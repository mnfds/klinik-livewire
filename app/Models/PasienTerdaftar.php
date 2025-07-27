<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PasienTerdaftar extends Model
{
    use HasFactory;

    protected $table = 'pasien_terdaftars';
    protected $guarded = ['id'];
}
