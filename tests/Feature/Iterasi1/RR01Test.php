<?php

use App\Models\User;
use App\Models\UserDetail;

test('Seluruh data valid', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $user = User::factory()->create([
        'email' => 'ketuarw@gmail.com',
        'password' => bcrypt('ketuarw123')
    ]);

    $payload = [
        'id_user' => $user->id,
        'id_gender' => 2,
        'userName' => 'ketuarw',
        'fullName' => 'Ketua RW',
        'id_rt' => 2,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',

    ];
    $userDetail = UserDetail::factory()->ketuaRW()->create($payload);

    $response = $this->post('/login', [
        'email' => 'ketuarw@gmail.com',
        'password' => 'ketuarw123'
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertAuthenticatedAs($user);
    // 4. Bertindak sebagai user ini
    $response = $this->actingAs($user)->post('/KetuaRW/Dashboard');
});

test('Seluruh field kosong', function () {
    $user = User::factory()->create();

    $response =  $this->post('/login', [
        'email' => '',
        'password' => '',
    ]);

    $response->assertSessionHasErrors(['email', 'password']);
});


test('Format Field Salah', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => 'muhammaddzulfiqar',
        'password' => 123456,
    ]);

    $response->assertSessionHasErrors(['email', 'password']);
});

test('Email Tidak Terdaftar', function () {

    $response = $this->post('/login', [
        'email' => 'jul@gmail.com',
        'password' => '12345678',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('Dapat Logout dan seluruh session terhapus', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $user = User::factory()->create([
        'email' => 'ketuarw@gmail.com',
        'password' => bcrypt('ketuarw123')
    ]);

    $payload = [
        'id_user' => $user->id,
        'id_roles' => 1,
        'id_gender' => 2,
        'status' => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'userName' => 'ketuarw',
        'fullName' => 'Ketua RW',
        'id_rt' => 2,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',

    ];

    $userDetail = UserDetail::factory()->create($payload);

    $response = $this->post('/login', [
        'email' => 'ketuarw@gmail.com',
        'password' => 'ketuarw123'
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertAuthenticatedAs($user);
    // 4. Bertindak sebagai user ini
    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/login');
});
