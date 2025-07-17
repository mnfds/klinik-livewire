<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProdukdanobatbundlingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('produk_obat_bundlings')->insert([
            // Paket Pemeriksaan Umum
            [
                'bundling_id' => 1,
                'produk_id' => 1,
                'jumlah' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bundling_id' => 1,
                'produk_id' => 1,
                'jumlah' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
