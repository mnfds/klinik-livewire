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
        $now = now();

        $akses = [
            // Dashboard
            1 => [
                'Dashboard',
            ],

            // Staff
            2 => [
                'Staff Data',
                'Staff Tambah',
                'Staff Hapus',
                'Staff Edit',
                'Verifikasi Email',
                'Reset Password',
            ],

            // Dokter
            3 => [
                'Dokter Data',
                'Dokter Tambah',
                'Dokter Hapus',
                'Dokter Edit',
                'Dokter Detail',
            ],

            // Jam Kerja
            4 => [
                'Jam Kerja Data',
                'Jam Kerja Tambah',
                'Jam Kerja Hapus',
                'Jam Kerja Edit',
            ],

            // Poliklinik
            5 => [
                'Poliklinik Data',
                'Poliklinik Tambah',
                'Poliklinik Hapus',
                'Poliklinik Edit',
            ],

            // Produk & Obat
            6 => [
                'Produk & Obat Data',
                'Produk & Obat Tambah',
                'Produk & Obat Hapus',
                'Produk & Obat Edit',
            ],

            // Pelayanan Medis
            7 => [
                'Pelayanan Data',

                'Pelayanan Medis Data',
                'Pelayanan Medis Tambah',
                'Pelayanan Medis Hapus',
                'Pelayanan Medis Edit',
                'Pelayanan Medis Tambah Bahan',

                'Pelayanan Estetika Data',
                'Pelayanan Estetika Tambah',
                'Pelayanan Estetika Hapus',
                'Pelayanan Estetika Edit',
                'Pelayanan Estetika Tambah Bahan',
            ],

            // Paket Bundling
            8 => [
                'Paket Bundling Data',
                'Paket Bundling Tambah',
                'Paket Bundling Hapus',
                'Paket Bundling Edit',
                'Paket Bundling Status',
            ],
            // Jadwal
            9 => [
                'Jadwal',
            ],
            // Laporan
            10 => [
                'Laporan',
            ],
            // Persediaan
            11 => [
                'Persediaan',
            ],
            // Pengajuan
            12 => [
                'Pengajuan',
            ],
            // Pasien
            13 => [
                'Pasien',
                'Pasien Tambah',
                'Pasien Detail',
                'Pasien Edit',
                'Pasien Hapus',
                'Pasien Registrasi',
            ],
            // Antrian
            14 => [
                'Antrian',
                'Ambil Nomor',
                'Kelola Antrian',
                'Panggilan Suara',
                'Hapus Nomor Antrian Masuk',
                'Hapus Nomor Antrian Dipanggil',
                
                'Display Antrian',
                'Display Registrasi',
                'Display Poliklinik',
                'Display Apotek',
            ],
            // Rawat Jalan
            15 => [
                'Rawat Jalan',
                'Pendaftaran',
                'Pasien Terdaftar Data',
                'Hapus Pasien Terdaftar',
                'Pasien Diperiksa Data',
            ],
            // Kajian
            16 => [
                'Kajian',
                'Kajian Tambah',
            ],
            // Rekam Medis
            17 => [
                'Rekam Medis',
                'Rekam Medis Tambah',
                'Riwayat Rekam Medis',
                'Detail Rekam Medis',
            ],
            // Transaksi
            18 => [
                'Transaksi',
                'Transaksi Klinik',
                'Transaksi Klinik Data',
                'Transaksi Klinik Detail',
                'Transaksi Klinik Selesai',

                'Transaksi Apotik',
                'Transaksi Apotik Data',
                'Transaksi Apotik Tambah',
                'Transaksi Apotik Detail',
                'Transaksi Apotik Edit',
                'Transaksi Apotik Hapus',
            ],
            // Resep
            19 => [
                'Resep Obat',
            ],
            // Satu Sehat
            20 => [
                'Satu Sehat',

                'Praktisi Satu Sehat',
                'Tambah Praktisi Satu Sehat',
                'Hapus Praktisi Satu Sehat',

                'Lokasi Satu Sehat',
                'Tambah Lokasi Satu Sehat',
                'Hapus Lokasi Satu Sehat',

                'Organisasi Satu Sehat',
                'Tambah Organisasi Satu Sehat',
                'Hapus Organisasi Satu Sehat',
            ],
        ];

        $data = [];

        foreach ($akses as $group => $items) {
            foreach ($items as $nama) {
                $data[] = [
                    'nama_akses'        => $nama,
                    'nomor_group_akses' => $group,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
        }

        DB::table('akses')->insert($data);
    }
}
