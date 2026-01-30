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

            // Pelayanan Medis
            6 => [
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
            7 => [
                'Paket Bundling Data',
                'Paket Bundling Tambah',
                'Paket Bundling Hapus',
                'Paket Bundling Edit',
                'Paket Bundling Status',
            ],

            // Jadwal
            8 => [
                'Jadwal',
            ],

            // Laporan
            9 => [
                'Laporan',
            ],

            // Laporan Arus Kas
            10 => [
                'Arus Kas'
            ],

            // Pengeluaran Arus Kas
            11 => [
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

            // Pendapatan Arus Kas
            12 => [
                'Pendapatan',
                'Pendapatan Tambah',
                'Pendapatan Hapus',
                'Pendapatan Edit',
            ],

            // Laporan Kunjungan Pasien
            13 => [
                'Kunjungan Pasien'
            ],

            // Laporan Kinerja Karyawan
            14 => [
                'Kinerja Karyawan'
            ],

            // Persediaan Barang
            15 => [
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

            // Persediaan Bahan Baku
            16 => [
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

            // Persediaan Produk & Obat
            17 => [
                'Produk & Obat Data',
                'Produk & Obat Tambah',
                'Produk & Obat Hapus',
                'Produk & Obat Edit',
                'Produk & Obat Keluar',
                'Produk & Obat Masuk',
                'Riwayat Produk & Obat',
                'Riwayat Produk & Obat Edit',
                'Riwayat Produk & Obat Hapus',
            ],

            // Pengajuan Izin Keluar
            18 => [
                'Pengajuan Izin Keluar',
                'Pengajuan Izin Keluar Tambah',
                'Pengajuan Izin Keluar Hapus',
                'Pengajuan Izin Keluar Edit',

                'Pengajuan Riwayat Izin Keluar',
                'Pengajuan Riwayat Izin Keluar Hapus',
                'Pengajuan Riwayat Izin Keluar Edit',

                'Pengajuan Izin Keluar Selesai',
            ],

            // Pengajuan Cuti
            19 => [
                'Pengajuan Cuti'
            ],

            // Pengajuan Lembur
            20 => [
                'Pengajuan Lembur',
                'Pengajuan Lembur Tambah',
                'Pengajuan Lembur Hapus',
                'Pengajuan Lembur Edit',

                'Pengajuan Riwayat Lembur',
                'Pengajuan Riwayat Lembur Hapus',
                'Pengajuan Riwayat Lembur Edit',

                'Persetujuan Ajuan Lembur',
                'Pengajuan Lembur Selesai',
            ],
            
            // Inventaris
            21 => [
                'Inventaris'
            ],

            // Pasien
            22 => [
                'Pasien',
                'Pasien Tambah',
                'Pasien Detail',
                'Pasien Edit',
                'Pasien Hapus',
                'Pasien Registrasi',
            ],

            // Antrian
            23 => [
                'Kelola Antrian',
                'Panggilan Suara',
                'Hapus Nomor Antrian Masuk',
                'Hapus Nomor Antrian Dipanggil',
                'Ambil Nomor',
            ],
            
            24 => [
                'Display Registrasi',
                'Display Poliklinik',
                'Display Apotek',
            ],

            // Rawat Jalan Pendaftaran
            25 => [
                'Pendaftaran',
                'Pasien Terdaftar Data',
                'Hapus Pasien Terdaftar',
                'Pasien Diperiksa Data',
            ],

            // Reservasi Pasien
            26 => [
                'Reservasi',
            ],

            // Pasien Tindakan Lanjutan 
            27 => [
                'Tindak Lanjut',
            ],

            // Kajian Awal
            28 => [
                'Kajian',
                'Kajian Tambah',
            ],

            // Rekam Medis Pasien
            29 => [
                'Rekam Medis',
                'Rekam Medis Tambah',
                'Riwayat Rekam Medis',
                'Detail Rekam Medis',
            ],

            // Transaksi Klinik
            30 => [
                'Transaksi Klinik',
                'Transaksi Klinik Data',
                'Transaksi Klinik Detail',
                'Transaksi Klinik Selesai',
            ],

            // Transaksi Apotik
            31 => [
                'Transaksi Apotik',
                'Transaksi Apotik Data',
                'Transaksi Apotik Tambah',
                'Transaksi Apotik Detail',
                'Transaksi Apotik Edit',
                'Transaksi Apotik Hapus',
            ],

            // Resep Obat
            32 => [
                'Resep Obat',
                'Kalkulasi Obat',
                'Tebus Obat',
            ],

            // Satu Sehat configuration
            33 => [
                'Praktisi Satu Sehat',
                'Tambah Praktisi Satu Sehat',
                'Hapus Praktisi Satu Sehat',
            ],

            34 => [
                'Lokasi Satu Sehat',
                'Tambah Lokasi Satu Sehat',
            ],
            35 => [
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
