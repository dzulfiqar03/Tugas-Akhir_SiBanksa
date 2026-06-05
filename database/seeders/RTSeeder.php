<?php

namespace Database\Seeders;

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
        $rts = [1, 2, 3, 4, 5, 6, 7];

        $data = [];

        foreach ($rts as $rt) {
            $data[] = [
                'RT' => $rt,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('rt_perumahan')->insert($data);
    }
}
