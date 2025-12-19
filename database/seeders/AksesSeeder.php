<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AksesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('akses')->insert([
            'nama_akses' => 'Dashboard',
            'nomor_group_akses' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        //AKSES STAFF
        DB::table('akses')->insert([
            'nama_akses' => 'Staff Data',
            'nomor_group_akses' => '2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Staff Tambah',
            'nomor_group_akses' => '2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Staff Hapus',
            'nomor_group_akses' => '2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Staff Edit',
            'nomor_group_akses' => '2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Verifikasi Email',
            'nomor_group_akses' => '2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Reset Password',
            'nomor_group_akses' => '2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        //AKSES DOKTER
        DB::table('akses')->insert([
            'nama_akses' => 'Dokter Data',
            'nomor_group_akses' => '3',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Dokter Tambah',
            'nomor_group_akses' => '3',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Dokter Hapus',
            'nomor_group_akses' => '3',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Dokter Edit',
            'nomor_group_akses' => '3',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Dokter Detail',
            'nomor_group_akses' => '3',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        //AKSES JAM KERJA
        DB::table('akses')->insert([
            'nama_akses' => 'Jam Kerja Data',
            'nomor_group_akses' => '4',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Jam Kerja Tambah',
            'nomor_group_akses' => '4',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Jam Kerja Hapus',
            'nomor_group_akses' => '4',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Jam Kerja Edit',
            'nomor_group_akses' => '4',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        //AKSES POLIKLINIK
        DB::table('akses')->insert([
            'nama_akses' => 'Poliklinik Data',
            'nomor_group_akses' => '5',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Poliklinik Tambah',
            'nomor_group_akses' => '5',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Poliklinik Hapus',
            'nomor_group_akses' => '5',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Poliklinik Edit',
            'nomor_group_akses' => '5',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        //PELAYANAN MEDIS
        DB::table('akses')->insert([
            'nama_akses' => 'Pelayanan Data',
            'nomor_group_akses' => '6',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Pelayanan Medis Data',
            'nomor_group_akses' => '6',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Pelayanan Medis Tambah',
            'nomor_group_akses' => '6',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Pelayanan Medis Hapus',
            'nomor_group_akses' => '6',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Pelayanan Medis Edit',
            'nomor_group_akses' => '6',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Pelayanan Medis Tambah Bahan',
            'nomor_group_akses' => '6',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        //PELAYANAN ESTETIKA
        DB::table('akses')->insert([
            'nama_akses' => 'Pelayanan Estetika Data',
            'nomor_group_akses' => '7',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Pelayanan Estetika Tambah',
            'nomor_group_akses' => '7',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Pelayanan Estetika Hapus',
            'nomor_group_akses' => '7',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Pelayanan Estetika Edit',
            'nomor_group_akses' => '7',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Pelayanan Estetika Tambah Bahan',
            'nomor_group_akses' => '7',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        //PAKET BUNDLING
        DB::table('akses')->insert([
            'nama_akses' => 'Paket Bundling Data',
            'nomor_group_akses' => '8',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Paket Bundling Tambah',
            'nomor_group_akses' => '8',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Paket Bundling Hapus',
            'nomor_group_akses' => '8',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Paket Bundling Edit',
            'nomor_group_akses' => '8',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Paket Bundling Status',
            'nomor_group_akses' => '8',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        //PRODUK & OBAT
        DB::table('akses')->insert([
            'nama_akses' => 'Produk & Obat Data',
            'nomor_group_akses' => '9',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Produk & Obat Tambah',
            'nomor_group_akses' => '9',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Produk & Obat Hapus',
            'nomor_group_akses' => '9',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('akses')->insert([
            'nama_akses' => 'Produk & Obat Edit',
            'nomor_group_akses' => '9',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
