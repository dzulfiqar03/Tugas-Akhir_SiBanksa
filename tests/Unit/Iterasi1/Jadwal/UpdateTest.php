<?php

use App\Models\User;
use App\Models\UserChat;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Services\BankSampah\JadwalServices;
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

    $this->jadwalServices = new JadwalServices(new JadwalPelaksanaan(), new ChatServices(new UserChat()));

     $payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'tanggal_setoran'   => '2026-12-01',
    ];
    $this->jadwal = $this->jadwalServices->createJadwal($payload);

});

// =========================================================================
// UT.JDWL.UPDATE.001: Sukses Update
// =========================================================================
test('UT.JDWL.UPDATE.001 - Memperbarui Data Jadwal dengan inputan valid', function () {


    $payloadUpdate = [
        'id_userdetail' => $this->currentUserDetail->id,
        'tanggal_setoran'   => '2027-12-01',
    ];


    // Menggunakan patch atau put tergantung route kamu
    $response = $this->put(route('update-jadwalBankSampah', $this->jadwal->id), $payloadUpdate);

    $response->assertRedirect();
    $response->assertSessionHas('message'); // Asumsi flash message sukses
    $this->assertDatabaseHas('jadwal_pelaksanaan', ['id' => $this->jadwal->id, 'tanggal_setoran' => '2027-12-01']);
});

// =========================================================================
// UT.JDWL.UPDATE.002: Error Validasi
// =========================================================================
test('UT.JDWL.UPDATE.002 - Memperbarui Data Jadwal dengan inputan error', function () {

    // Mengirim payload kosong atau tidak valid untuk memicu validator (JadwalRequest)
    $invalidPayload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'tanggal_setoran'   => '2026-12-01',
    ];


    $response = $this->put(route('update-jadwalBankSampah', $this->jadwal->id), $invalidPayload);

    // Assert bahwa sistem kembali ke form (back) dan membawa error validasi
    $response->assertSessionHasErrors(['tanggal_setoran']);
});
