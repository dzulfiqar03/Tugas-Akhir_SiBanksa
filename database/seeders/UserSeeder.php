<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([

            // =====================
            // 1. USER KETUA RW
            // =====================
            [
                'id' => Str::uuid(),
                'email' => 'ketuarw@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('ketuarw123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],


        ]);
    }
}
