<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DokterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('dokters')->insert([
            'user_id' => 2,
            'nama_dokter' => 'dr. Olivia Kirana, Sp.OG',
            'nik' => 3217040109800006,
            'ihs' => null,
            'alamat_dokter' => 'Jl. Merdeka No. 10',
            'jenis_kelamin' => 'P',
            'telepon' => '08123456789',
            'foto_wajah' => null,
            'tingkat_pendidikan' => 'S1 Kedokteran',
            'institusi' => 'Universitas Indonesia',
            'tahun_kelulusan' => '2015',
            'no_str' => 'STR123456',
            'surat_izin_pratik' => 'SIP123456',
            'masa_berlaku_sip' => '2027-12-31',
            'ttd_digital' => null,
        ]);
        DB::table('dokters')->insert([
            'user_id' => 3,
            'nama_dokter' => 'dr. Yoga Yandika, Sp.A',
            'nik' => 3322071302900002,
            'ihs' => null,
            'alamat_dokter' => 'Jl. perintis No. 49',
            'jenis_kelamin' => 'L',
            'telepon' => '08918273645',
            'foto_wajah' => null,
            'tingkat_pendidikan' => 'S1 Kedokteran',
            'institusi' => 'Universitas Indonesia',
            'tahun_kelulusan' => '2018',
            'no_str' => 'STR109872',
            'surat_izin_pratik' => 'SIP109283',
            'masa_berlaku_sip' => '2030-12-31',
            'ttd_digital' => null,
        ]);
    }
}
