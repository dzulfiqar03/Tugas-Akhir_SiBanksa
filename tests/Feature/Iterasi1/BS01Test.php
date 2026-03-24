<?php

use App\Models\User;
use App\Models\UserDetail;


test('Seluruh data valid', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $user = User::factory()->create([
        'email' => 'banksampah03@gmail.com',
        'password' => bcrypt('banksampah123')
    ]);

    $rt = \App\Models\RTPerumahan::first();

    $payload = [
        'id_user' => $user->id,
        'userName' => 'banksampah03',
        'fullName' => 'Petugas Bank Sampah XYZ',
        'id_rt' => $rt->id,
        'telephone_number' => 0,
        'address' => 'Gresik Kota',

    ];

    $userDetail = UserDetail::factory()->bankSampah()->create($payload);

    $response = $this->post('/login', [
        'email' => 'banksampah03@gmail.com',
        'password' => 'banksampah123'
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertAuthenticatedAs($user);
    // 4. Bertindak sebagai user ini
    $response = $this->actingAs($user)->post('/banksampah03/Dashboard');
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
        'email' => 'banksampah03',
        'password' => 123456,
    ]);

    $response->assertSessionHasErrors(['email', 'password']);
});

test('Email Tidak Terdaftar', function () {

    $response = $this->post('/login', [
        'email' => 'banksampah23@gmail.com',
        'password' => 'banksampah123',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('Seluruh data register valid', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $rt = \App\Models\RTPerumahan::first();
    $gender = \App\Models\Gender::where('gender', 'Laki-Laki')->first();
    // STRUKTUR HARUS SEPERTI INI (Sesuai RegisterRequest Anda)
    $formData = [
        'id_roles' => 2, // Role Nasabah
        'id_gender' => 3,
        'status' => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
        'bankSampah' => [
            'userName' => 'banksampah03',
            'fullName' => 'Petugas Bank Sampah XYZ',
            'email' => 'banksampah03@gmail.com',
            'password' => 'banksampah123',
            'password_confirmation' => 'banksampah123',
            'id_rt' => $rt->id,
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

    $emailTarget = 'banksampah03@gmail.com';

    $formData = [
        'id_roles' => 2, // Role Nasabah
        'id_gender' => $gender->id,
        'status' => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
        'bankSampah' => [
            'userName' => 'banksampah03',
            'fullName' => 'Petugas Bank Sampah XYZ',
            'email' => $emailTarget,
            'password' => 'banksampah123',
            'password_confirmation' => 'banksampah123',
            'id_rt' => $rt->id,
            'phoneNumber' => '081234567890',
            'address' => 'Gresik Kota',
        ]
    ];

    // Kirim pertama (Berhasil)
    $this->post('/register', $formData);

    // Kirim kedua (Duplikat)
    $response = $this->post('/register', $formData);

    // Perhatikan key-nya: bankSampah.email
    $response->assertSessionHasErrors(['bankSampah.email']);
});

test('Format Field Register Salah', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $rt = \App\Models\RTPerumahan::first();
    $gender = \App\Models\Gender::where('gender', 'Laki-Laki')->first();


    $formData = [
        'id_roles' => 2, // Role Nasabah
        'id_gender' => $gender->id,
        'status' => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
        'bankSampah' => [
            'userName' => 'banksampah03',
            'fullName' => 'Petugas Bank Sampah XYZ',
            'email' => 'banksampah03',
            'password' => '123',
            'password_confirmation' => '123',
            'id_rt' => $rt->id,
            'phoneNumber' => 0,
            'address' => 'Gresik Kota',
        ]
    ];

    $response = $this->post('/register', $formData);
    // Debugging: Jika masih error, buka baris di bawah ini
    // dd(session()->get('errors')->getMessages());
    $response->assertSessionHasErrors(['bankSampah.email', 'bankSampah.password', 'bankSampah.phoneNumber']);
});

test('Seluruh field register kosong', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $rt = \App\Models\RTPerumahan::first();
    $gender = \App\Models\Gender::where('gender', 'Laki-Laki')->first();
    $formData = [
        'email' => '',
        'password' => '',
        'id_roles' => null, // Role Nasabah
        'id_gender' => '',
        'status' => '',
        'status_transaction' => '',
        'bankSampah' => [
            'userName' => '',
            'fullName' => '',
            'email' => '',
            'password' => '',
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
        'email' => 'banksampah03@gmail.com',
        'password' => bcrypt('banksampah123')
    ]);

    $payload = [
        'id_user' => $user->id,
        'userName' => 'banksampah03',
        'fullName' => 'Petugas Bank Sampah XYZ',
        'id_rt' => 2,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',

    ];

    $userDetail = UserDetail::factory()->bankSampah()->create($payload);

    $response = $this->post('/login', [
        'email' => 'banksampah03@gmail.com',
        'password' => 'banksampah123'
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertAuthenticatedAs($user);
    // 4. Bertindak sebagai user ini
    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/login');
});
