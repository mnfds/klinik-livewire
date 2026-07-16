<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JamKerja extends Model
{
    use HasFactory;

    protected $table = 'jam_kerjas';
    protected $guarded = ['id'];

    public function jadwals() {
        return $this->hasMany(Jadwal::class, 'jamkerja_id');
    }

    public function jamkerjarole()
    {
        return $this->hasMany(JamKerjaRole::class, 'jamkerja_id');
    }
}
