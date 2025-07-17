<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PelayananSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pelayanans')->insert([
            [
                'nama_pelayanan' => 'Konsultasi Dokter Umum',
                'harga_pelayanan' => 50000,
                'deskripsi' => 'Pemeriksaan awal dan konsultasi dengan dokter umum.',
                'diskon' => 0,
                'harga_bersih' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelayanan' => 'Pemeriksaan Gigi',
                'harga_pelayanan' => 75000,
                'deskripsi' => 'Pembersihan karang gigi dan pengecekan gigi rutin.',
                'diskon' => 5000,
                'harga_bersih' => 70000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelayanan' => 'USG Kehamilan',
                'harga_pelayanan' => 120000,
                'deskripsi' => 'USG 2D untuk pemeriksaan kehamilan.',
                'diskon' => 10000,
                'harga_bersih' => 110000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelayanan' => 'Vaksinasi',
                'harga_pelayanan' => 150000,
                'deskripsi' => 'Pelayanan vaksin sesuai jadwal imunisasi.',
                'diskon' => 0,
                'harga_bersih' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
