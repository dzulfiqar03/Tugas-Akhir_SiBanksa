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




// UT.WW.DBORD.001: Log Activity Mapping
test('UT.WW.DBORD.001 - Memproses Log Activity "LOGIN"', function () {
    $currentWarga = User::factory()->create();
    $currentWargaDetail = UserDetail::factory()->create([
        'fullName'           => 'Muhammad Fadli',
        'userName'           => 'warga01',
        'telephone_number'   => '08333333333',
        'id_user'            => $currentWarga->id,
        'id_roles'           => $this->roleWarga->id, // ← Warga
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $currentWarga = $currentWarga->fresh([
        'user_detail',
        'user_detail.rt', // ← agar rt->id tidak null di controller
    ]);


    UserLog::factory()->create(['id_userdetail' => $currentWargaDetail->id, 'action' => 'LOGIN', 'ip_address' => '127.0.0.1', 'device_agent' => 'Mozilla/5.0', 'device' => 'Desktop', 'platform' => 'Windows', 'type_platform' => 'Desktop', 'time_logs' => now()]);

    $this->actingAs($currentWarga);
    $this->get(route('warga.dashboard'))
        ->assertInertia(
            fn(Assert $page) => $page
                ->where('lastActivity.0.description', 'Masuk ke sistem')
        );
});

test('UT.WW.DBORD.002 - Memproses Log Activity “LOGOUT”', function () {
    $currentWarga = User::factory()->create();
    $currentWargaDetail = UserDetail::factory()->create([
        'fullName'           => 'Warga 01',
        'userName'           => 'warga01',
        'telephone_number'   => '08333333333',
        'id_user'            => $currentWarga->id,
        'id_roles'           => $this->roleWarga->id, // ← Warga
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $currentWarga = $currentWarga->fresh([
        'user_detail',
        'user_detail.rt', // ← agar rt->id tidak null di controller
    ]);


    UserLog::factory()->create(['id_userdetail' => $currentWargaDetail->id, 'action' => 'LOGOUT', 'ip_address' => '127.0.0.1', 'device_agent' => 'Mozilla/5.0', 'device' => 'Desktop', 'platform' => 'Windows', 'type_platform' => 'Desktop', 'time_logs' => now()]);

    $this->actingAs($currentWarga);
    $this->get(route('warga.dashboard'))
        ->assertInertia(
            fn(Assert $page) => $page
                ->where('lastActivity.0.description', 'Keluar dari sistem')
        );
});

test('UT.WW.DBORD.003 - Memproses Aktivitas Akun Pengguna Online', function () {
    $currentWarga = User::factory()->create();
    $currentWargaDetail = UserDetail::factory()->create([
        'fullName'           => 'Warga 01',
        'userName'           => 'warga01',
        'telephone_number'   => '08333333333',
        'id_user'            => $currentWarga->id,
        'id_roles'           => $this->roleWarga->id,
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $currentWarga = $currentWarga->fresh(['user_detail', 'user_detail.rt']);

    UserLog::factory()->create([
        'id_userdetail' => $currentWargaDetail->id,
        'action'        => 'LOGIN', // ← last action LOGIN = Online
        'ip_address'    => '127.0.0.1',
        'device_agent'  => 'Mozilla/5.0',
        'device'        => 'Desktop',
        'platform'      => 'Windows',
        'type_platform' => 'Desktop',
        'time_logs'     => now()
    ]);

    $this->actingAs($currentWarga);

    $this->get(route('warga.dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('nasabahList')
            // ✅ Ambil data dari collection, cari yang id-nya cocok
            ->tap(function (Assert $page) use ($currentWarga) {
                $nasabahList = collect($page->toArray()['props']['nasabahList']);
                $found = $nasabahList->firstWhere('id', $currentWarga->id);

                expect($found)->not->toBeNull()
                    ->and($found['online'])->toBe('Online');
            })
        );
});

test('UT.WW.DBORD.004 - Memproses Aktivitas Akun Pengguna Offline', function () {
    $currentWarga = User::factory()->create();
    $currentWargaDetail = UserDetail::factory()->create([
        'fullName'           => 'Warga 01',
        'userName'           => 'warga01',
        'telephone_number'   => '08333333333',
        'id_user'            => $currentWarga->id,
        'id_roles'           => $this->roleWarga->id,
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $currentWarga = $currentWarga->fresh(['user_detail', 'user_detail.rt']);

    UserLog::factory()->create([
        'id_userdetail' => $currentWargaDetail->id,
        'action'        => 'LOGOUT', // ← last action LOGOUT = Offline
        'ip_address'    => '127.0.0.1',
        'device_agent'  => 'Mozilla/5.0',
        'device'        => 'Desktop',
        'platform'      => 'Windows',
        'type_platform' => 'Desktop',
        'time_logs'     => now()
    ]);

    $this->actingAs($currentWarga);

    $this->get(route('warga.dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('nasabahList')
            // ✅ Cara yang sama dengan test 003
            ->tap(function (Assert $page) use ($currentWarga) {
                $nasabahList = collect($page->toArray()['props']['nasabahList']);
                $found = $nasabahList->firstWhere('id', $currentWarga->id);

                expect($found)->not->toBeNull()
                    ->and($found['online'])->toBe('Offline');
            })
        );
});

