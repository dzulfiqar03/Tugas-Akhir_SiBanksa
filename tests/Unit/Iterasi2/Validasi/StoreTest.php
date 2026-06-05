<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;

beforeEach(function () {
    $this->gender = Gender::factory()->create(['id' => 1]);
    $this->rt     = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);

    $this->roleKetuaRW = Roles::factory()->create(['id' => 1, 'role' => 'Ketua RW']);
    $this->roleWarga = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);
    $this->roleBankSampah = Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);

    // Buat User
    $this->admin = User::factory()->create();

    // PENTING: Pastikan UserDetail ada agar auth()->user()->user_detail tidak null
    UserDetail::factory()->create([
        'fullName'         => 'Ketua RW',
        'userName'         => 'ketuarw01',
        'telephone_number' => '08222222222',
        'id_user'            => $this->admin->id,
        'id_roles'           => $this->roleKetuaRW->id,
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $this->actingAs($this->admin);
});

// =========================================================================
// UT.VALBS.STORE.001: Sukses Simpan Bank Sampah
// =========================================================================
test('UT.VALBS.STORE.001 - Menyimpan Akun Bank Sampah dengan inputan valid', function () {
    $payload = [
        'fullName'         => 'Bank Sampah XYZ',
        'userName'         => 'banksampah03',
        'phoneNumber' => '081234567890',
        'id_roles'         => $this->roleBankSampah->id,
        'id_rt'            => $this->rt->id,
        'id_gender'        => $this->gender->id,
        'status'           => 'Disetujui',
    ];

    $response = $this->post(route('rw.add-banksampah'), $payload);

    $response->assertRedirect();
    $response->assertSessionHas('message', 'Bank Sampah berhasil ditambahkan');
    $this->assertDatabaseHas('user_details', ['fullName' => 'Petugas Bank Sampah XYZ']);
});

// =========================================================================
// UT.VALBS.STORE.002: Error Validasi
// =========================================================================
test('UT.VALBS.STORE.002 - Menyimpan Akun Bank Sampah dengan inputan error', function () {
    // Kirim payload kosong atau tidak lengkap
    $invalidPayload = [];

    $response = $this->post(route('rw.add-banksampah'), $invalidPayload);

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
