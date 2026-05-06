<?php

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\Sampah;
use App\Models\Gender;
use App\Models\Roles;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Hash;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);
test('Model Sampah memiliki relasi yang valid dengan Model User Detail', function () {
  $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    // 1. Buat User induk terlebih dahulu
    $user = User::factory()->create([
        'email' => 'banksampah@gmail.com',
        'password' => Hash::make('banksampah123')
    ]);

    // 2. Buat Role
    $role = Roles::factory()->create(['role' => 'Bank Sampah']);

    $rt = \App\Models\RTPerumahan::first();

    $userDetail = UserDetail::create([
        'id_user'  => $user->id, // Ini yang tadi kosong/null
        'userName' => 'banksampah',
        'fullName' => 'Bank Sampah',
        'id_gender' => 1,
        'id_rt' => $rt->id,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',
        'id_roles' => $role->id,
        'pencairan_via' => 'Non-Tunai',
        'status' => 'Disetujui',
        'status_transaction' => 'Disetujui'
    ]);

        $sampah = Sampah::factory()->create([
        'id_userdetail' => $userDetail->id,
        'nama_sampah' => 'Plastik',
        'harga' => 1000,
        'saldo' => 5000,
        'satuan' => 'Kg',
        'kategori' => 'Non Daur Ulang',
    ]);

    // Ambil user dengan relasinya
    $userWithSampah = UserDetail::with('sampah')->find($userDetail->id);

    expect($userWithSampah->sampah->first())->toBeInstanceOf(Sampah::class);
});


test('Sampah bernilai true jika tersimpan sampah dengan id user yang sama', function () {
     $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    // 1. Buat User induk terlebih dahulu
    $user = User::factory()->create([
        'email' => 'banksampah@gmail.com',
        'password' => Hash::make('banksampah123')
    ]);

    // 2. Buat Role
    $role = Roles::factory()->create(['role' => 'Bank Sampah']);

    $rt = \App\Models\RTPerumahan::first();

    $userDetail = UserDetail::create([
        'id_user'  => $user->id, // Ini yang tadi kosong/null
        'userName' => 'banksampah',
        'fullName' => 'Bank Sampah',
        'id_gender' => 1,
        'id_rt' => $rt->id,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',
        'id_roles' => $role->id,
        'pencairan_via' => 'Non-Tunai',
        'status' => 'Disetujui',
        'status_transaction' => 'Disetujui'
    ]);

    $sampah = Sampah::factory()->create([
        'id_userdetail' => $userDetail->id,
        'nama_sampah' => 'Plastik',
        'harga' => 1000,
        'saldo' => 5000,
        'satuan' => 'Kg',
        'kategori' => 'Non Daur Ulang',
    ]);
    expect($sampah->id_userdetail === $userDetail->id)->toBeTrue();
});

