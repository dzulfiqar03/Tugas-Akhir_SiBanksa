<?php

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\Kepengurusan;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\BankSampah\Sampah;
use App\Models\Gender;
use App\Models\Roles;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\BankSampah\KepengurusanServices;
use App\Services\BankSampah\PencatatanServices;
use Illuminate\Support\Facades\Hash;


test('Model User Detail memiliki relasi yang valid dengan model Kepengurusan', function () {
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

    $pengurus = Kepengurusan::create([
        'id_userdetail' => $userDetail->id,
        'userName' => 'nahisa21',
        'fullName' => 'Nahisa Alya',
        'address' => 'Gresik Kota',
        'telephone_number' => '0878908890890',
        'id_gender' => 2,
        'divisi' => 'Ketua'
    ]);


    $userWithPengurus = UserDetail::with('kepengurusan')->find($userDetail->id);


    expect($userWithPengurus->kepengurusan->first())->toBeInstanceOf(Kepengurusan::class);
});

test('Model Kepengurusan memiliki relasi yang valid dengan model Gender', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    // 1. Buat User induk terlebih dahulu
    $user = User::factory()->create([
        'email' => 'banksampah@gmail.com',
        'password' => Hash::make('banksampah123')
    ]);

    // 2. Buat Role
    $role = Roles::where('role', 'Bank Sampah')->first();

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


    $pengurus = Kepengurusan::create([
        'id_userdetail' => $userDetail->id,
        'userName' => 'nahisa21',
        'fullName' => 'Nahisa Alya',
        'address' => 'Gresik Kota',
        'telephone_number' => '0878908890890',
        'id_gender' => 2,
        'divisi' => 'Ketua'
    ]);


    $userPengurus = Kepengurusan::with('gender')->find($pengurus->id);

    expect($userPengurus->gender->first())->toBeInstanceOf(Gender::class);
});

test('Ketua hanya dapat dimasukkan 1 kali', function () {
    // 1. Setup Lingkungan
    $this->loginAsBankSampah();


    // 1. Buat User induk terlebih dahulu
    $user = User::factory()->create([
        'email' => 'banksampah@gmail.com',
        'password' => Hash::make('banksampah123')
    ]);

    // 2. Buat Role
    $role = Roles::where('role', 'Bank Sampah')->first();

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



    $payload = [
        'id_userdetail' => $userDetail->id,
        'userName' => 'nahisa21',
        'fullName' => 'Nahisa Alya',
        'address' => 'Gresik Kota',
        'phoneNumber' => '0878908890890',
        'id_gender' => 2,
        'divisi' => 'Ketua'
    ];

    // 2. Eksekusi Service
    $service = app(KepengurusanServices::class);
    $service->createKepengurusan($payload);

    // 2. Eksekusi kedua dengan payload yang sama (Harus Error)
    // Bungkus dalam fn() agar Exception bisa ditangkap oleh expect
    expect(fn() => $service->createKepengurusan($payload))
        ->toThrow(\Illuminate\Database\QueryException::class);
});
