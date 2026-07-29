<?php

namespace Tests;

use App\Models\User;
use App\Models\UserDetail;
use App\Models\RTPerumahan;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    /**
     * Helper untuk membuat user dan otomatis login.
     * Sangat berguna untuk pengujian Iterasi 1 & 2.
     */
    public function loginAsBankSampah(): UserDetail
    {
        // 1. Jalankan Seed data master yang dibutuhkan
        $this->seed([
            \Database\Seeders\RTSeeder::class,
            \Database\Seeders\RolesSeeder::class,
            \Database\Seeders\GenderSeeder::class
        ]);

        // 3. Buat Detail (Pastikan status 'Disetujui' agar tidak terkena middleware)
        $rt = RTPerumahan::first();
        $user = User::factory()->create([
            'email' => 'banksampah03@gmail.com',
            'password' => bcrypt('banksampah123')
        ]);

        $payload = [
            'id_user' => $user->id,
            'userName' => 'banksampah03',
            'fullName' => 'Petugas Bank Sampah XYZ',
            'id_rt' => $rt->id,
            'telephone_number' => '081234567890',
            'address' => 'Gresik Kota',

        ];
        $userDetail = UserDetail::factory()->bankSampah()->create($payload);

        // 4. Lakukan login state
        $this->actingAs($user);


        return $userDetail;
    }

       public function loginAsKetuaRW(): UserDetail
    {
        // 1. Jalankan Seed data master yang dibutuhkan
        $this->seed([
            \Database\Seeders\RTSeeder::class,
            \Database\Seeders\RolesSeeder::class,
            \Database\Seeders\GenderSeeder::class
        ]);

        // 3. Buat Detail (Pastikan status 'Disetujui' agar tidak terkena middleware)
        $rt = RTPerumahan::first();
        $user = User::factory()->create([
            'email' => 'ketuarw@gmail.com',
            'password' => bcrypt('ketuarw123')
        ]);

        $payload = [
            'id_user' => $user->id,
            'userName' => 'ketuarw',
            'fullName' => 'Ketua RW',
            'id_rt' => $rt->id,
            'telephone_number' => '081234567890',
            'address' => 'Gresik Kota',
            'id_gender' => 1

        ];
        $userDetail = UserDetail::factory()->ketuaRW()->create($payload);

        // 4. Lakukan login state
        $this->actingAs($user);


        return $userDetail;
    }

    public function loginAsWarga(): UserDetail
    {
        // 1. Jalankan Seed data master yang dibutuhkan
        $this->seed([
            \Database\Seeders\RTSeeder::class,
            \Database\Seeders\RolesSeeder::class,
            \Database\Seeders\GenderSeeder::class
        ]);

        // 3. Buat Detail (Pastikan status 'Disetujui' agar tidak terkena middleware)
        $rt = RTPerumahan::first();
        $user = User::factory()->create([
            'email' => 'warga@gmail.com',
            'password' => bcrypt('warga123')
        ]);

        $payload = [
            'id_user' => $user->id,
            'userName' => 'warga',
            'fullName' => 'Warga',
            'id_rt' => $rt->id,
            'telephone_number' => '081234567890',
            'address' => 'Gresik Kota',
            'id_gender' => 1
        ];
        $userDetail = UserDetail::factory()->warga()->create($payload);

        // 4. Lakukan login state
        $this->actingAs($user);


        return $userDetail;
    }
}
