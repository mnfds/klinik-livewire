<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleaksesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('role_akses')->insert([
            'role_id' => 1,
            'akses_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
