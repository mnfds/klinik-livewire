<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Akses extends Model
{
    use HasFactory;

    protected $table = 'akses';
    protected $guarded = ['id'];

    public function aksesrole()
    {
        return $this->hasMany(RoleAkses::class);
    }
}
