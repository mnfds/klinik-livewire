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

        // Biodata Perawat
        DB::table('biodatas')->insert([
            'user_id' => 4,
            'nama_lengkap' => 'Sinta Wijaya, S.Kep',
            'alamat' => 'Jl. Sehat No. 2',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2002-05-15',
            'jenis_kelamin' => 'P',
            'telepon' => '08129876543',
            'mulai_bekerja' => '2021-03-01',
            'foto_wajah' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('biodatas')->insert([
            'user_id' => 5,
            'nama_lengkap' => 'Anita Lestari, S.Kep., Ns.',
            'alamat' => 'Jl. Kesehatan No. 3',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '2002-07-20',
            'jenis_kelamin' => 'P',
            'telepon' => '081377788899',
            'mulai_bekerja' => '2021-07-01',
            'foto_wajah' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        }
}
