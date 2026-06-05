<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;

beforeEach(function () {
    $this->gender = Gender::factory()->create(['id' => 1]);
    $this->rt     = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);
    $this->roleWarga = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);
    $this->roleBankSampah = Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);

    // Buat User
    $this->admin = User::factory()->create();

    // PENTING: Pastikan UserDetail ada agar auth()->user()->user_detail tidak null
    UserDetail::factory()->create([
        'fullName'         => 'Bank Sampah XYZ',
        'userName'         => 'banksampah03',
        'telephone_number' => '08333333333',
        'id_user'            => $this->admin->id,
        'id_roles'           => $this->roleBankSampah->id,
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $this->actingAs($this->admin);
});

// =========================================================================
// UT.NSBH.STORE.001: Sukses Simpan Nasabah
// =========================================================================
test('UT.NSBH.STORE.001 - Menyimpan Data Nasabah dengan inputan valid', function () {
    $payload = [
        'fullName'         => 'Budi Santoso',
        'userName'         => 'budisantoso',
        'phoneNumber' => '081234567890',
        'id_roles'         => $this->roleWarga->id,
        'id_rt'            => $this->rt->id,
        'id_gender'        => $this->gender->id,
        'status'           => 'Disetujui',
    ];

    $response = $this->post(route('add-nasabah'), $payload);

    $response->assertRedirect();
    $response->assertSessionHas('message', 'Nasabah berhasil ditambahkan');
    $this->assertDatabaseHas('user_details', ['fullName' => 'Budi Santoso']);
});

// =========================================================================
// UT.NSBH.STORE.002: Error Validasi
// =========================================================================
test('UT.NSBH.STORE.002 - Menyimpan Data Nasabah dengan inputan error', function () {
    // Kirim payload kosong atau tidak lengkap
    $invalidPayload = [];

    $response = $this->post(route('add-nasabah'), $invalidPayload);

    // Sesuaikan dengan daftar error yang benar-benar muncul di session
    $response->assertSessionHasErrors([
        'fullName',
        'phoneNumber',
        'id_rt',
        'id_roles',
        'id_gender',
        'status'
    ]);

    $this->assertDatabaseMissing('user_details', ['fullName' => '']);
});
