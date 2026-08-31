<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuancutitanggal extends Model
{
    use HasFactory;

    protected $table = 'pengajuancutitanggals';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pengajuanCuti()
    {
        return $this->belongsTo(Pengajuancuti::class, 'pengajuan_cuti_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    public function jamKerjaSebelumnya()
    {
        return $this->belongsTo(JamKerja::class, 'jamkerja_id_sebelumnya');
    }
}
