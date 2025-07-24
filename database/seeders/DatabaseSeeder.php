<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AksesSeeder::class,
            RoleaksesSeeder::class,
            UserSeeder::class,
            BiodataSeeder::class,
            JamkerjaSeeder::class,
            PoliklinikSeeder::class,
            ProdukdanobatSeeder::class,
            PelayananSeeder::class,
            BundlingSeeder::class,
            PelayananbundlingSeeder::class,
            ProdukdanobatbundlingSeeder::class,
            BarangSeeder::class,
            MutasibarangSeeder::class,
            DokterSeeder::class,
            DokterPoliSeeder::class,
        ]);
    }
}
