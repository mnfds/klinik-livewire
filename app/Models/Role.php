<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $guarded = ['id'];

    public function aksesrole()
    {
        return $this->hasMany(RoleAkses::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
