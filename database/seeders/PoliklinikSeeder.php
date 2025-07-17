<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PoliklinikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('poli_kliniks')->insert([
            [
                'nama_poli' => 'Poli Umum',
                'kode' => 'UMM',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_poli' => 'Poli Gigi',
                'kode' => 'GIG',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
