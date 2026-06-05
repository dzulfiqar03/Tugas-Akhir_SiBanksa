<?php

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\BankSampah\Sampah;
use App\Models\UserLog;
use App\Services\BankSampah\SampahServices;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->gender    = Gender::factory()->create(['id' => 1]);
    $this->rt        = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);
    $this->roleWarga = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);
    $this->roleBankSampah = Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);
});


// UT.BS.DBORD.001: Role Bank Sampah
test('UT.BS.DBORD.001 - Mengambil jumlah saldo dan jumlah sampah berdasarkan role Bank Sampah', function () {

    $currentBankSampah = User::factory()->create();
    $currentBankSampahDetail = UserDetail::factory()->create([
        'fullName'           => 'Bank Sampah XYZ',
        'userName'           => 'banksampah03',
        'telephone_number'   => '08333333333',
        'id_user'            => $currentBankSampah->id,
        'id_roles'           => $this->roleBankSampah->id, // ← Bank Sampah
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $currentBankSampah = $currentBankSampah->fresh([
        'user_detail',
        'user_detail.rt', // ← agar rt->id tidak null di controller
    ]);

    $this->actingAs($currentBankSampah);

    $this->get(route('dashboard'))
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('BankSampah/Dashboard')
                ->has('saldo')
                ->has('jmlSampah')
        );
});

// UT.BS.DBORD.002: Role Selain Bank Sampah
test('UT.BS.DBORD.002 - Mengambil jumlah saldo dan jumlah sampah berdasarkan role Bank Sampah', function () {

    $currentNasabah = User::factory()->create();
    $currentNasabahDetail = UserDetail::factory()->create([
        'fullName'           => 'Muhammad Irfan',
        'userName'           => 'irfan123',
        'telephone_number'   => '08333333333',
        'id_user'            => $currentNasabah->id,
        'id_roles'           => $this->roleWarga->id, // ← Warga
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai', // ← tambahkan ini
    ]);

    // ✅ Fresh dengan relasi rt
    $currentNasabah = $currentNasabah->fresh([
        'user_detail',
        'user_detail.rt', // ← agar rt->id tidak null di controller
    ]);

    $this->actingAs($currentNasabah);
    $this->get(route('dashboard'))
        ->assertInertia(
            fn(Assert $page) => $page
                ->where('saldo', 0)
                ->where('jmlSampah', 0)
        );
});

// UT.BS.DBORD.003: Log Activity Mapping
test('UT.BS.DBORD.003 - Memproses Log Activity "LOGIN"', function () {
    $currentBankSampah = User::factory()->create();
    $currentBankSampahDetail = UserDetail::factory()->create([
        'fullName'           => 'Bank Sampah XYZ',
        'userName'           => 'banksampah03',
        'telephone_number'   => '08333333333',
        'id_user'            => $currentBankSampah->id,
        'id_roles'           => $this->roleBankSampah->id, // ← Bank Sampah
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $currentBankSampah = $currentBankSampah->fresh([
        'user_detail',
        'user_detail.rt', // ← agar rt->id tidak null di controller
    ]);


    UserLog::factory()->create(['id_userdetail' => $currentBankSampahDetail->id, 'action' => 'LOGIN', 'ip_address' => '127.0.0.1', 'device_agent' => 'Mozilla/5.0', 'device' => 'Desktop', 'platform' => 'Windows', 'type_platform' => 'Desktop', 'time_logs' => now()]);

    $this->actingAs($currentBankSampah);
    $this->get(route('dashboard'))
        ->assertInertia(
            fn(Assert $page) => $page
                ->where('lastActivity.0.description', 'Masuk ke sistem')
        );
});

test('UT.BS.DBORD.004 - Memproses Log Activity “SETORAN TERCATAT”', function () {
    $currentBankSampah = User::factory()->create();
    $currentBankSampahDetail = UserDetail::factory()->create([
        'fullName'           => 'Bank Sampah XYZ',
        'userName'           => 'banksampah03',
        'telephone_number'   => '08333333333',
        'id_user'            => $currentBankSampah->id,
        'id_roles'           => $this->roleBankSampah->id, // ← Bank Sampah
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $currentBankSampah = $currentBankSampah->fresh([
        'user_detail',
        'user_detail.rt', // ← agar rt->id tidak null di controller
    ]);


    UserLog::factory()->create(['id_userdetail' => $currentBankSampahDetail->id, 'action' => 'SETORAN TERCATAT', 'ip_address' => '127.0.0.1', 'device_agent' => 'Mozilla/5.0', 'device' => 'Desktop', 'platform' => 'Windows', 'type_platform' => 'Desktop', 'time_logs' => now()]);

    $this->actingAs($currentBankSampah);
    $this->get(route('dashboard'))
        ->assertInertia(
            fn(Assert $page) => $page
                ->where('lastActivity.0.description', 'Setoran berhasil dicatat')
        );
});

test('UT.BS.DBORD.005 - Memproses Log Activity “SETORAN MASUK”', function () {
    $currentBankSampah = User::factory()->create();
    $currentBankSampahDetail = UserDetail::factory()->create([
        'fullName'           => 'Bank Sampah XYZ',
        'userName'           => 'banksampah03',
        'telephone_number'   => '08333333333',
        'id_user'            => $currentBankSampah->id,
        'id_roles'           => $this->roleBankSampah->id, // ← Bank Sampah
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $currentBankSampah = $currentBankSampah->fresh([
        'user_detail',
        'user_detail.rt', // ← agar rt->id tidak null di controller
    ]);


    UserLog::factory()->create(['id_userdetail' => $currentBankSampahDetail->id, 'action' => 'SETORAN MASUK', 'ip_address' => '127.0.0.1', 'device_agent' => 'Mozilla/5.0', 'device' => 'Desktop', 'platform' => 'Windows', 'type_platform' => 'Desktop', 'time_logs' => now()]);

    $this->actingAs($currentBankSampah);
    $this->get(route('dashboard'))
        ->assertInertia(
            fn(Assert $page) => $page
                ->where('lastActivity.0.description', 'Setoran masuk')
        );
});

test('UT.BS.DBORD.006 - Memproses Log Activity “LOGOUT”', function () {
    $currentBankSampah = User::factory()->create();
    $currentBankSampahDetail = UserDetail::factory()->create([
        'fullName'           => 'Bank Sampah XYZ',
        'userName'           => 'banksampah03',
        'telephone_number'   => '08333333333',
        'id_user'            => $currentBankSampah->id,
        'id_roles'           => $this->roleBankSampah->id, // ← Bank Sampah
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $currentBankSampah = $currentBankSampah->fresh([
        'user_detail',
        'user_detail.rt', // ← agar rt->id tidak null di controller
    ]);


    UserLog::factory()->create(['id_userdetail' => $currentBankSampahDetail->id, 'action' => 'LOGOUT', 'ip_address' => '127.0.0.1', 'device_agent' => 'Mozilla/5.0', 'device' => 'Desktop', 'platform' => 'Windows', 'type_platform' => 'Desktop', 'time_logs' => now()]);

    $this->actingAs($currentBankSampah);
    $this->get(route('dashboard'))
        ->assertInertia(
            fn(Assert $page) => $page
                ->where('lastActivity.0.description', 'Keluar dari sistem')
        );
});

// UT.BS.DBORD.007: Akses saat data kosong (Stabilitas)
test('UT.BS.DBORD.007 - Akses Dashboard jika kondisi tidak memenuhi', function () {

    $currentBankSampah = User::factory()->create();
    $currentBankSampahDetail = UserDetail::factory()->create([
        'fullName'           => 'Bank Sampah XYZ',
        'userName'           => 'banksampah03',
        'telephone_number'   => '08333333333',
        'id_user'            => $currentBankSampah->id,
        'id_roles'           => $this->roleBankSampah->id, // ← Bank Sampah
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $currentBankSampah = $currentBankSampah->fresh([
        'user_detail',
        'user_detail.rt', // ← agar rt->id tidak null di controller
    ]);
    $this->actingAs($currentBankSampah);

    $this->get(route('dashboard'))
        ->assertStatus(200)
        ->assertInertia(
            fn(Assert $page) => $page
                ->has('allBankSampah')
                ->has('nasabah')
        );
});
