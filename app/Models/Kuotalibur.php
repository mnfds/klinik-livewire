<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kuotalibur extends Model
{
    use HasFactory;

    protected $table = 'kuotaliburs';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
