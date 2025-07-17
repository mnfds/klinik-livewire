<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'admin123',
            'email' => 'admin@gmail.com.',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'role_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
