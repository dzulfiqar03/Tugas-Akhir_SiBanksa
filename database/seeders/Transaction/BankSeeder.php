<?php

namespace Database\Seeders\Transaction;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('banks')->insert([
            [
                'transfer_code' => '008',
                'name' => 'Bank Mandiri',
                'short_name' => 'MANDIRI',
                'swift_code' => 'BMRIIDJA',
                'logo' => 'https://logo.clearbit.com/mandiri.co.id',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'transfer_code' => '014',
                'name' => 'Bank Central Asia',
                'short_name' => 'BCA',
                'swift_code' => 'CENAIDJA',
                'logo' => 'https://logo.clearbit.com/bca.co.id',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'transfer_code' => '009',
                'name' => 'Bank Negara Indonesia',
                'short_name' => 'BNI',
                'swift_code' => 'BNINIDJA',
                'logo' => 'https://logo.clearbit.com/bni.co.id',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'transfer_code' => '002',
                'name' => 'Bank Rakyat Indonesia',
                'short_name' => 'BRI',
                'swift_code' => 'BRINIDJA',
                'logo' => 'https://logo.clearbit.com/bri.co.id',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'transfer_code' => '013',
                'name' => 'Bank Permata',
                'short_name' => 'PERMATA',
                'swift_code' => 'BBBAIDJA',
                'logo' => 'https://logo.clearbit.com/permatabank.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'transfer_code' => '022',
                'name' => 'CIMB Niaga',
                'short_name' => 'CIMB',
                'swift_code' => 'BNIAIDJA',
                'logo' => 'https://logo.clearbit.com/cimbniaga.co.id',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'transfer_code' => '016',
                'name' => 'Bank BJB',
                'short_name' => 'BJB',
                'swift_code' => 'BJBRIDJA',
                'logo' => 'https://logo.clearbit.com/bankbjb.co.id',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
