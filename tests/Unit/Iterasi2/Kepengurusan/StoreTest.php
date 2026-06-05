<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Services\BankSampah\JadwalServices;
use App\Services\BankSampah\KepengurusanServices;

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

    $this->actingAs($this->currentUser);


    $this->payload = [
        'fullName' => 'Muhammad Irfan',
        'userName' => 'irfan123',
        'address' => 'Jl. Merdeka No. 123',
        'phoneNumber' => '081234567890',
        'id_gender' => $this->gender->id,
        'id_userdetail' => $this->currentUserDetail->id,
        'divisi' => 'Ketua',
    ];
});

test('UT.PNGRS.STORE.001 - Menyimpan Data Kepengurusan dengan inputan valid', function () {

    $response = $this->post(route('add-kepengurusan'), $this->payload);

    // Memastikan user diarahkan kembali dan session sukses ada
    $response->assertRedirect();
    $response->assertSessionHas('message', 'Kepengurusan berhasil ditambahkan');

    // Memastikan data masuk ke database
    $this->assertDatabaseHas('kepengurusans', ['fullName' => 'Muhammad Irfan']);
});

test('UT.PNGRS.STORE.002 - Menyimpan Data Kepengurusan dengan inputan error', function () {

    $this->mock(KepengurusanServices::class, function ($mock) {
        $mock->shouldReceive('createKepengurusan')
            ->once()
            ->andThrow(new \Exception("Database error"));
    });

    $response = $this->post(route('add-kepengurusan'), $this->payload);

    $response->assertSessionHas('error', 'Gagal mendaftar: Database error');
});
