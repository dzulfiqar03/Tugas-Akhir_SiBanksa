<?php

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\Gender;
use App\Models\Roles;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Hash;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);
test('Model Jadwal Pelaksanaan memiliki relasi yang valid dengan Model User Detail', function () {
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

    JadwalPelaksanaan::factory()->create(['id_userdetail' => $userDetail->id]);

    // Ambil user dengan relasinya
    $userWithJadwal = UserDetail::with('jadwal')->find($userDetail->id);

    expect($userWithJadwal->jadwal->first())->toBeInstanceOf(JadwalPelaksanaan::class);
});


test('Jadwal bernilai true jika tersimpan jadwal dengan id user yang sama', function () {
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

    $jadwal = JadwalPelaksanaan::factory()->create([
        'id_userdetail' => $userDetail->id,
        'tanggal_setoran' => now()->toDateString(),
    ]);
    expect($jadwal->id_userdetail === $userDetail->id)->toBeTrue();
});

test('Jadwal bernilai false jika tanggal setoran lampau sebelum hari ini', function () {
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

    $jadwal = JadwalPelaksanaan::factory()->create([
        'id_userdetail' => $userDetail->id,
        'tanggal_setoran' => now()->toDateString(),
    ]);

    expect($jadwal->tanggal_setoran < now()->toDateString())->toBeFalse();
});
