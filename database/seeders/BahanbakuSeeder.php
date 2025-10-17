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
                'expired_at' => '2026-02-01',
                'reminder'   => 3,
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
                'expired_at' => '2025-12-20',
                'reminder'   => 4,
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
                'expired_at' => '2025-10-27',
                'reminder'   => 4,
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
                'expired_at' => '2026-05-10',
                'reminder'   => 2,
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
                'expired_at' => '2025-11-16',
                'reminder'   => 1,
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
                'expired_at' => '2025-5-01',
                'reminder'   => 3,
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
                'expired_at' => '2027-12-15',
                'reminder'   => 1,
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
                'expired_at' => '2027-01-21',
                'reminder'   => 2,
                'lokasi'     => 'Gudang Kecil',
                'keterangan' => 'Gel untuk perawatan laser',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
