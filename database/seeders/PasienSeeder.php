<?php

namespace Database\Seeders;

use App\Models\Pasien;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PasienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pasiens')->insert([
            [
                'no_register' => 'REG-' . Str::upper(Str::random(6)),
                'nik' => '3201010101010001',
                'no_ihs' => 'IHS-001',
                'nama' => 'Ahmad Fauzi',
                'alamat' => 'Jl. Merpati No.10, Jakarta',
                'no_telp' => '081234567890',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Islam',
                'profesi' => 'Karyawan Swasta',
                'tanggal_lahir' => '1990-05-10',
                'status' => 'Menikah',
                'foto_pasien' => null,
                'deskripsi' => 'Pasien baru, tidak ada riwayat penyakit berat.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_register' => 'REG-' . Str::upper(Str::random(6)),
                'nik' => '3201010101010002',
                'no_ihs' => 'IHS-002',
                'nama' => 'Siti Aminah',
                'alamat' => 'Jl. Kenanga No.5, Bandung',
                'no_telp' => '082345678901',
                'jenis_kelamin' => 'Wanita',
                'agama' => 'Islam',
                'profesi' => 'Guru',
                'tanggal_lahir' => '1985-08-15',
                'status' => 'Menikah',
                'foto_pasien' => null,
                'deskripsi' => 'Pasien rutin kontrol kesehatan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_register' => 'REG-' . Str::upper(Str::random(6)),
                'nik' => '3201010101010003',
                'no_ihs' => 'IHS-003',
                'nama' => 'Budi Santoso',
                'alamat' => 'Jl. Melati No.7, Surabaya',
                'no_telp' => '083456789012',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Kristen',
                'profesi' => 'Pegawai Negeri',
                'tanggal_lahir' => '1995-12-20',
                'status' => 'Belum Menikah',
                'foto_pasien' => null,
                'deskripsi' => 'Pasien dengan riwayat alergi ringan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_register' => 'REG-' . Str::upper(Str::random(6)),
                'nik' => '3201010101010004',
                'no_ihs' => 'IHS-004',
                'nama' => 'Dewi Lestari',
                'alamat' => 'Jl. Anggrek No.12, Yogyakarta',
                'no_telp' => '084567890123',
                'jenis_kelamin' => 'Wanita',
                'agama' => 'Hindu',
                'profesi' => 'Mahasiswa',
                'tanggal_lahir' => '2000-03-05',
                'status' => 'Belum Menikah',
                'foto_pasien' => null,
                'deskripsi' => 'Pasien baru, ingin cek kesehatan rutin.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_register' => 'REG-' . Str::upper(Str::random(6)),
                'nik' => '3201010101010005',
                'no_ihs' => 'IHS-005',
                'nama' => 'Rudi Hartono',
                'alamat' => 'Jl. Sakura No.9, Semarang',
                'no_telp' => '085678901234',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Islam',
                'profesi' => 'Wiraswasta',
                'tanggal_lahir' => '1988-11-30',
                'status' => 'Menikah',
                'foto_pasien' => null,
                'deskripsi' => 'Pasien rutin periksa tahunan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
