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
                'lokasi' => 'Gudang A',
                'keterangan' => 'Pulpen untuk keperluan administrasi',
            ],
            [
                'nama' => 'Cutter Besar',
                'kode' => 'BRG002',
                'satuan' => 'pcs',
                'stok' => 50,
                'lokasi' => 'Gudang B',
                'keterangan' => 'Cutter untuk gudang atau ruang alat',
            ],
            [
                'nama' => 'Kertas A4',
                'kode' => 'BRG003',
                'satuan' => 'rim',
                'stok' => 30,
                'lokasi' => 'Ruang Admin',
                'keterangan' => 'Kertas untuk print',
            ],
            [
                'nama' => 'Masker Medis',
                'kode' => 'BRG004',
                'satuan' => 'box',
                'stok' => 20,
                'lokasi' => 'Gudang Alkes',
                'keterangan' => 'Masker medis untuk staf klinik',
            ],
        ];

        foreach ($barangs as $barang) {
            Barang::create($barang);
        }
    }
}