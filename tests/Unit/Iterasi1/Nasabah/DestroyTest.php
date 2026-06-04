<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
// Sesuaikan dengan Service Nasabah Anda
use App\Services\BankSampah\NasabahServices;

beforeEach(function () {
    $this->gender        = Gender::factory()->create();
    $this->rt            = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);
    $this->roleWarga     = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);
    $this->roleBankSampah = Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);

    $this->nasabah = User::factory()->create();
    $this->nasabahDetail = UserDetail::factory()->create([
        'fullName'           => 'Bank Sampah XYZ',
        'userName'           => 'banksampah03',
        'telephone_number'   => '08333333333',
        'id_user'            => $this->nasabah->id,
        'id_roles'           => $this->roleBankSampah->id, // ← id = 2
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai', // ← tambahkan ini jika nullable tidak diset
    ]);


    $this->actingAs($this->nasabah);
});

// =========================================================================
// UT.NSBH.DESTROY.001: Sukses Hapus
// =========================================================================
test('UT.NSBH.DESTROY.001 - Menghapus Data Nasabah', function () {
    $response = $this->delete(route('delete-nasabah', $this->nasabah->id));

    $response->assertRedirect();
    $response->assertSessionHas('message', 'Data berhasil dihapus');


    $this->assertDatabaseMissing('users', ['id' => $this->nasabah->id]);
    $this->assertDatabaseMissing('user_details', ['id_user' => $this->nasabah->id]);
});


test('UT.NSBH.DESTROY.002 - Menghapus Data Nasabah yang tidak ada di database', function () {


    $this->mock(NasabahServices::class, function ($mock) {
        $mock->shouldReceive('deleteNasabah')
            ->once()
            ->andThrow(new \Exception("Database error"));
    });

    $response = $this->delete(route('delete-nasabah', $this->nasabah->id));

    $response->assertSessionHas('error', 'Gagal menghapus: Database error');
});
