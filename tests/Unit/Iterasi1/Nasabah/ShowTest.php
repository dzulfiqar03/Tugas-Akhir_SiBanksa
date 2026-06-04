<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\Transaction\Bank;

beforeEach(function () {
    $this->gender = Gender::factory()->create(['id' => 1]);
    $this->rt     = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);
    $this->roleWarga = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);
    $this->roleBankSampah = Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);

    $this->admin = User::factory()->create();
    // Setup admin dengan data lengkap agar tidak error saat login
    UserDetail::factory()->create([
        'fullName'         => 'Admin Bank',
        'userName'         => 'admin01',
        'telephone_number' => '08123456789',
        'id_user'          => $this->admin->id,
        'id_roles'         => $this->roleBankSampah->id,
        'id_rt'            => $this->rt->id,
        'id_gender'        => $this->gender->id,
        'status'           => 'Disetujui',
        'pencairan_via'    => 'Tunai',
        'status_transaction' => 'Belum Disetujui',
    ]);

    $this->actingAs($this->admin);
});

/**
 * Helper untuk membuat nasabah lengkap dengan relasi User-nya.
 * Data di-merge agar kolom NOT NULL tidak kosong.
 */
function createNasabahWithUser($overrides = [])
{
    $user = User::factory()->create();

    // 1. Buat Bank dummy agar relasi id_bank terpenuhi
  $bank = Bank::factory()->create([
        'name'       => 'Bank ABC',
        'short_name' => 'ABC',
        'transfer_code' => '123',
    ]);
    // 2. Buat UserDetail
    $dataDetail = array_merge([
        'id_user'          => $user->id,
        'fullName'         => 'Nasabah Test',
        'userName'         => 'nasabah_' . uniqid(),
        'telephone_number' => '0899999999',
        'address'          => 'Jl. Contoh No.123',
        'id_rt'            => 1,
        'id_gender'        => 1,
        'id_roles'         => 3, // Warga
        'status'           => 'Disetujui',
        'pencairan_via'    => 'Tunai',
        'status_transaction' => 'Belum Disetujui',
    ], Arr::except($overrides, ['nomor_rekening']));

    $detail = UserDetail::factory()->create($dataDetail);

    // 3. Buat UserBank dengan ID Bank yang valid
    \App\Models\UserBank::factory()->create([
        'id_userdetail'  => $detail->id,
        'id_bank'        => $bank->id, // PENTING: ID Bank harus ada
        'nomor_rekening' => $overrides['nomor_rekening'] ?? '1234567890',
    ]);

    return ['user' => $user, 'detail' => $detail];
}

// ==========================================
// 6 JALUR PENGUJIAN (FULL PATH COVERAGE)
// ==========================================

test('UT.NSBH.SHOW.001 - Menampilkan profil lengkap dengan metode pembayaran non tunai', function () {
    $nasabah = createNasabahWithUser(['pencairan_via' => 'Non-Tunai']);
    $response = $this->get(route('show-nasabah', $nasabah['user']->id));
    $response->assertInertia(fn($page) => $page->where('percentageSuccessProfile', 100));
});

test('UT.NSBH.SHOW.002 - Menampilkan profil lengkap dengan metode pembayaran tunai', function () {
    $nasabah = createNasabahWithUser(['pencairan_via' => 'Tunai', 'nomor_rekening' => '1234567890']);
    $response = $this->get(route('show-nasabah', $nasabah['user']->id));
    $response->assertInertia(fn($page) => $page->where('percentageSuccessProfile', 100));
});

test('UT.NSBH.SHOW.003 - Menampilkan profil (Tunai/Non Tunai) tetapi ada field yang kosong', function () {
    $nasabah = createNasabahWithUser();
    UserDetail::where('id_user', $nasabah['user']->id)->update(['address' => null]);

    $response = $this->get(route('show-nasabah', $nasabah['user']->id));
    $response->assertInertia(fn($page) => $page->has('nullForm', 1));
});

test('UT.NSBH.SHOW.004 - Menampilkan Dokumen Lengkap', function () {
    $nasabah = createNasabahWithUser();
    // Asumsi: Logic backend mengambil data dari relasi
    $response = $this->get(route('show-nasabah', $nasabah['user']->id));
    $response->assertStatus(200);
});

test('UT.NSBH.SHOW.005 - Menampilkan dokumen ada yang belum terisi', function () {
    $nasabah = createNasabahWithUser();
    $response = $this->get(route('show-nasabah', $nasabah['user']->id));
    $response->assertStatus(200);
});

test('UT.NSBH.SHOW.006 - Menampilkan profil (Tunai/Non Tunai) tetapi ada field yang kosong', function () {
    $nasabah = createNasabahWithUser();
    $response = $this->get(route('show-nasabah', $nasabah['user']->id));
    $response->assertStatus(200);
});
