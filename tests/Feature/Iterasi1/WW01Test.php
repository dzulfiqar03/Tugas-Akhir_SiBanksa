<?php

use App\Models\User;
use App\Models\UserDetail;


test('Seluruh data valid', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $user = User::factory()->create([
        'email' => 'muhammaddzulfiqar03@gmail.com',
        'password' => bcrypt('dzulfiqar123')
    ]);

    $payload = [
        'id_user' => $user->id,
        'id_gender' => 2,
        'userName' => 'dzulfiqar03',
        'fullName' => 'Muhammad Dzulfiqar',
        'id_rt' => 2,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',

    ];

    $userDetail = UserDetail::factory()->warga()->create($payload);

    $response = $this->post('/login', [
        'email' => 'muhammaddzulfiqar03@gmail.com',
        'password' => 'dzulfiqar123'
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertAuthenticatedAs($user);
    // 4. Bertindak sebagai user ini
    $response = $this->actingAs($user)->post('/Warga/Dashboard');
});

test('Seluruh field kosong', function () {
    $user = User::factory()->create();

    $response =  $this->post('/login', [
        'email' => '',
        'password' => '',
    ]);

    $response->assertSessionHasErrors();
});


test('Format Field Salah', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => 'muhammaddzulfiqar03',
        'password' => 123456,
    ]);

    $response->assertSessionHasErrors(['email', 'password']);
});

test('Email Tidak Terdaftar', function () {

    $response = $this->post('/login', [
        'email' => 'muhammaddzulfiqar23@gmail.com',
        'password' => 'dzul12345',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('Seluruh data register valid', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $rt = \App\Models\RTPerumahan::first();
    $gender = \App\Models\Gender::where('gender', 'Laki-Laki')->first();
    // STRUKTUR HARUS SEPERTI INI (Sesuai RegisterRequest Anda)
    $formData = [
        'email' => 'muhammaddzulfiqar03gmail.com',
        'password' => '12345678',
        'id_roles' => 3, // Role Nasabah
        'status' => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
        'nasabah' => [
            'userName' => 'dzulfiqar_nasabah',
            'fullName' => 'Muhammad Dzulfiqar',
            'email' => 'dzulfiqar@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'id_rt' => $rt->id,
            'id_gender' => $gender->id,
            'phoneNumber' => '081234567890',
            'address' => 'Gresik Kota',
        ]
    ];

    $response = $this->post('/register', $formData);

    // Debugging: Jika masih error, buka baris di bawah ini
    // dd(session()->get('errors')->getMessages());

    $response->assertSessionHasNoErrors();
});

test('Data Field Register Duplikat', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $gender = \App\Models\Gender::first();
    $rt = \App\Models\RTPerumahan::first();

    $emailTarget = 'dzulfiqar@gmail.com';

    $formData = [
        'id_roles' => 3,
        'status' => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
        'nasabah' => [
            'userName' => 'dzulfiqar_nasabah',
            'fullName' => 'Muhammad Dzulfiqar',
            'email' => $emailTarget, // Ini yang akan dicek uniknya
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'id_rt' => $rt->id,
            'id_gender' => $gender->id,

            'phoneNumber' => '081234567890',
            'address' => 'Gresik Kota',
        ]
    ];

    // Kirim pertama (Berhasil)
    $this->post('/register', $formData);

    // Kirim kedua (Duplikat)
    $response = $this->post('/register', $formData);

    $response->assertSessionHasErrors(['nasabah.email']);
});

test('Format Field Register Salah', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $rt = \App\Models\RTPerumahan::first();
    $gender = \App\Models\Gender::where('gender', 'Laki-Laki')->first();


    $formData = [
        'id_roles' => 3, // Role Nasabah
        'status' => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
        'nasabah' => [
            'userName' => 'dzulfiqar_nasabah',
            'fullName' => 'Muhammad Dzulfiqar',
            'email' => 'dzulfiqar',
            'password' => '123',
            'password_confirmation' => 'password123',
            'id_rt' => $rt->id,
            'id_gender' => $gender->id,

            'phoneNumber' => 0,
            'address' => 'Gresik Kota',
        ]
    ];

    $response = $this->post('/register', $formData);
    // Debugging: Jika masih error, buka baris di bawah ini
    // dd(session()->get('errors')->getMessages());
    $response->assertSessionHasErrors(['nasabah.email', 'nasabah.password', 'nasabah.phoneNumber']);
});

test('Seluruh field register kosong', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $rt = \App\Models\RTPerumahan::first();
    $gender = \App\Models\Gender::where('gender', 'Laki-Laki')->first();
    // STRUKTUR HARUS SEPERTI INI (Sesuai RegisterRequest Anda)
    $formData = [
        'email' => '',
        'password' => '',
        'id_roles' => null, // Role Nasabah
        'status' => '',
        'status_transaction' => '',
        'nasabah' => [
            'userName' => '',
            'fullName' => '',
            'email' => '',
            'password' => '',
            'id_gender' => $gender->id,
            'password_confirmation' => '',
            'id_rt' => $rt->id,
            'phoneNumber' => '',
            'address' => '',
        ]
    ];

    $response = $this->post('/register', $formData);

    // Debugging: Jika masih error, buka baris di bawah ini
    // dd(session()->get('errors')->getMessages());

    $response->assertSessionHasErrors();
});


test('Dapat Logout dan seluruh session terhapus', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $user = User::factory()->create([
        'email' => 'muhammaddzulfiqar03@gmail.com',
        'password' => bcrypt('muhammaddzulfiqar123')
    ]);

    $payload = [
        'id_user' => $user->id,
        'id_roles' => 3,
        'id_gender' => 2,
        'status' => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'userName' => 'muhammaddzulfiqar03',
        'fullName' => 'Muhammad Dzulfiqar',
        'id_rt' => 2,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',

    ];

    $userDetail = UserDetail::factory()->create($payload);

    $response = $this->post('/login', [
        'email' => 'muhammaddzulfiqar03@gmail.com',
        'password' => 'muhammaddzulfiqar123'
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertAuthenticatedAs($user);
    // 4. Bertindak sebagai user ini
    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/login');
});
