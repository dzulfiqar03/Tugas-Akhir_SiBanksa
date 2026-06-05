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
    $this->roleWarga = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);
    $this->roleBankSampah = Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);

    // Setup User Nasabah yang akan diupdate
    $this->nasabah = User::factory()->create();

    UserDetail::factory()->create([
        'fullName'         => 'Bank Sampah XYZ',
        'userName'         => 'banksampah03',
        'telephone_number' => '08333333333',
        'id_user'            => $this->nasabah->id,
        'id_roles'           => $this->roleBankSampah->id,
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);
    $this->nasabahDetail = UserDetail::factory()->create([
        'fullName'         => 'Admin Bank',
        'userName'         => 'admin01',
        'telephone_number' => '08123456789',
        'id_user'          => $this->nasabah->id,
        'id_roles'         => $this->roleWarga->id,
        'id_rt'            => $this->rt->id,
        'id_gender'        => $this->gender->id,
        'status'           => 'Disetujui',
        'pencairan_via'    => 'Tunai',
        'status_transaction' => 'Belum Disetujui',
    ]);

    // Login sebagai Admin/Bank Sampah (opsional, sesuaikan dengan logic aplikasi Anda)
    $this->actingAs($this->nasabah);
});

test('UT.NSBH.UPDATE.001 - Memperbarui Data Nasabah dengan inputan valid', function () {
    $payloadUpdate = [
        'fullName'    => 'Admin Bank',
        'phoneNumber' => '081234567890',
        'address'     => 'Jl. Baru No. 1',
        'id_rt'       => $this->rt->id,
        'id_roles'    => $this->roleWarga->id,
        'id_gender'   => $this->gender->id,
        'status'      => 'Disetujui',
    ];

    $response = $this->put(route('update-nasabah', $this->nasabahDetail->id), $payloadUpdate);

    // Tambahkan ini untuk melihat apakah data berubah di memori
    $this->nasabahDetail->refresh();

    // Cek apakah model sudah berubah
    expect($this->nasabahDetail->fullName)->toBe('Admin Bank');

    $response->assertRedirect();
    // ... sisanya
});

test('UT.NSBH.UPDATE.002 - Memperbarui Data Nasabah dengan inputan error', function () {
    $invalidPayload = [
        'fullName'    => '',           // Error: wajib diisi
        'phoneNumber' => '123',        // Error: minimal 10 karakter
        // Field wajib lainnya tidak dikirim untuk memicu error
    ];

    $response = $this->put(route('update-nasabah', $this->nasabahDetail->id), $invalidPayload);

    // Assert sesuai dengan daftar field yang wajib di rules()
    $response->assertSessionHasErrors([
        'fullName',
        'phoneNumber',
        'id_rt',
        'id_roles',
        'id_gender',
        'status'
    ]);
});
