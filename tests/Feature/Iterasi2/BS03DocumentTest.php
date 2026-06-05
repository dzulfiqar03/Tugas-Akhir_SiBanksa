<?php

use App\Models\BankSampah\Sampah;
use App\Models\DocumentArchiver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('Seluruh dokumen valid', function () {

    $user = $this->loginAsBankSampah();

    $jadwal = \App\Models\BankSampah\JadwalPelaksanaan::factory()->create([
        'id_userdetail' => $user->id,
        'tanggal_setoran' => '2027-01-01'
    ]);

    Storage::fake('public');

    $file = UploadedFile::fake()->create('dokumen_nasabah.pdf', 500);

    $responseCreateDocument = $this->post('bank-sampah/document/create', [
        'id_userdetail' => $user->id,
        'id_jadwal'     => $jadwal->id,
        'name'          => 'haakaolo',
        'fileDoc'       => [$file]
    ]);

    $responseCreateDocument->assertSessionHasNoErrors();
});


test('Data dapat dihapus', function () {
    // 1. Setup: Login
    $user = $this->loginAsBankSampah();

    $jadwal = \App\Models\BankSampah\JadwalPelaksanaan::factory()->create([
        'id_userdetail' => $user->id,
        'tanggal_setoran' => '2027-01-01'
    ]);
    Storage::fake('public');

    $file = UploadedFile::fake()->create('dokumen_nasabah.pdf', 500);
    $responseCreateDocument =  DocumentArchiver::factory()->create([
        'id_userdetail' => $user->id,
        'id_jadwal'     => $jadwal->id,
        'name'          => 'haakaolo',
        'original_filesname'       => $file,
        'encrypted_filesname' => 'smsmssksmsksm'
    ]);

    $deleteResponse = $this->delete("/bank-sampah/document/delete/{$responseCreateDocument->id}");

    $deleteResponse->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('document_archivers', [
        'id' => $responseCreateDocument->id
    ]);
});

test('Seluruh field kosong', function () {

    $user = $this->loginAsBankSampah();


    $responseCreateDocument = $this->post('bank-sampah/document/create', [
        'id_userdetail' => null,
        'id_jadwal'     => null,
        'name'          => '',
        'fileDoc'       => []
    ]);

    $responseCreateDocument->assertSessionHasErrors(['id_userdetail', 'id_jadwal', 'name', 'fileDoc']);
});


test('Format Field Salah', function () {
    $user = $this->loginAsBankSampah();

    Storage::fake('public');

    $file = UploadedFile::fake()->create('dokumen_nasabah.pdf', 500);
    $responseCreateDocument = $this->post('bank-sampah/document/create', [
        'id_userdetail' => 'mkmkmkm',
        'id_jadwal'     => 'abc',
        'name'          => 2,
        'fileDoc'       => $file
    ]);


    $responseCreateDocument->assertSessionHasErrors(['id_userdetail', 'id_jadwal', 'name', 'fileDoc']);
});
