<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\BankSampah\Sampah;
use App\Services\BankSampah\SampahServices;

beforeEach(function () {
    // Setup data dasar
    $this->gender = Gender::factory()->create(['id' => 1]);
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
});

test('UT.SMPH.STORE.001 - Menyimpan Data Sampah dengan inputan valid', function () {
    $this->actingAs($this->currentUser);

    $payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Plastik',
        'harga'         => 3000,
        'kategori'      => 'Daur Ulang',
        'satuan'        => 'kg',
        'saldo'         => 5
    ];

    $response = $this->post(route('add-sampah'), $payload);

    // Memastikan user diarahkan kembali dan session sukses ada
    $response->assertRedirect();
    $response->assertSessionHas('message', 'Sampah berhasil ditambahkan');

    // Memastikan data masuk ke database
    $this->assertDatabaseHas('sampah', ['nama_sampah' => 'Plastik']);
});

test('UT.SMPH.STORE.002 - Menyimpan Data Sampah dengan inputan error', function () {
    $this->actingAs($this->currentUser);

    // Mocking service yang digunakan oleh controller
    // Pastikan service ini di-resolve oleh Laravel Container
    $this->mock(SampahServices::class, function ($mock) {
        $mock->shouldReceive('createSampah')
            ->once()
            ->andThrow(new \Exception("Database error"));
    });

    $payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Gagal',
        'harga'         => 0,
        'kategori'      => 'Error',
        'satuan'        => 'kg',
        'saldo'         => 0
    ];

    $response = $this->post(route('add-sampah'), $payload);

    // Memastikan session error tertangkap
    $response->assertSessionHas('error', 'Gagal mendaftar: Database error');
});
