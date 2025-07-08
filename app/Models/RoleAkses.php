<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleAkses extends Model
{
    use HasFactory;

    protected $table = 'role_akses';
    protected $guarded = ['id'];


    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function akses()
    {
        return $this->belongsTo(Akses::class);
    }
}
