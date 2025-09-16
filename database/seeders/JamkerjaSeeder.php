<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JamkerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jam_kerjas')->insert([
            [
                'nama_shift'   => 'Shift Pagi',
                'tipe_shift'   => 'pagi',
                'jam_mulai'    => '07:00:00',
                'jam_selesai'  => '13:00:00',
                'lewat_hari'   => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_shift'   => 'Shift Siang',
                'tipe_shift'   => 'siang',
                'jam_mulai'    => '13:00:00',
                'jam_selesai'  => '19:00:00',
                'lewat_hari'   => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_shift'   => 'Shift Malam',
                'tipe_shift'   => 'malam',
                'jam_mulai'    => '19:00:00',
                'jam_selesai'  => '07:00:00',
                'lewat_hari'   => true, // karena lewat tengah malam
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_shift'   => 'Shift Full',
                'tipe_shift'   => 'full',
                'jam_mulai'    => '07:00:00',
                'jam_selesai'  => '19:00:00',
                'lewat_hari'   => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}
