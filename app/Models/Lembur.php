<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    use HasFactory;

    protected $table = 'lemburs';
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
