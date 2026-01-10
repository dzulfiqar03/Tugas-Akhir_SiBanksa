<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\BankSampah\RTSeeder;
use Database\Seeders\Transaction\BankSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            UserSeeder::class,
            BankSeeder::class,
            RTSeeder::class,
            RolesSeeder::class,
            GenderSeeder::class,
        ]);
    }
}
