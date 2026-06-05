<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Services\BankSampah\JadwalServices;
use App\Services\ChatServices;
use App\Models\UserChat;
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
// UT.SMPH.UPDATE.001: Sukses Update
// =========================================================================
test('UT.JDWL.DESTROY.001 - Menghapus data jadwal pelaksanaan yang ada di database', function () {


    $this->assertDatabaseHas('jadwal_pelaksanaan', ['id' => $this->jadwal->id]);

    $response = $this->delete(route('delete-jadwalBankSampah', $this->jadwal->id));

    $response->assertRedirect();
    $response->assertSessionHas('message', 'Jadwal berhasil dihapus');

    // Pastikan data hilang dari database
    $this->assertDatabaseMissing('jadwal_pelaksanaan', ['id' => $this->jadwal->id]);
});

// =========================================================================
// UT.JDWL.DESTROY.002: Error Validasi
// =========================================================================
test('UT.JDWL.DESTROY.002 - Menghapus data jadwal pelaksanaan yang tidak ada di database', function () {


    $this->mock(JadwalServices::class, function ($mock) {
        $mock->shouldReceive('deleteJadwal')
            ->once()
            ->andThrow(new \Exception("Database error"));
    });

    $response = $this->delete(route('delete-jadwalBankSampah', $this->jadwal->id));

    $response->assertSessionHas('error', 'Gagal menghapus: Database error');
});
