<?php

use App\Models\User;
use App\Models\UserDetail;



test('Validasi Bank Sampah Sukses', function () {
    // 1. Setup: Login
    $this->loginAsKetuaRW();


        $user = User::factory()->create([
        'email' => 'banksampah03@gmail.com',
        'password' => bcrypt('banksampah123')
    ]);

    $payload = [
        'id_user' => $user->id,
        'id_gender' => 3,
        'userName' => 'banksampah03',
        'fullName' => 'Bank Sampah Basmi',
        'id_roles' => 2,
        'id_rt' => 7,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',
        'status' => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via' => 'Non-Tunai'
    ];

    $userDetail = UserDetail::factory()->bankSampah()->create($payload);

    $updateResponse = $this->put("KetuaRW/bank-sampah/nasabah/update/{$user->id}", [

        'id_gender' => 2,
        'fullName' => 'Bank Sampah Basmi',
        'status' => 'Disetujui',
        'id_roles' => 3,
        'id_rt' => 7,

    ]);

    $updateResponse->assertSessionHasNoErrors();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'banksampah03@gmail.com'
    ]);
});

