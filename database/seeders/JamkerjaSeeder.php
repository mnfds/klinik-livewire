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
            'nama_shift'   => 'Shift Pagi',
            'tipe_shift'   => 'pagi',
            'jam_mulai'    => '07:00:00',
            'jam_selesai'  => '13:00:00',
            'lewat_hari'   => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
