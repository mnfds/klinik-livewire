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
            [
                'bundling_id' => 1,
                'pelayanan_id' => 2,
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bundling_id' => 1,
                'pelayanan_id' => 1,
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
