<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BundlingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bundlings')->insert([
            [
                'nama' => 'Paket Pemeriksaan Umum',
                'deskripsi' => 'Termasuk konsultasi dokter, pemeriksaan darah, dan tensi.',
                'harga' => 150000,
                'diskon' => 10, // 10%
                'harga_bersih' => 150000 - (150000 * 10 / 100),
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);    
    }
}
