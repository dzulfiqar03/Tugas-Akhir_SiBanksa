<?php

use App\Models\BankSampah\Sampah;

test('Seluruh data valid', function () {

    $user = $this->loginAsBankSampah();

    $responseCreateSampah = $this->post('bank-sampah/Sampah/Create', [
        'id_userdetail' => $user->id,
        'nama_sampah' => 'Plastik',
        'satuan' => 'Lusin',
        'kategori' => 'Non Daur Ulang',
        'harga' => 124000,
        'saldo' => 2000,
    ]);

    $responseCreateSampah->assertSessionHasNoErrors();
});

test('Data dapat diperbarui', function () {
    // 1. Setup: Login
    $user = $this->loginAsBankSampah();

    $sampah = \App\Models\BankSampah\Sampah::factory()->create([
        'id_userdetail' => $user->id,
        'nama_sampah' => 'Plastik',
        'satuan' => 'Lusin',
        'kategori' => 'Non Daur Ulang',
        'harga' => 124000,
        'saldo' => 2000,
    ]);

    $updateResponse = $this->put("/bank-sampah/Sampah/Update/{$sampah->id}", [
        'id_userdetail' => $user->id,
        'nama_sampah' => 'Plastik',
        'satuan' => 'Lusin',
        'kategori' => 'Non Daur Ulang',
        'harga' => 12000,
        'saldo' => 2000,
    ]);

    $updateResponse->assertSessionHasNoErrors();

    $this->assertDatabaseHas('sampah', [
        'id' => $sampah->id,
        'nama_sampah' => 'Plastik'
    ]);
});

test('Data dapat dihapus', function () {
    // 1. Setup: Login
    $user = $this->loginAsBankSampah();

    $sampah = Sampah::factory()->create([
        'id_userdetail' => $user->id,
        'nama_sampah' => 'Plastik',
        'satuan' => 'Lusin',
        'kategori' => 'Non Daur Ulang',
        'harga' => 124000,
        'saldo' => 2000,
    ]);

    $deleteResponse = $this->delete("/bank-sampah/Sampah/Delete/{$sampah->id}");

    $deleteResponse->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('sampah', [
        'id' => $sampah->id
    ]);
});

test('Seluruh field kosong', function () {

    $user = $this->loginAsBankSampah();

    $responseCreateSampah = $this->post('bank-sampah/Sampah/Create', [
        'id_userdetail' => $user->id,
        'nama_sampah' => '',
        'satuan' => '',
        'kategori' => '',
        'harga' => null,
        'saldo' => null,
    ]);

    $responseCreateSampah->assertSessionHasErrors(['nama_sampah', 'satuan', 'kategori', 'harga', 'saldo']);
});


test('Format Field Salah', function () {
    $user = $this->loginAsBankSampah();

    $responseCreateSampah = $this->post('bank-sampah/Sampah/Create', [
        'id_userdetail' => $user->id,
        'nama_sampah' => [],
        'satuan' => 12345,
        'kategori' => 'hahah',
        'harga' => 'hahah',
        'saldo' => 'hahah',
    ]);

    $responseCreateSampah->assertSessionHasErrors(['nama_sampah', 'harga', 'saldo']);
});
