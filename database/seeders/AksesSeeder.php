<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AksesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('akses')->insert([
            'nama_akses' => 'create A',
            'nomor_group_akses' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'update A',
            'nomor_group_akses' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'create B',
            'nomor_group_akses' => '2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'update B',
            'nomor_group_akses' => '2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
