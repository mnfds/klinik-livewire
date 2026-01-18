<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Izinkeluar extends Model
{
    use HasFactory;

    protected $table = 'izinkeluars';
    protected $guarded = ['id'];

    public function user()
    {
        // USER ATAU STAFF YANG SEDANG KELUAR
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        // USER ATAU STAFF YANG MENYETUJUI IZIN UNTUK KELUAR
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
