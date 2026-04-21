<?php

use App\Models\User;
use App\Models\UserDetail;

test('Seluruh data valid', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $rt = \App\Models\RTPerumahan::first();
    $gender = \App\Models\Gender::where('gender', 'Laki-Laki')->first();
    // STRUKTUR HARUS SEPERTI INI (Sesuai RegisterRequest Anda)
    $formData = [
        'fullName' => 'Petugas Bank Sampah XYZ',
        'id_rt' => $rt->id,
        'id_roles' => 2,
        'id_gender' => 3,
        'phoneNumber' => '081234567890',
        'status' => 'Pengajuan Verifikasi',
    ];

    $response = $this->post('/bank-sampah/nasabah/create', $formData);


    $response->assertSessionHasNoErrors();
});

test('Data dapat diperbarui', function () {
    // 1. Setup: Login
    $this->loginAsBankSampah();


    $user = User::factory()->create([
        'email' => 'muhammaddzulfiqar03@gmail.com',
        'password' => bcrypt('dzulfiqar123')
    ]);

    $payload = [
        'id_user' => $user->id,
        'id_gender' => 2,
        'userName' => 'dzulfiqar03',
        'fullName' => 'Muhammad Dzulfiqar',
        'id_roles' => 3,
        'id_rt' => 2,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',
        'status' => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via' => 'Non-Tunai'
    ];

    $userDetail = UserDetail::factory()->warga()->create($payload);

    $updateResponse = $this->put("/bank-sampah/nasabah/update/{$user->id}", [
        'id_gender' => 2,
        'fullName' => 'Muhammad Dzulfiqar ganteng',
        'status' => 'Disetujui',
        'id_roles' => 3,
        'id_rt' => 2,
        'phoneNumber' => '081234567890',
    ]);

    $updateResponse->assertSessionHasNoErrors();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'muhammaddzulfiqar03@gmail.com'
    ]);
});

test('Data dapat dihapus', function () {
    // 1. Setup: Login
    $this->loginAsBankSampah();


    $user = User::factory()->create([
        'email' => 'muhammaddzulfiqar03@gmail.com',
        'password' => bcrypt('dzulfiqar123')
    ]);

    $rt = \App\Models\RTPerumahan::first();
    $gender = \App\Models\Gender::where('gender', 'Laki-Laki')->first();


    $payload = [
        'id_user' => $user->id,
        'id_gender' => 2,
        'userName' => 'dzulfiqar03',
        'fullName' => 'Muhammad Dzulfiqar',
        'id_roles' => 3,
        'id_rt' => $rt->id,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',
        'status' => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via' => 'Non-Tunai'
    ];

    $userDetail = UserDetail::factory()->warga()->create($payload);


    $deleteResponse = $this->delete("/bank-sampah/nasabah/delete/{$user->id}");

    $deleteResponse->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('users', [
        'id' => $user->id
    ]);
});



test('Format Field Salah', function () {

    $this->loginAsBankSampah();
    $formData = [
        'fullName' => 2,
        'id_rt' => 999,
        'id_roles' => 2,
        'id_gender' => 3,
        'phoneNumber' => 'abs',
        'status' => 'Pengajuan Verifikasi',
    ];

    $response = $this->post('/bank-sampah/nasabah/create', $formData);
    // Debugging: Jika masih error, buka baris di bawah ini
    // dd(session()->get('errors')->getMessages());
    $response->assertSessionHasErrors(['fullName', 'phoneNumber']);
});

test('Seluruh field kosong', function () {
    $this->loginAsBankSampah();
    $formData = [
         'fullName' => '',
        'id_rt' => null,
        'id_roles' => null,
        'id_gender' => null,
        'phoneNumber' => '',
        'status' => '',
    ];

    $response = $this->post('/bank-sampah/nasabah/create', $formData);

    // Debugging: Jika masih error, buka baris di bawah ini
    // dd(session()->get('errors')->getMessages());

    $response->assertSessionHasErrors();
});
