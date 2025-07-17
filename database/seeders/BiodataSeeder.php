<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BiodataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('biodatas')->insert([
            'user_id' => 1,
            'nama_lengkap' => 'Admin Klinik',
            'alamat' => 'Jl. Utama No. 1',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'telepon' => '08123456789',
            'mulai_bekerja' => '2020-01-01',
            'foto_wajah' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
