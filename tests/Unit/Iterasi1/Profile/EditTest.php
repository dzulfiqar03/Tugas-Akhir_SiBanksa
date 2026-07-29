<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;

beforeEach(function () {

    $this->gender = Gender::factory()->create();

    $this->rt = RTPerumahan::factory()->create([
        'id' => 1,
        'RT' => '1'
    ]);

    $this->roleBank = Roles::factory()->create([
        'id' => 2,
        'role' => 'Bank Sampah'
    ]);

    $this->roleNasabah = Roles::factory()->create([
        'id' => 3,
        'role' => 'Warga'
    ]);

    $this->user = User::factory()->create();

    $this->userDetail = UserDetail::factory()->create([
        'id_user' => $this->user->id,
        'id_roles' => $this->roleBank->id,
        'id_rt' => $this->rt->id,
        'id_gender' => $this->gender->id,
        'fullName' => 'Admin Bank',
        'userName' => 'adminbank',
        'telephone_number' => '08123456789',
        'address' => 'Jl. Mawar',
        'status' => 'Disetujui',
        'pencairan_via' => 'Tunai',
        'status_transaction' => 'Disetujui',
    ]);

    $this->actingAs($this->user);

});

test('UT.PROF.EDIT.001 - Menampilkan halaman edit profil Bank Sampah', function () {

    $response = $this->get(route('profile.edit'));

    $response->assertStatus(200);

    $response->assertInertia(fn ($page) =>
        $page
            ->component('Profile/Edit')
            ->where('pageName', 'BankSampahEditPage')
    );

});

test('UT.PROF.EDIT.002 - Menampilkan halaman edit profil Nasabah', function () {

    $this->userDetail->update([
        'id_roles' => $this->roleNasabah->id
    ]);

    $response = $this->get(route('profile.edit'));

    $response->assertStatus(200);

    $response->assertInertia(fn ($page) =>
        $page
            ->component('Profile/Edit')
            ->where('pageName', 'NasabahEditPage')
    );

});

test('UT.PROF.EDIT.003 - Menghitung profile completion ketika seluruh field terisi', function () {

    $response = $this->get(route('profile.edit'));

    $response->assertStatus(200);

    expect($this->user->fresh()->user_detail)->not->toBeNull();

});

test('UT.PROF.EDIT.004 - Menghitung profile completion ketika terdapat field kosong', function () {

    $this->userDetail->update([
        'address' => null,
    ]);

    $response = $this->get(route('profile.edit'));

    $response->assertStatus(200);

    expect($this->user->fresh()->user_detail->address)->toBeNull();

});

test('UT.PROF.EDIT.005 - Menampilkan halaman edit beserta data profil', function () {

    $response = $this->get(route('profile.edit'));

    $response->assertStatus(200);

    $response->assertInertia(fn ($page) =>
        $page
            ->component('Profile/Edit')
            ->has('nasabah')
            ->has('nasabahAll')
            ->has('initialNotifications')
            ->has('sidebardata')
            ->has('formdata')
    );

});
