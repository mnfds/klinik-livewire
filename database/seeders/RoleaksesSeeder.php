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
        $now = now();

        // Ambil semua ID akses
        $aksesIds = DB::table('akses')->pluck('id');

        $roleAkses = [];

        foreach ($aksesIds as $aksesId) {
            $roleAkses[] = [
                'role_id'   => 1, // SUPER ADMIN
                'akses_id'  => $aksesId,
                'created_at'=> $now,
                'updated_at'=> $now,
            ];
        }

        DB::table('role_akses')->insert($roleAkses);
    }
}
