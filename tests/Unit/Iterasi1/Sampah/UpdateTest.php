<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\BankSampah\Sampah;
use App\Services\BankSampah\SampahServices;

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

    $this->sampahService = new SampahServices(new Sampah());

            $payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Kardus Bekas',
        'kategori'      => 'Daur Ulang',
        'harga'         => 1500,
        'satuan'        => 'kg',
        'saldo'         => 20 // Saldo awal murni 20 karena data baru
    ];

    $this->sampah = $this->sampahService->createSampah($payload);
});

// =========================================================================
// UT.SMPH.UPDATE.001: Sukses Update
// =========================================================================
test('UT.SMPH.UPDATE.001 - Memperbarui Data Sampah dengan inputan valid', function () {


    $payloadUpdate = [
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Tembaga Baru',
        'harga'         => 60000,
        'kategori'      => 'Daur Ulang',
        'satuan'        => 'kg',
        'saldo'         => 10
    ];

    // Menggunakan patch atau put tergantung route kamu
    $response = $this->put(route('update-sampah', $this->sampah->id), $payloadUpdate);

    $response->assertRedirect();
    $response->assertSessionHas('message'); // Asumsi flash message sukses
    $this->assertDatabaseHas('sampah', ['id' => $this->sampah->id, 'nama_sampah' => 'Tembaga Baru']);
});

// =========================================================================
// UT.SMPH.UPDATE.002: Error Validasi
// =========================================================================
test('UT.SMPH.UPDATE.002 - Memperbarui Data Sampah dengan inputan error', function () {

    // Mengirim payload kosong atau tidak valid untuk memicu validator (SampahRequest)
    $invalidPayload = [
        'nama_sampah' => '', // Asumsi field ini required
        'harga'       => 'bukan-angka'
    ];

    $response = $this->put(route('update-sampah', $this->sampah->id), $invalidPayload);

    // Assert bahwa sistem kembali ke form (back) dan membawa error validasi
    $response->assertSessionHasErrors(['nama_sampah', 'harga']);
});
