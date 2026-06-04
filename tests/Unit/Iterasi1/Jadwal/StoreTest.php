<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Services\BankSampah\JadwalServices;

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

test('UT.JDWL.STORE.001 - Menyimpan Data jadwal dengan inputan valid', function () {
    $this->actingAs($this->currentUser);

    $payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'tanggal_setoran'   => '2026-12-01',
    ];

    $response = $this->post(route('add-jadwalBankSampah'), $payload);

    // Memastikan user diarahkan kembali dan session sukses ada
    $response->assertRedirect();
    $response->assertSessionHas('message', 'Jadwal berhasil ditambahkan');

    // Memastikan data masuk ke database
    $this->assertDatabaseHas('jadwal_pelaksanaan', ['tanggal_setoran' => '2026-12-01']);
});

test('UT.JDWL.STORE.002 - Menyimpan Data jadwal dengan inputan error', function () {
    $this->actingAs($this->currentUser);

    // Mocking service yang digunakan oleh controller
    // Pastikan service ini di-resolve oleh Laravel Container
    $this->mock(JadwalServices::class, function ($mock) {
        $mock->shouldReceive('createjadwal')
            ->once()
            ->andThrow(new \Exception("Database error"));
    });

    $payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'tanggal_setoran'   => '2026-12-01',
    ];

    $response = $this->post(route('add-jadwalBankSampah'), $payload);

    // Memastikan session error tertangkap
    $response->assertSessionHas('error', 'Gagal mendaftar: Database error');
});
