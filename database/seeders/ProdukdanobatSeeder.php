<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProdukdanobatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('produk_dan_obats')->insert([
            [
                'nama_dagang'   => 'Paracetamol 500mg',
                'kode'          => 'OBT-' . Str::upper(Str::random(6)),
                'sediaan'       => 'Tablet',
                'harga_dasar'   => 1000,
                'diskon'        => 0,
                'harga_bersih'  => 1000,
                'stok'          => 500,
                'expired_at'    => '2026-01-01',
                'batch'         => 'B1234',
                'lokasi'        => 'Rak A1',
                'supplier'      => 'PT Farmasi Sehat',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nama_dagang'   => 'Amoxicillin 250mg',
                'kode'          => 'OBT-' . Str::upper(Str::random(6)),
                'sediaan'       => 'Kapsul',
                'harga_dasar'   => 1500,
                'diskon'        => 100,
                'harga_bersih'  => 1400,
                'stok'          => 300,
                'expired_at'    => '2025-12-31',
                'batch'         => 'B5678',
                'lokasi'        => 'Rak B2',
                'supplier'      => 'Apotek Jaya Farma',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nama_dagang'   => 'Betadine Solution',
                'kode'          => 'PRD-' . Str::upper(Str::random(6)),
                'sediaan'       => 'Cairan',
                'harga_dasar'   => 8000,
                'diskon'        => 500,
                'harga_bersih'  => 7500,
                'stok'          => 100,
                'expired_at'    => '2027-06-01',
                'batch'         => 'B9012',
                'lokasi'        => 'Rak C3',
                'supplier'      => 'CV Medika Sentosa',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}
