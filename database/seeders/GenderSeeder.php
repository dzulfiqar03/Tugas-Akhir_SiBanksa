<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genders = ['Laki-Laki', 'Perempuan', 'None'];

        $data = [];

        foreach ($genders as $gender) {
            $data[] = [
                'gender' => $gender,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('genders')->insert($data);
    }
}
