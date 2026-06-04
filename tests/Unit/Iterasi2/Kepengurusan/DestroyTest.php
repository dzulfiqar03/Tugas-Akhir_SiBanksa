<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Services\BankSampah\JadwalServices;
use App\Services\BankSampah\KepengurusanServices;
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

        $this->kepengurusanServices = new KepengurusanServices(new \App\Models\BankSampah\Kepengurusan());

    $this->payload = [
        'fullName' => 'Muhammad Irfan',
        'userName' => 'irfan123',
        'address' => 'Jl. Merdeka No. 123',
        'phoneNumber' => '081234567890',
        'id_gender' => $this->gender->id,
        'id_userdetail' => $this->currentUserDetail->id,
        'divisi' => 'Ketua',
    ];
    $this->kepengurusan = $this->kepengurusanServices->createKepengurusan($this->payload);
});

// =========================================================================
// UT.SMPH.UPDATE.001: Sukses Update
// =========================================================================
test('UT.PNGRS.DESTROY.001 - Menghapus data kepengurusan yang ada di database', function () {


    $this->assertDatabaseHas('kepengurusans', ['id' => $this->kepengurusan->id]);

    $response = $this->delete(route('delete-kepengurusan', $this->kepengurusan->id));

    $response->assertRedirect();
    $response->assertSessionHas('message', 'Data berhasil dihapus');

    // Pastikan data hilang dari database
    $this->assertDatabaseMissing('kepengurusans', ['id' => $this->kepengurusan->id]);
});

// =========================================================================
// UT.PNGRS.DESTROY.002: Error Validasi
// =========================================================================
test('UT.PNGRS.DESTROY.002 - Menghapus data kepengurusan yang tidak ada di database', function () {


    $this->mock(KepengurusanServices::class, function ($mock) {
        $mock->shouldReceive('deleteKepengurusan')
            ->once()
            ->andThrow(new \Exception("Database error"));
    });

    $response = $this->delete(route('delete-kepengurusan', $this->kepengurusan->id));

    $response->assertSessionHas('error', 'Gagal menghapus: Database error');
});
