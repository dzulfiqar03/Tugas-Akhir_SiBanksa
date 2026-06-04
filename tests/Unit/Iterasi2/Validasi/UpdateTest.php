<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;

beforeEach(function () {
    // Setup environment data
    $this->gender = Gender::factory()->create();
    $this->rt     = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);
    $this->roleKetuaRW = Roles::factory()->create(['id' => 1, 'role' => 'Ketua RW']);
    $this->roleWarga = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);
    $this->roleBankSampah = Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);

    // Setup User Nasabah yang akan diupdate
    $this->ketuaRW = User::factory()->create();

    UserDetail::factory()->create([
        'fullName'         => 'Ketua RW',
        'userName'         => 'ketuarw01',
        'telephone_number' => '08123456789',
        'id_user'            => $this->ketuaRW->id,
        'id_roles'           => $this->roleKetuaRW->id,
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $this->actingAs($this->ketuaRW);

    $this->bankSampah = User::factory()->create();
    $this->bankSampahDetail = UserDetail::factory()->create([
         'fullName'         => 'Bank Sampah XYZ',
        'userName'         => 'banksampah03',
        'telephone_number' => '08333333333',
        'id_user'            => $this->bankSampah->id,
        'id_roles'           => $this->roleBankSampah->id,
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);


});

test('UT.BASAM.UPDATE.001 - Memperbarui Data Status Bank Sampah dengan inputan valid', function () {
    $payloadUpdate = [
        'fullName'    => 'Bank Sampah XYZ',
        'phoneNumber' => '081234567890',
        'address'     => 'Jl. Baru No. 1',
        'id_rt'       => $this->rt->id,
        'id_roles'    => $this->roleWarga->id,
        'id_gender'   => $this->gender->id,
        'status'      => 'Disetujui',
    ];

    $response = $this->put(route('rw.update-banksampah', $this->bankSampah->id), $payloadUpdate);

    // Tambahkan ini untuk melihat apakah data berubah di memori
    $this->bankSampahDetail->refresh();

    // Cek apakah model sudah berubah
    expect($this->bankSampahDetail->fullName)->toBe('Bank Sampah XYZ');

    $response->assertRedirect();
    // ... sisanya
});

test('UT.BASAM.UPDATE.002 - Memperbarui Data Bank Sampah dengan inputan error', function () {
    $invalidPayload = [
        'fullName'    => '',           // Error: wajib diisi
        'phoneNumber' => '',        // Error: minimal 10 karakter
        // Field wajib lainnya tidak dikirim untuk memicu error
    ];

   $response = $this->withHeaders(['Accept' => 'application/json'])
                     ->put(route('rw.update-banksampah', $this->bankSampah->id), $invalidPayload);

    // Sekarang gunakan assertJsonValidationErrors
    $response->assertStatus(422); // 422 Unprocessable Entity
    $response->assertJsonValidationErrors([
        'fullName',
        'phoneNumber',
        'id_rt',
        'id_roles',
        'id_gender',
        'status'
    ]);
});
