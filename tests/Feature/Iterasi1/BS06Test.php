<?php

use App\Models\BankSampah\JadwalPelaksanaan;

test('Seluruh data valid', function () {

    $user = $this->loginAsBankSampah();

    $responseCreateDate = $this->post('bank-sampah/Jadwal/Create', [
        'id_userdetail' => $user->id,
        'tanggal_setoran' => '2027-01-01'
    ]);

    $responseCreateDate->assertSessionHasNoErrors();
});

test('Data dapat diperbarui', function () {
    // 1. Setup: Login
    $user = $this->loginAsBankSampah();

    $jadwal = \App\Models\BankSampah\JadwalPelaksanaan::factory()->create([
        'id_userdetail' => $user->id,
        'tanggal_setoran' => '2027-01-01'
    ]);

    $updateResponse = $this->put("/bank-sampah/Jadwal/Update/{$jadwal->id}", [
        'id_userdetail' => $user->id,
        'tanggal_setoran' => '2026-05-25' // Data baru
    ]);

    $updateResponse->assertSessionHasNoErrors();

    $this->assertDatabaseHas('jadwal_pelaksanaan', [
        'id' => $jadwal->id,
        'tanggal_setoran' => '2026-05-25'
    ]);
});

test('Data dapat dihapus', function () {
    // 1. Setup: Login
    $user = $this->loginAsBankSampah();

    $jadwal = JadwalPelaksanaan::factory()->create([
        'id_userdetail' => $user->id,
        'tanggal_setoran' => '2027-01-01'
    ]);

    $deleteResponse = $this->delete("/bank-sampah/Jadwal/Delete/{$jadwal->id}");

    $deleteResponse->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('jadwal_pelaksanaan', [
        'id' => $jadwal->id
    ]);
});

test('Seluruh field kosong', function () {

    $user = $this->loginAsBankSampah();

    $responseCreateDate = $this->post('bank-sampah/Jadwal/Create', [
        'id_userdetail' => null,
        'tanggal_setoran' => ''
    ]);

    $responseCreateDate->assertSessionHasErrors(['id_userdetail', 'tanggal_setoran']);
});


test('Format Field Salah', function () {
    $user = $this->loginAsBankSampah();

    $responseCreateDate = $this->post('bank-sampah/Jadwal/Create', [
        'id_userdetail' => $user->id,
        'tanggal_setoran' => 0
    ]);

    $responseCreateDate->assertSessionHasErrors(['tanggal_setoran']);
});


test('Data Field Duplikat', function () {
    $user = $this->loginAsBankSampah();

    $tanggal_setoran = '2027-01-01';
    $responseCreateDate = $this->post('bank-sampah/Jadwal/Create', [
        'id_userdetail' => $user->id,
        'tanggal_setoran' => $tanggal_setoran
    ]);

    $responseCreateDate = $this->post('bank-sampah/Jadwal/Create', [
        'id_userdetail' => $user->id,
        'tanggal_setoran' => $tanggal_setoran
    ]);

    $responseCreateDate->assertSessionHasErrors();
});
