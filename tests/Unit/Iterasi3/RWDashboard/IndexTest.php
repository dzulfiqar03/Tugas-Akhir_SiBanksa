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
    $this->roleKetuaRW = Roles::factory()->create(['id' => 1, 'role' => 'Ketua RW']);
    $this->roleWarga = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);
    $this->roleBankSampah = Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);
});




// UT.RW.DBORD.001: Log Activity Mapping
test('UT.RW.DBORD.001 - Memproses Log Activity "LOGIN"', function () {
    $currentKetuaRW = User::factory()->create();
    $currentKetuaRWDetail = UserDetail::factory()->create([
        'fullName'           => 'Ketua RW 01',
        'userName'           => 'ketuarw01',
        'telephone_number'   => '08333333333',
        'id_user'            => $currentKetuaRW->id,
        'id_roles'           => $this->roleKetuaRW->id, // ← Ketua RW
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $currentKetuaRW = $currentKetuaRW->fresh([
        'user_detail',
        'user_detail.rt', // ← agar rt->id tidak null di controller
    ]);


    UserLog::factory()->create(['id_userdetail' => $currentKetuaRWDetail->id, 'action' => 'LOGIN', 'ip_address' => '127.0.0.1', 'device_agent' => 'Mozilla/5.0', 'device' => 'Desktop', 'platform' => 'Windows', 'type_platform' => 'Desktop', 'time_logs' => now()]);

    $this->actingAs($currentKetuaRW);
    $this->get(route('rw.dashboard'))
        ->assertInertia(
            fn(Assert $page) => $page
                ->where('lastActivity.0.description', 'Masuk ke sistem')
        );
});

test('UT.RW.DBORD.002 - Memproses Log Activity “SETORAN TERCATAT”', function () {
    $currentKetuaRW = User::factory()->create();
    $currentKetuaRWDetail = UserDetail::factory()->create([
        'fullName'           => 'Ketua RW 01',
        'userName'           => 'ketuarw01',
        'telephone_number'   => '08333333333',
        'id_user'            => $currentKetuaRW->id,
        'id_roles'           => $this->roleKetuaRW->id, // ← Ketua RW
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $currentKetuaRW = $currentKetuaRW->fresh([
        'user_detail',
        'user_detail.rt', // ← agar rt->id tidak null di controller
    ]);


    UserLog::factory()->create(['id_userdetail' => $currentKetuaRWDetail->id, 'action' => 'SETORAN TERCATAT', 'ip_address' => '127.0.0.1', 'device_agent' => 'Mozilla/5.0', 'device' => 'Desktop', 'platform' => 'Windows', 'type_platform' => 'Desktop', 'time_logs' => now()]);

    $this->actingAs($currentKetuaRW);
    $this->get(route('rw.dashboard'))
        ->assertInertia(
            fn(Assert $page) => $page
                ->where('lastActivity.0.description', 'Setoran berhasil dicatat')
        );
});

test('UT.RW.DBORD.003 - Memproses Log Activity “SETORAN MASUK”', function () {
    $currentKetuaRW = User::factory()->create();
    $currentKetuaRWDetail = UserDetail::factory()->create([
        'fullName'           => 'Ketua RW 01',
        'userName'           => 'ketuarw01',
        'telephone_number'   => '08333333333',
        'id_user'            => $currentKetuaRW->id,
        'id_roles'           => $this->roleKetuaRW->id, // ← Ketua RW
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $currentKetuaRW = $currentKetuaRW->fresh([
        'user_detail',
        'user_detail.rt', // ← agar rt->id tidak null di controller
    ]);


    UserLog::factory()->create(['id_userdetail' => $currentKetuaRWDetail->id, 'action' => 'SETORAN MASUK', 'ip_address' => '127.0.0.1', 'device_agent' => 'Mozilla/5.0', 'device' => 'Desktop', 'platform' => 'Windows', 'type_platform' => 'Desktop', 'time_logs' => now()]);

    $this->actingAs($currentKetuaRW);
    $this->get(route('rw.dashboard'))
        ->assertInertia(
            fn(Assert $page) => $page
                ->where('lastActivity.0.description', 'Setoran masuk')
        );
});

test('UT.RW.DBORD.004 - Memproses Log Activity “LOGOUT”', function () {
    $currentKetuaRW = User::factory()->create();
    $currentKetuaRWDetail = UserDetail::factory()->create([
        'fullName'           => 'Ketua RW 01',
        'userName'           => 'ketuarw01',
        'telephone_number'   => '08333333333',
        'id_user'            => $currentKetuaRW->id,
        'id_roles'           => $this->roleKetuaRW->id, // ← Ketua RW
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $currentKetuaRW = $currentKetuaRW->fresh([
        'user_detail',
        'user_detail.rt', // ← agar rt->id tidak null di controller
    ]);


    UserLog::factory()->create(['id_userdetail' => $currentKetuaRWDetail->id, 'action' => 'LOGOUT', 'ip_address' => '127.0.0.1', 'device_agent' => 'Mozilla/5.0', 'device' => 'Desktop', 'platform' => 'Windows', 'type_platform' => 'Desktop', 'time_logs' => now()]);

    $this->actingAs($currentKetuaRW);
    $this->get(route('rw.dashboard'))
        ->assertInertia(
            fn(Assert $page) => $page
                ->where('lastActivity.0.description', 'Keluar dari sistem')
        );
});

// UT.RW.DBORD.007: Akses saat data kosong (Stabilitas)
test('UT.RW.DBORD.005 - Akses Dashboard jika kondisi tidak memenuhi', function () {

    $currentKetuaRW = User::factory()->create();
    $currentKetuaRWDetail = UserDetail::factory()->create([
        'fullName'           => 'Ketua RW 01',
        'userName'           => 'ketuarw01',
        'telephone_number'   => '08333333333',
        'id_user'            => $currentKetuaRW->id,
        'id_roles'           => $this->roleKetuaRW->id, // ← Ketua RW
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $currentKetuaRW = $currentKetuaRW->fresh([
        'user_detail',
        'user_detail.rt', // ← agar rt->id tidak null di controller
    ]);
    $this->actingAs($currentKetuaRW);

    $this->get(route('rw.dashboard'))
        ->assertStatus(200)
        ->assertInertia(
            fn(Assert $page) => $page
                ->has('allBankSampah')
                ->has('nasabah')
        );
});
