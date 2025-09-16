<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PelayananbundlingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pelayanan_bundlings')->insert([
            // --- Paket Pemeriksaan Umum (bundling_id = 1) ---
            [
                'bundling_id' => 1,
                'pelayanan_id' => 1, // Konsultasi Dokter Umum
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bundling_id' => 1,
                'pelayanan_id' => 5, // Pemeriksaan Darah Lengkap
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bundling_id' => 1,
                'pelayanan_id' => 6, // Tensi Darah
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bundling_id' => 1,
                'pelayanan_id' => 2, // Pemeriksaan Gigi
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bundling_id' => 1,
                'pelayanan_id' => 3, // USG Kehamilan
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bundling_id' => 1,
                'pelayanan_id' => 4, // Vaksinasi
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
