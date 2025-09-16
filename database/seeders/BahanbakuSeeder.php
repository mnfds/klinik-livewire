<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BahanbakuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bahan_bakus')->insert([
            // --- Bahan Baku (8) ---
            [
                'nama'       => 'Kapas',
                'kode'       => 'BBK-' . Str::upper(Str::random(6)),
                'satuan'     => 'pcs',
                'stok'       => 1000,
                'lokasi'     => 'Gudang Besar',
                'keterangan' => 'Kapas steril untuk perawatan wajah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama'       => 'Facial Foam',
                'kode'       => 'BBK-' . Str::upper(Str::random(6)),
                'satuan'     => 'botol',
                'stok'       => 200,
                'lokasi'     => 'Gudang Besar',
                'keterangan' => 'Sabun pembersih wajah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama'       => 'Serum Wajah',
                'kode'       => 'BBK-' . Str::upper(Str::random(6)),
                'satuan'     => 'botol',
                'stok'       => 150,
                'lokasi'     => 'Gudang Kecil',
                'keterangan' => 'Serum untuk perawatan premium',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama'       => 'Larutan Peeling',
                'kode'       => 'BBK-' . Str::upper(Str::random(6)),
                'satuan'     => 'ml',
                'stok'       => 500,
                'lokasi'     => 'Gudang Besar',
                'keterangan' => 'Bahan kimia untuk chemical peeling',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama'       => 'Masker Gold',
                'kode'       => 'BBK-' . Str::upper(Str::random(6)),
                'satuan'     => 'pcs',
                'stok'       => 300,
                'lokasi'     => 'Gudang Kecil',
                'keterangan' => 'Masker emas 24K untuk perawatan wajah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama'       => 'Obat Jerawat',
                'kode'       => 'BBK-' . Str::upper(Str::random(6)),
                'satuan'     => 'tube',
                'stok'       => 100,
                'lokasi'     => 'Gudang Kecil',
                'keterangan' => 'Krim untuk perawatan jerawat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama'       => 'Hydrating Gel',
                'kode'       => 'BBK-' . Str::upper(Str::random(6)),
                'satuan'     => 'jar',
                'stok'       => 120,
                'lokasi'     => 'Gudang Besar',
                'keterangan' => 'Gel pelembab untuk terapi hidrasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama'       => 'Gel Pendingin',
                'kode'       => 'BBK-' . Str::upper(Str::random(6)),
                'satuan'     => 'tube',
                'stok'       => 80,
                'lokasi'     => 'Gudang Kecil',
                'keterangan' => 'Gel untuk perawatan laser',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
