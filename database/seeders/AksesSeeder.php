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
            // Pasien
            14 => [
                'Pasien',
                'Pasien Tambah',
                'Pasien Detail',
                'Pasien Edit',
                'Pasien Hapus',
                'Pasien Registrasi',
            ],
            // Antrian
            15 => [
                'Kelola Antrian',
                'Panggilan Suara',
                'Hapus Nomor Antrian Masuk',
                'Hapus Nomor Antrian Dipanggil',
                'Ambil Nomor',
            ],
            16 => [
                'Display Registrasi',
                'Display Poliklinik',
                'Display Apotek',
            ],
            // Rawat Jalan
            17 => [
                'Pendaftaran',
                'Pasien Terdaftar Data',
                'Hapus Pasien Terdaftar',
                'Pasien Diperiksa Data',
            ],
            18 => [
                'Reservasi',
            ],
            19 => [
                'Tindak Lanjut',
            ],
            // Kajian
            20 => [
                'Kajian',
                'Kajian Tambah',
            ],
            // Rekam Medis
            21 => [
                'Rekam Medis',
                'Rekam Medis Tambah',
                'Riwayat Rekam Medis',
                'Detail Rekam Medis',
            ],
            // Transaksi
            22 => [
                'Transaksi Klinik',
                'Transaksi Klinik Data',
                'Transaksi Klinik Detail',
                'Transaksi Klinik Selesai',
            ],
            23 => [
                'Transaksi Apotik',
                'Transaksi Apotik Data',
                'Transaksi Apotik Tambah',
                'Transaksi Apotik Detail',
                'Transaksi Apotik Edit',
                'Transaksi Apotik Hapus',
            ],
            // Resep
            24 => [
                'Resep Obat',
                'Kalkulasi Obat',
                'Tebus Obat',
            ],
            // Satu Sehat
            25 => [
                'Praktisi Satu Sehat',
                'Tambah Praktisi Satu Sehat',
                'Hapus Praktisi Satu Sehat',
            ],
            26 => [
                'Lokasi Satu Sehat',
                'Tambah Lokasi Satu Sehat',
            ],
            27 => [
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
