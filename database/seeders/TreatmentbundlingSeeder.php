<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TreatmentbundlingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('treatment_bundlings')->insert([
            // --- Paket Perawatan Wajah Dasar (bundling_id = 2) ---
            [
                'bundling_id' => 2,
                'treatments_id' => 1, // Facial Dasar
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bundling_id' => 2,
                'treatments_id' => 2, // Pembersihan Komedo
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bundling_id' => 2,
                'treatments_id' => 3, // Masker Wajah
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // --- Paket Anti Jerawat (bundling_id = 3) ---
            [
                'bundling_id' => 3,
                'treatments_id' => 4, // Facial Anti Jerawat
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bundling_id' => 3,
                'treatments_id' => 5, // Obat Jerawat
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bundling_id' => 3,
                'treatments_id' => 6, // Masker Soothing
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // --- Paket Whitening (bundling_id = 4) ---
            [
                'bundling_id' => 4,
                'treatments_id' => 7, // Serum Whitening
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bundling_id' => 4,
                'treatments_id' => 3, // Masker Wajah / Gold
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bundling_id' => 4,
                'treatments_id' => 6, // Facial Premium
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // --- Paket Relaksasi & Hydrating (bundling_id = 5) ---
            [
                'bundling_id' => 5,
                'treatments_id' => 7, // Facial Hidrasi / Hydrating
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bundling_id' => 5,
                'treatments_id' => 6, // Hydrating Gel Therapy
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bundling_id' => 5,
                'treatments_id' => 5, // Massage Relaksasi
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
