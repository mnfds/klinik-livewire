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
                'Persediaan Barang',
                'Persediaan Barang Tambah',
                'Persediaan Barang Hapus',
                'Persediaan Barang Edit',
                'Persediaan Barang Keluar',
                'Persediaan Barang Masuk',
                'Persediaan Riwayat Barang',
                'Persediaan Riwayat Barang Edit',
                'Persediaan Riwayat Barang Hapus',
            ],
            12 => [
                'Persediaan Bahan Baku',
                'Persediaan Bahan Baku Tambah',
                'Persediaan Bahan Baku Hapus',
                'Persediaan Bahan Baku Edit',
                'Persediaan Bahan Baku Keluar',
                'Persediaan Bahan Baku Masuk',
                'Persediaan Riwayat Bahan Baku',
                'Persediaan Riwayat Bahan Baku Edit',
                'Persediaan Riwayat Bahan Baku Hapus',
            ],
            // Pengajuan
            13 => [
                'Pengajuan Pengeluaran',
                'Pengajuan Pengeluaran Tambah',
                
                'Pengajuan Pengeluaran Pending',
                'Pengajuan Pengeluaran Pending Hapus',
                'Pengajuan Pengeluaran Pending Edit',
                
                'Pengajuan Pengeluaran Disetujui',
                'Pengajuan Pengeluaran Disetujui Tambah',
                'Pengajuan Pengeluaran Disetujui Hapus',
                'Pengajuan Pengeluaran Disetujui Edit',

                'Pengajuan Pengeluaran Ditolak',
                'Pengajuan Pengeluaran Ditolak Hapus',
                'Pengajuan Pengeluaran Ditolak Edit',

                'Pengajuan Pengeluaran Persetujuan',
            ],

            // Izin Keluar
            14 => [
                'Pengajuan Izin Keluar',
                'Pengajuan Izin Keluar Tambah',
                'Pengajuan Izin Keluar Hapus',
                'Pengajuan Izin Keluar Edit',

                'Pengajuan Riwayat Izin Keluar',
                'Pengajuan Riwayat Izin Keluar Hapus',
                'Pengajuan Riwayat Izin Keluar Edit',

                'Pengajuan Izin Keluar Selesai',
            ],
            // Pasien
            15 => [
                'Pasien',
                'Pasien Tambah',
                'Pasien Detail',
                'Pasien Edit',
                'Pasien Hapus',
                'Pasien Registrasi',
            ],
            // Antrian
            16 => [
                'Kelola Antrian',
                'Panggilan Suara',
                'Hapus Nomor Antrian Masuk',
                'Hapus Nomor Antrian Dipanggil',
                'Ambil Nomor',
            ],
            17 => [
                'Display Registrasi',
                'Display Poliklinik',
                'Display Apotek',
            ],
            // Rawat Jalan
            18 => [
                'Pendaftaran',
                'Pasien Terdaftar Data',
                'Hapus Pasien Terdaftar',
                'Pasien Diperiksa Data',
            ],
            19 => [
                'Reservasi',
            ],
            20 => [
                'Tindak Lanjut',
            ],
            // Kajian
            21 => [
                'Kajian',
                'Kajian Tambah',
            ],
            // Rekam Medis
            22 => [
                'Rekam Medis',
                'Rekam Medis Tambah',
                'Riwayat Rekam Medis',
                'Detail Rekam Medis',
            ],
            // Transaksi
            23 => [
                'Transaksi Klinik',
                'Transaksi Klinik Data',
                'Transaksi Klinik Detail',
                'Transaksi Klinik Selesai',
            ],
            24 => [
                'Transaksi Apotik',
                'Transaksi Apotik Data',
                'Transaksi Apotik Tambah',
                'Transaksi Apotik Detail',
                'Transaksi Apotik Edit',
                'Transaksi Apotik Hapus',
            ],
            // Resep
            25 => [
                'Resep Obat',
                'Kalkulasi Obat',
                'Tebus Obat',
            ],
            // Satu Sehat
            26 => [
                'Praktisi Satu Sehat',
                'Tambah Praktisi Satu Sehat',
                'Hapus Praktisi Satu Sehat',
            ],
            27 => [
                'Lokasi Satu Sehat',
                'Tambah Lokasi Satu Sehat',
            ],
            28 => [
                'Organisasi Satu Sehat',
                'Tambah Organisasi Satu Sehat',
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
