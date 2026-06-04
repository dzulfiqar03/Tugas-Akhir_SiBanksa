<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Services\BankSampah\NasabahServices;
use App\Notifications\Admin\UserVerification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

beforeEach(function () {
    $this->gender = Gender::factory()->create(['id' => 1]);
    $this->rt     = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);

    $this->roleAdmin = Roles::factory()->create(['id' => 1, 'role' => 'Ketua RW']);
    $this->roleNasabah = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);

    $this->adminUser = User::factory()->create();

    UserDetail::factory()->create([
        'fullName'         => 'Bank Sampah XYZ',
        'userName'         => 'banksampah03',
        'telephone_number' => '08333333333',
        'id_user'            => $this->adminUser->id,
        'id_roles'           => $this->roleAdmin->id,
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $this->nasabahUser = User::factory()->create(['email' => 'nasabah_lama@gmail.com']);
    $this->nasabahDetail = UserDetail::factory()->create([
        'id_user' => $this->nasabahUser->id,
        'id_roles' => $this->roleNasabah->id,
        'id_rt' => $this->rt->id,
        'id_gender' => $this->gender->id,
        'status' => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
        'fullName' => 'Nasabah Lama',
        'userName'         => 'nasabah_lama',
        'telephone_number' => '08333333333',
    ]);

    $this->actingAs($this->adminUser);

    Notification::fake();
});

// =========================================================================
// CREATE NASABAH
// =========================================================================
test('UT.NSBH.CREATE.001 - Menambahkan Nasabah dengan nama lengkap hanya 1 kata', function () {
    $payload = [
        'fullName'  => 'Budi',
        'id_rt'     => 1,
        'id_roles'  => $this->roleNasabah->id,
        'id_gender' => $this->gender->id,
        'phoneNumber' => '0899999999',
        'status'    => 'Disetujui',
        'status_transaction' => 'Belum Disetujui'
    ];

    $service = new NasabahServices(new User(), new UserDetail());
    $result = $service->createNasabah($payload);

    expect($result)->toBeInstanceOf(User::class);
    $this->assertDatabaseHas('user_details', ['id_user' => $result->id, 'userName' => 'budi_rt01']);
});

test('UT.NSBH.CREATE.002 - Menambahkan Nasabah dengan nama lengkap lebih dari 1 kata', function () {
    $payload = [
        'fullName'  => 'Ahmad Fauzi',
        'id_rt'     => 1,
        'id_roles'  => $this->roleNasabah->id,
        'id_gender' => $this->gender->id,
        'phoneNumber' => '0899999998',
        'status'    => 'Disetujui'
    ];

    $service = new NasabahServices(new User(), new UserDetail());
    $result = $service->createNasabah($payload);

    $this->assertDatabaseHas('user_details', ['id_user' => $result->id, 'userName' => 'fauzi_rt01']);
});
