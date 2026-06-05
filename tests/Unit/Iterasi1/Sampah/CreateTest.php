<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\BankSampah\Sampah;
use App\Services\BankSampah\SampahServices;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->gender = Gender::factory()->create(['id' => 1]);
    $this->rt     = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);

    $this->roleAdmin = Roles::factory()->create(['id' => 1, 'role' => 'Ketua RW']);
    $this->roleWarga = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);
    $this->roleBankSampah = Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);

    $this->adminUser = User::factory()->create();


    $this->currentUser = User::factory()->create();
    $this->currentUserDetail = UserDetail::factory()->create([
        'fullName'         => 'Bank Sampah XYZ',
        'userName'         => 'banksampah03',
        'telephone_number' => '08333333333',
        'id_user'   => $this->currentUser->id,
        'id_roles'  => $this->roleBankSampah->id,
        'id_rt'     => $this->rt->id,
        'id_gender' => $this->gender->id,
        'status'    => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
    ]);

    $this->actingAs($this->currentUser);

    $this->sampahService = new SampahServices(new Sampah());
});

// =========================================================================
// UT.SMPH.CREATE.001: Create Sampah Baru (Belum Ada)
// =========================================================================
test('UT.SMPH.CREATE.001 - Menambahkan Sampah yang belum ada di database', function () {
    $payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Kardus Bekas',
        'kategori'      => 'Daur Ulang',
        'harga'         => 1500,
        'satuan'        => 'kg',
        'saldo'         => 20 // Saldo awal murni 20 karena data baru
    ];

    $result = $this->sampahService->createSampah($payload);

    expect($result->exists)->toBeTrue();
    $this->assertDatabaseHas('sampah', [
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Kardus Bekas',
        'saldo'         => 20
    ]);
});

// =========================================================================
// UT.SMPH.CREATE.002: Create Sampah Eksis (Akumulasi Saldo)
// =========================================================================
test('UT.SMPH.CREATE.002 - Menambahkan Sampah dengan nama sampah sudah ada di database', function () {
    \App\Models\BankSampah\Sampah::factory()->create([
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Plastik HD',
        'kategori'      => 'Daur Ulang',
        'harga'         => 2000,
        'satuan'        => 'kg',
        'saldo'         => 10
    ]);

    $payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Plastik HD',
        'harga'         => 2500, // Ceritanya harga naik
        'satuan'        => 'kg',
        'kategori'      => 'Daur Ulang',
        'saldo'         => 15
    ];

    $result = $this->sampahService->createSampah($payload);

    $this->assertDatabaseHas('sampah', [
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Plastik HD',
        'saldo'         => 25
    ]);
});
