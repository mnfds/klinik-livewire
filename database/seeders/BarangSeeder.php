<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $barangs = [
            [
                'nama' => 'Pulpen Biru',
                'kode' => 'BRG001',
                'satuan' => 'pcs',
                'stok' => 100,
                'harga_dasar' => 3500,
                'lokasi' => 'Gudang A',
                'keterangan' => 'Pulpen untuk keperluan administrasi',
            ],
            [
                'nama' => 'Cutter Besar',
                'kode' => 'BRG002',
                'satuan' => 'pcs',
                'stok' => 50,
                'harga_dasar' => 5000,
                'lokasi' => 'Gudang B',
                'keterangan' => 'Cutter untuk gudang atau ruang alat',
            ],
            [
                'nama' => 'Kertas A4',
                'kode' => 'BRG003',
                'satuan' => 'rim',
                'stok' => 30,
                'harga_dasar' => 60000,
                'lokasi' => 'Ruang Admin',
                'keterangan' => 'Kertas untuk print',
            ],
            [
                'nama' => 'Masker Medis',
                'kode' => 'BRG004',
                'satuan' => 'box',
                'stok' => 20,
                'harga_dasar' => 50000,
                'lokasi' => 'Gudang Alkes',
                'keterangan' => 'Masker medis untuk staf klinik',
            ],
        ];

        foreach ($barangs as $barang) {
            Barang::create($barang);
        }
    }
}