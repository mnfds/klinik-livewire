<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BundlingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bundlings')->insert([
            [
                'nama' => 'Paket Pemeriksaan Umum',
                'deskripsi' => 'Termasuk konsultasi dokter, pemeriksaan darah, dan tensi.',
                'harga' => 150000,
                'diskon' => 10, // 10%
                'harga_bersih' => 150000 - (150000 * 10 / 100), // 135000
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Paket Perawatan Wajah Dasar',
                'deskripsi' => 'Facial dasar, pembersihan komedo, dan masker wajah.',
                'harga' => 250000,
                'diskon' => 15, // 15%
                'harga_bersih' => 250000 - (250000 * 15 / 100), // 212500
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Paket Anti Jerawat',
                'deskripsi' => 'Termasuk facial anti jerawat, obat jerawat, dan masker soothing.',
                'harga' => 300000,
                'diskon' => 20, // 20%
                'harga_bersih' => 300000 - (300000 * 20 / 100), // 240000
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Paket Whitening',
                'deskripsi' => 'Perawatan mencerahkan dengan serum whitening, masker gold, dan facial premium.',
                'harga' => 500000,
                'diskon' => 25, // 25%
                'harga_bersih' => 500000 - (500000 * 25 / 100), // 375000
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Paket Relaksasi & Hydrating',
                'deskripsi' => 'Facial hidrasi, hydrating gel therapy, dan massage relaksasi.',
                'harga' => 400000,
                'diskon' => 15, // 15%
                'harga_bersih' => 400000 - (400000 * 15 / 100), // 340000
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
