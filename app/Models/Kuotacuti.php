<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kuotacuti extends Model
{
    use HasFactory;

    protected $table = 'kuotacutis';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function getKuotaSisaAttribute(): int
    {
        return $this->kuota_dimiliki - $this->kuota_terpakai;
    }
}
