<?php

use App\Models\BankSampah\Sampah;
use App\Models\DocumentArchiver;
use App\Models\EvidenceArchiver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('Seluruh evidence valid', function () {

    $user = $this->loginAsBankSampah();

    Storage::fake('public');

    $file = UploadedFile::fake()->create('evidence_nasabah.jpg', 500);

    $responseCreateEvidence = $this->post('bank-sampah/evidence/create', [
        'id_userdetail' => $user->id,
        'name'          => 'haakaolo',
        'imgEvidence'       => [$file]
    ]);

    $responseCreateEvidence->assertSessionHasNoErrors();
});


test('Data dapat dihapus', function () {
    // 1. Setup: Login
    $user = $this->loginAsBankSampah();
    Storage::fake('public');

    $file = UploadedFile::fake()->create('evidence_nasabah.jpg', 500);
    $responseCreateEvidence =  EvidenceArchiver::factory()->create([
        'id_userdetail' => $user->id,
        'name'          => 'haakaolo',
        'original_photoname'       => $file,
        'encrypted_photoname' => 'smsmssksmsksm'
    ]);

    $deleteResponse = $this->delete("/bank-sampah/evidence/delete/{$responseCreateEvidence->id}");

    $deleteResponse->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('evidence_archivers', [
        'id' => $responseCreateEvidence->id
    ]);
});

test('Seluruh field kosong', function () {

    $user = $this->loginAsBankSampah();


    $responseCreateEvidence = $this->post('bank-sampah/evidence/create', [
        'id_userdetail' => null,
        'name'          => '',
        'imgEvidence'       => []
    ]);

    $responseCreateEvidence->assertSessionHasErrors(['id_userdetail', 'name', 'imgEvidence']);
});


test('Format Field Salah', function () {
    $user = $this->loginAsBankSampah();

    Storage::fake('public');

    $file = UploadedFile::fake()->create('evidence_nasabah.pdf', 500);
    $responseCreateEvidence = $this->post('bank-sampah/evidence/create', [
        'id_userdetail' => 'mkmkmkm',
        'name'          => 2,
        'imgEvidence'       => $file
    ]);


    $responseCreateEvidence->assertSessionHasErrors(['id_userdetail', 'name', 'imgEvidence']);
});
