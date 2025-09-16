<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TreatmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('treatments')->insert([
            [
                'nama_treatment'  => 'Facial Basic',
                'harga_treatment' => 75000,
                'diskon'          => 0,
                'harga_bersih'    => 75000 - (75000 * 0 / 100),
                'deskripsi'       => 'Perawatan dasar pembersihan wajah',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'nama_treatment'  => 'Facial Premium',
                'harga_treatment' => 150000,
                'diskon'          => 0,
                'harga_bersih'    => 150000 - (150000 * 0 / 100),
                'deskripsi'       => 'Facial dengan serum dan masker premium',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'nama_treatment'  => 'Chemical Peeling',
                'harga_treatment' => 200000,
                'diskon'          => 20,
                'harga_bersih'    => 200000 - (200000 * 20 / 100),
                'deskripsi'       => 'Peeling kimia untuk regenerasi kulit',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'nama_treatment'  => 'Masker Gold',
                'harga_treatment' => 180000,
                'diskon'          => 0,
                'harga_bersih'    => 180000 - (180000 * 0 / 100),
                'deskripsi'       => 'Masker emas untuk mencerahkan wajah',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'nama_treatment'  => 'Acne Treatment',
                'harga_treatment' => 120000,
                'diskon'          => 50,
                'harga_bersih'    => 120000 - (120000 * 50 / 100),
                'deskripsi'       => 'Perawatan kulit berjerawat',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'nama_treatment'  => 'Hydrating Therapy',
                'harga_treatment' => 100000,
                'diskon'          => 0,
                'harga_bersih'    => 100000 - (100000 * 0 / 100),
                'deskripsi'       => 'Perawatan untuk melembabkan kulit kering',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'nama_treatment'  => 'Laser Rejuvenation',
                'harga_treatment' => 500000,
                'diskon'          => 50,
                'harga_bersih'    => 500000 - (500000 * 50 / 100),
                'deskripsi'       => 'Perawatan laser untuk meremajakan kulit dan mengurangi kerutan',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
    }
}
