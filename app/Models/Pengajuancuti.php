<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuancuti extends Model
{
    use HasFactory;

    protected $table = 'pengajuancutis';
    protected $guarded = ['id'];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function tanggals()
    {
        return $this->hasMany(Pengajuancutitanggal::class, 'pengajuan_cuti_id');
    }

    public function getJumlahHariAttribute(): int
    {
        return $this->tanggals()->count();
    }
}
