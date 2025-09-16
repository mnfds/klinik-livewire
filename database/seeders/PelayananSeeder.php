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
                'harga_bersih' => 50000  - (50000 * 0 / 100),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelayanan' => 'Pemeriksaan Gigi',
                'harga_pelayanan' => 75000,
                'deskripsi' => 'Pembersihan karang gigi dan pengecekan gigi rutin.',
                'diskon' => 50,
                'harga_bersih' => 75000 - (75000 * 50 / 100),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelayanan' => 'USG Kehamilan',
                'harga_pelayanan' => 120000,
                'deskripsi' => 'USG 2D untuk pemeriksaan kehamilan.',
                'diskon' => 10,
                'harga_bersih' => 120000 - (120000 * 10 / 100),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelayanan' => 'Vaksinasi',
                'harga_pelayanan' => 150000,
                'deskripsi' => 'Pelayanan vaksin sesuai jadwal imunisasi.',
                'diskon' => 0,
                'harga_bersih' => 150000 - (150000 * 0 / 100),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelayanan' => 'Pemeriksaan Darah Lengkap',
                'harga_pelayanan' => 90000,
                'deskripsi' => 'Tes darah lengkap termasuk gula dan kolesterol.',
                'diskon' => 5,
                'harga_bersih' => 90000 - (90000 * 5 / 100),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelayanan' => 'Tensi Darah',
                'harga_pelayanan' => 20000,
                'deskripsi' => 'Pengukuran tekanan darah rutin.',
                'diskon' => 0,
                'harga_bersih' => 20000 - (20000 * 0 / 100),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelayanan' => 'Rontgen Dada',
                'harga_pelayanan' => 100000,
                'deskripsi' => 'Rontgen untuk pemeriksaan organ dada.',
                'diskon' => 0,
                'harga_bersih' => 100000 - (100000 * 0 / 100),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelayanan' => 'Pemeriksaan Mata',
                'harga_pelayanan' => 50000,
                'deskripsi' => 'Cek kesehatan mata dan visus.',
                'diskon' => 0,
                'harga_bersih' => 50000 - (50000 * 0 / 100),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelayanan' => 'EKG Jantung',
                'harga_pelayanan' => 120000,
                'deskripsi' => 'Pemeriksaan elektrokardiogram jantung.',
                'diskon' => 0,
                'harga_bersih' => 120000 - (120000 * 0 / 100),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelayanan' => 'Pemeriksaan Urine',
                'harga_pelayanan' => 30000,
                'deskripsi' => 'Tes urine rutin untuk berbagai parameter.',
                'diskon' => 0,
                'harga_bersih' => 30000 - (30000 * 0 / 100),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
