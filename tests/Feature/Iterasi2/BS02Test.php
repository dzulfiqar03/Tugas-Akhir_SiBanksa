<?php

use App\Models\BankSampah\Sampah;

test('Seluruh data valid', function () {

    $user = $this->loginAsBankSampah();

    $responseCreateKepengurusan = $this->post('bank-sampah/kepengurusan/create', [
        'id_userdetail' => $user->id,
        'userName' => 'nahisa21',
        'fullName' => 'Nahisa Alya',
        'address' => 'Gresik Kota',
        'phoneNumber' => '0878908890890',
        'id_gender' => 2,
        'divisi' => 'Ketua'
    ]);

    $responseCreateKepengurusan->assertSessionHasNoErrors();
});

test('Data dapat diperbarui', function () {
    // 1. Setup: Login
    $user = $this->loginAsBankSampah();

    $kepengurusan = \App\Models\BankSampah\Kepengurusan::factory()->create([
        'id_userdetail' => $user->id,
        'userName' => 'nahisa21',
        'fullName' => 'Nahisa Alya',
        'address' => 'Gresik Kota',
        'telephone_number' => '0878908890890',
        'id_gender' => 2,
        'divisi' => 'Ketua'
    ]);

    $updateResponse = $this->put("/bank-sampah/kepengurusan/update/{$kepengurusan->id}", [
        'id_userdetail' => $user->id,
        'userName' => 'nahisa21',
        'fullName' => 'Nahiza Alya',
        'address' => 'Gresik Kota',
        'phoneNumber' => '0878908890890',
        'id_gender' => 2,
        'divisi' => 'Ketua'
    ]);

    $updateResponse->assertSessionHasNoErrors();

    $this->assertDatabaseHas('kepengurusans', [
        'id' => $kepengurusan->id,
        'fullName' => 'Nahiza Alya'
    ]);
});

test('Data dapat dihapus', function () {
    // 1. Setup: Login
    $user = $this->loginAsBankSampah();

    $kepengurusan = \App\Models\BankSampah\Kepengurusan::factory()->create([
        'id_userdetail' => $user->id,
        'userName' => 'nahisa21',
        'fullName' => 'Nahisa Alya',
        'address' => 'Gresik Kota',
        'telephone_number' => '0878908890890',
        'id_gender' => 2,
        'divisi' => 'Ketua'
    ]);

    $deleteResponse = $this->delete("/bank-sampah/kepengurusan/delete/{$kepengurusan->id}");

    $deleteResponse->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('kepengurusans', [
        'id' => $kepengurusan->id
    ]);
});

test('Data Field Duplikat', function () {
    $user = $this->loginAsBankSampah();

    $divisiTarget = 'Ketua';

    $formData = [
       'id_userdetail' => $user->id,
        'userName' => 'nahisa21',
        'fullName' => 'Nahisa Alya',
        'address' => 'Gresik Kota',
        'phoneNumber' => '0878908890890',
        'id_gender' => 2,
        'divisi' => $divisiTarget
    ];



    // Kirim pertama (Berhasil)
    $this->post('bank-sampah/kepengurusan/create', $formData);

    // Kirim kedua (Duplikat)
    $response = $this->post('bank-sampah/kepengurusan/create', $formData);

    // Perhatikan key-nya: bankSampah.email
    $response->assertSessionHasErrors(['divisi']);
});

test('Seluruh field kosong', function () {

    $user = $this->loginAsBankSampah();

    $responseCreateKepengurusan = $this->post('bank-sampah/kepengurusan/create', [
        'id_userdetail' => $user->id,
        'userName' => '',
        'fullName' => '',
        'address' => '',
        'phoneNumber' => '',
        'id_gender' => 2,
        'divisi' => ''
    ]);

    $responseCreateKepengurusan->assertSessionHasErrors(['userName', 'fullName', 'phoneNumber', 'divisi']);
});


test('Format Field Salah', function () {
    $user = $this->loginAsBankSampah();

    $responseCreateKepengurusan = $this->post('bank-sampah/kepengurusan/create', [
        'id_userdetail' => $user->id,
        'userName' => 0,
        'fullName' => 0,
        'address' => 0,
        'phoneNumber' => 0,
        'id_gender' => 2,
        'divisi' => 0
    ]);

    $responseCreateKepengurusan->assertSessionHasErrors(['userName', 'fullName', 'phoneNumber', 'divisi']);
});
