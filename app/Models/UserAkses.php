<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAkses extends Model
{
    use HasFactory;

    protected $table = 'user_akses';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function akses()
    {
        return $this->belongsTo(Akses::class);
    }
}
