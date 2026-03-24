<?php

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

test('Data Field Duplikat', function () {
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

test('Format Field Salah', function () {
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

