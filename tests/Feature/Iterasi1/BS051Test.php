<?php

use App\Models\User;
use App\Models\UserDetail;

test('Seluruh data valid', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $rt = \App\Models\RTPerumahan::first();
    $gender = \App\Models\Gender::where('gender', 'Laki-Laki')->first();
    // STRUKTUR HARUS SEPERTI INI (Sesuai RegisterRequest Anda)
    $formData = [
        'id_roles' => 2, // Role Nasabah
        'id_gender' => 3,
        'status' => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via' => 'Non-Tunai',

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

test('Data Field Duplikat', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $gender = \App\Models\Gender::first();
    $rt = \App\Models\RTPerumahan::first();

    $emailTarget = 'muhammaddzulfiqar03@gmail.com';

    $formData = [
        'id_roles' => 3, // Role Nasabah
        'status' => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via' => 'Non-Tunai',
        'nasabah' => [
            'userName' => 'dzulfiqar_nasabah',
            'fullName' => 'Muhammad Dzulfiqar',
            'email' => $emailTarget,
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

    // Perhatikan key-nya: bankSampah.email
    $response->assertSessionHasErrors(['nasabah.email']);
});

test('Format Field Salah', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $rt = \App\Models\RTPerumahan::first();
    $gender = \App\Models\Gender::where('gender', 'Laki-Laki')->first();


    $formData = [
        'id_roles' => 2, // Role Nasabah
        'id_gender' => $gender->id,
        'status' => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via' => 'Non-Tunai',
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

test('Seluruh field kosong', function () {
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
        'pencairan_via' => '',
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
