<?php

use App\Models\User;
use App\Models\UserChat;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Services\BankSampah\KepengurusanServices;
use App\Services\ChatServices;

beforeEach(function () {
    // Setup environment data
    $this->gender = Gender::factory()->create();
    $this->rt     = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);
    $this->roleWarga = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);
    $this->roleBankSampah = Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);

    // Setup user yang login
    $this->currentUser = User::factory()->create();
    $this->currentUserDetail = UserDetail::factory()->create([
        'fullName'         => 'Bank Sampah XYZ',
        'userName'         => 'banksampah03',
        'telephone_number' => '08333333333',
        'id_user' => $this->currentUser->id,
        'id_roles' => $this->roleBankSampah->id,
        'id_rt' => $this->rt->id,
        'id_gender' => $this->gender->id,
        'status' => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',

    ]);

    $this->actingAs($this->currentUser);

    $this->kepengurusanServices = new KepengurusanServices(new \App\Models\BankSampah\Kepengurusan());

    $this->payload = [
        'fullName' => 'Muhammad Irfan',
        'userName' => 'irfan123',
        'address' => 'Jl. Merdeka No. 123',
        'phoneNumber' => '081234567890',
        'id_gender' => $this->gender->id,
        'id_userdetail' => $this->currentUserDetail->id,
        'divisi' => 'Ketua',
    ];
    $this->kepengurusan = $this->kepengurusanServices->createKepengurusan($this->payload);
});

// =========================================================================
// UT.PNGRS.UPDATE.001: Sukses Update
// =========================================================================
test('UT.PNGRS.UPDATE.001 - Memperbarui Data Kepengurusan dengan inputan valid', function () {


    $payloadUpdate = [
        'id_userdetail' => $this->currentUserDetail->id,
        'fullName' => 'Muhammad Irfan Updated',
        'userName' => 'irfan1234',
        'address' => 'Jl. Merdeka No. 123 Updated',
        'phoneNumber' => '081234567890',
        'id_gender' => $this->gender->id,
        'divisi' => 'Sekretaris',
    ];

    $response = $this->put(route('update-kepengurusan', $this->kepengurusan->id), $payloadUpdate);

    $response->assertRedirect();
    $response->assertSessionHas('message'); // Asumsi flash message sukses
    $this->assertDatabaseHas('kepengurusans', ['id' => $this->kepengurusan->id, 'fullName' => 'Muhammad Irfan Updated']);
});

// =========================================================================
// UT.PNGRS.UPDATE.002: Error Validasi
// =========================================================================
test('UT.PNGRS.UPDATE.002 - Memperbarui Data Kepengurusan dengan inputan error', function () {

    // Mengirim payload kosong atau tidak valid untuk memicu validator (KepengurusanRequest)
    $invalidPayload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'fullName' => '',
        'userName' => '',
        'address' => '',
        'phoneNumber' => '',
        'id_gender' => '',
        'divisi' => '',
    ];


    $response = $this->put(route('update-kepengurusan', $this->kepengurusan->id), $invalidPayload);

    // Assert bahwa sistem kembali ke form (back) dan membawa error validasi
    $response->assertSessionHasErrors(['fullName', 'userName', 'address', 'phoneNumber', 'id_gender', 'divisi']);
});
