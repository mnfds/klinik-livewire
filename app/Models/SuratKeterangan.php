<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuratKeterangan extends Model
{
    use HasFactory;

    protected $table = 'surat_keterangans';
    protected $guarded = ['id'];

    public function pasienTerdaftar()
    {
        return $this->belongsTo(PasienTerdaftar::class);
    }
}
