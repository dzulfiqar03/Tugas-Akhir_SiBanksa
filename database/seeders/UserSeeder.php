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

            // =====================
            // 2. 8 USER BANK SAMPAH
            // =====================
            [
                'id' => Str::uuid(),
                'email' => 'banksampah01@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('banksampah123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'email' => 'banksampah02@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('banksampah123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'email' => 'banksampah03@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('banksampah123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'email' => 'banksampah04@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('banksampah123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'email' => 'banksampah05@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('banksampah123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'email' => 'banksampah06@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('banksampah123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'email' => 'banksampah07@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('banksampah123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'email' => 'banksampah08@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('banksampah123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
