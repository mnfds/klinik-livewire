<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DokterPoliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dokter_polis')->insert([
            'dokter_id' => 1,
            'poli_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('dokter_polis')->insert([
            'dokter_id' => 2,
            'poli_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
