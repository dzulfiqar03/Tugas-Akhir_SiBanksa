<?php

namespace Database\Seeders\BankSampah;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RTSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [];

        for ($i = 1; $i <= 8; $i++) {
            $rows[] = [
                'rt' => str_pad($i, 2, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert all at once
        DB::table('rt_perumahan')->insert($rows);
    }
}
