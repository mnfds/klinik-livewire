<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\MutasiBarang;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MutasibarangSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada barang dulu
        $barang = Barang::first();
        if (!$barang) {
            $barang = Barang::create([
                'nama' => 'Contoh Barang',
                'kode' => 'BRG000',
                'satuan' => 'pcs',
                'stok' => 50,
                'lokasi' => 'Gudang Utama',
                'keterangan' => 'Barang dummy untuk mutasi',
            ]);
        }

        $mutasi = [
            [
                'barang_id' => $barang->id,
                'tipe' => 'masuk',
                'jumlah' => 20,
                'diajukan_oleh' => 'Admin Gudang',
                'catatan' => 'Pengadaan awal barang',
            ],
            [
                'barang_id' => $barang->id,
                'tipe' => 'keluar',
                'jumlah' => 5,
                'diajukan_oleh' => 'Perawat UGD',
                'catatan' => 'Digunakan di ruang UGD',
            ],
            [
                'barang_id' => $barang->id,
                'tipe' => 'masuk',
                'jumlah' => 10,
                'diajukan_oleh' => 'Kepala Gudang',
                'catatan' => 'Stok tambahan dari supplier',
            ],
        ];

        foreach ($mutasi as $data) {
            MutasiBarang::create($data);
        }
    }
}
