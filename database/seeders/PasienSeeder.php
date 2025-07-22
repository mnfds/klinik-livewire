<?php

namespace Database\Seeders;

use App\Models\Pasien;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PasienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        {
            \Faker\Factory::create('id_ID');

            for ($i = 0; $i < 10; $i++) {
                Pasien::create([
                    'no_register' => Str::random(10),
                    'nik' => fake('id_ID')->nik(),
                    'no_ihs' => fake('id_ID')->numerify('IHS##########'),
                    'nama' => fake('id_ID')->name(),
                    'alamat' => fake('id_ID')->address(),
                    'no_telp' => fake('id_ID')->phoneNumber(),
                    'jenis_kelamin' => fake()->randomElement(['Laki-laki', 'Wanita']),
                    'agama' => fake('id_ID')->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu']),
                    'profesi' => fake('id_ID')->jobTitle(),
                    'tanggal_lahir' => fake('id_ID')->date(),
                    'status' => fake('id_ID')->randomElement(['Menikah', 'Belum Menikah']),
                    'foto_pasien' => fake('id_ID')->imageUrl(200, 200, 'people'),
                    'deskripsi' => fake('id_ID')->sentence(),
                ]);
            }
        }
    }
}
