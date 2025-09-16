<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TreatmentBahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('treatment_bahans')->insert([
            // --- Facial Basic ---
            [
                'treatments_id' => 1, // ID treatment "Facial Basic"
                'bahan_baku_id' => 1, // Contoh: kapas
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'treatments_id' => 1,
                'bahan_baku_id' => 2, // Contoh: facial foam
                'created_at'    => now(),
                'updated_at'    => now(),
            ],

            // --- Facial Premium ---
            [
                'treatments_id' => 2,
                'bahan_baku_id' => 2, // facial foam
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'treatments_id' => 2,
                'bahan_baku_id' => 3, // serum
                'created_at'    => now(),
                'updated_at'    => now(),
            ],

            // --- Chemical Peeling ---
            [
                'treatments_id' => 3,
                'bahan_baku_id' => 4, // larutan peeling
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'treatments_id' => 3,
                'bahan_baku_id' => 1, // kapas
                'created_at'    => now(),
                'updated_at'    => now(),
            ],

            // --- Masker Gold ---
            [
                'treatments_id' => 4,
                'bahan_baku_id' => 5, // masker gold
                'created_at'    => now(),
                'updated_at'    => now(),
            ],

            // --- Acne Treatment ---
            [
                'treatments_id' => 5,
                'bahan_baku_id' => 6, // obat jerawat
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'treatments_id' => 5,
                'bahan_baku_id' => 1, // kapas
                'created_at'    => now(),
                'updated_at'    => now(),
            ],

            // --- Hydrating Therapy ---
            [
                'treatments_id' => 6,
                'bahan_baku_id' => 7, // hydrating gel
                'created_at'    => now(),
                'updated_at'    => now(),
            ],

            // --- Laser Rejuvenation ---
            [
                'treatments_id' => 7,
                'bahan_baku_id' => 8, // gel pendingin
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}
