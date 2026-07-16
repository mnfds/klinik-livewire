<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JamKerjaRole extends Model
{
    use HasFactory;

    protected $table = 'jam_kerja_roles';
    protected $guarded = ['id'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function jamkerja()
    {
        return $this->belongsTo(JamKerja::class, 'jamkerja_id');
    }
}
