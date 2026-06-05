<?php

use App\Models\Gender;
use App\Models\Roles;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Hash;

test('Model mengembalikan true jika roles sebagai Ketua RW berupa role = "Ketua RW"', function () {
    $user = new User();
    $user->user_detail = new UserDetail([
        'id_roles' => 1
    ]);
    expect($user->user_detail->id_roles == 1)->toBeTrue();
});


test('Model mengembalikan false jika roles bukan Ketua RW', function () {
    $user = new User();
    $user->user_detail = new UserDetail([
        'id_roles' => 2
    ]);
    expect($user->user_detail->id_roles == 1)->toBeFalse();
});

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

test('Model User Detail memiliki relasi yang valid dengan model Roles', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    // 1. Buat User induk terlebih dahulu
    $user = User::factory()->create([
        'email' => 'ketuarw@gmail.com',
        'password' => Hash::make('ketuarw123')
    ]);

    // 2. Buat Role
    $role = Roles::factory()->create(['role' => 'Ketua RW']);

    $rt = \App\Models\RTPerumahan::first();

    $userDetail = UserDetail::create([
        'id_user'  => $user->id, // Ini yang tadi kosong/null
        'userName' => 'ketuarw',
        'fullName' => 'Ketua RW',
        'id_gender' => 1,
        'id_rt' => $rt->id,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',
        'id_roles' => $role->id,
        'pencairan_via' => 'Non-Tunai',
        'status' => 'Disetujui',
        'status_transaction' => 'Disetujui'
    ]);

    expect($userDetail->roles)->toBeInstanceOf(Roles::class);
});

test('Model User Detail memiliki relasi yang valid dengan model RT', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    // 1. Buat User induk terlebih dahulu
    $user = User::factory()->create([
        'email' => 'ketuarw@gmail.com',
        'password' => Hash::make('ketuarw123')
    ]);

    // 2. Buat Role
    $role = Roles::where('role', 'Ketua RW')->first();

    $rt = \App\Models\RTPerumahan::first();

    $userDetail = UserDetail::create([
        'id_user'  => $user->id, // Ini yang tadi kosong/null
        'userName' => 'ketuarw',
        'fullName' => 'Ketua RW',
        'id_gender' => 1,
        'id_rt' => $rt->id,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',
        'id_roles' => $role->id,
        'pencairan_via' => 'Non-Tunai',
        'status' => 'Disetujui',
        'status_transaction' => 'Disetujui'
    ]);

    expect($userDetail->rt)->toBeInstanceOf(\App\Models\RTPerumahan::class);
});

test('Model User Detail memiliki relasi yang valid dengan model Gender', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    // 1. Buat User induk terlebih dahulu
    $user = User::factory()->create([
        'email' => 'ketuarw@gmail.com',
        'password' => Hash::make('ketuarw123')
    ]);

    // 2. Buat Role
    $role = Roles::where('role', 'Ketua RW')->first();

    $rt = \App\Models\RTPerumahan::first();

    $gender = Gender::first();
    $userDetail = UserDetail::create([
        'id_user'  => $user->id, // Ini yang tadi kosong/null
        'userName' => 'ketuarw',
        'fullName' => 'Ketua RW',
        'id_gender' => $gender->id,
        'id_rt' => $rt->id,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',
        'id_roles' => $role->id,
        'pencairan_via' => 'Non-Tunai',
        'status' => 'Disetujui',
        'status_transaction' => 'Disetujui'
    ]);

    expect($userDetail->gender)->toBeInstanceOf(\App\Models\Gender::class);
});
