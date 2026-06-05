<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\Gender;
use App\Http\Controllers\UserLogController;
use App\Models\RTPerumahan;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    // 1. Membuat Master Data Gender & RT untuk menghindari Foreign Key & Not Null Constraint Failed
    $this->gender = Gender::factory()->create(['id' => 1]);

    // Mengisi properti 'RT' sesuai dengan constraint database tabel rt_perumahan
    $this->rt1 = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);
    $this->rt2 = RTPerumahan::factory()->create(['id' => 2, 'RT' => '2']);
    $this->rt3 = RTPerumahan::factory()->create(['id' => 3, 'RT' => '3']);

    // 2. Membuat Role Terlebih Dahulu untuk Relasi di Database
    $this->roleBankSampah = Roles::factory()->create(['role' => 'Bank Sampah']);
    $this->roleKetuaRW    = Roles::factory()->create(['role' => 'Ketua RW']);
    $this->roleWarga      = Roles::factory()->create(['role' => 'Warga']);

    // 3. Mocking UserLogController agar fungsi log() tidak mengeksekusi request asli
    $this->mockLog = mock(UserLogController::class);
    $this->mockLog->shouldReceive('log')->andReturn(null);
    app()->instance(UserLogController::class, $this->mockLog);
});

// =========================================================================
// UT.LOG.STORE.001: Menguji login dengan identitas yang tidak terdaftar
// =========================================================================
test('UT.LOG.STORE.001 - Menguji proses login dengan identitas yang belum terdaftar', function () {
    $this->withoutExceptionHandling();

    $payload = [
        'nama_bank' => 'Nama Tidak Terdaftar',
        'id_rt'     => $this->rt1->id,
        'phone'     => '081234567890',
        'password'  => 'secret123'
    ];

    expect(fn() => $this->post(route('login'), $payload))
        ->toThrow(ValidationException::class, 'Identitas tidak ditemukan. Periksa kembali penulisan nama Anda.');
});

// =========================================================================
// UT.LOG.STORE.002: Menguji login tahap 1 (Identitas benar, password kosong)
// =========================================================================
test('UT.LOG.STORE.002 - Menguji proses login dengan password tidak diisi', function () {
    $user = User::factory()->create(['password' => Hash::make('secret123')]);

    UserDetail::factory()->create([
        'fullName'         => 'Muhammad Dzulfiqar',
        'userName'         => 'dzulfiqar02',
        'id_rt'            => $this->rt2->id,
        'telephone_number' => '08991234567',
        'id_user'          => $user->id,
        'id_gender'        => $this->gender->id,
        'id_roles'         => $this->roleWarga->id,
        'status'           => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
    ]);

    $payload = [
        'nama_bank' => 'Muhammad Dzulfiqar',
        'id_rt'     => $this->rt2->id,
        'phone'     => '08991234567',
        'password'  => ''
    ];

    $response = $this->post(route('login'), $payload);

    $response->assertRedirect();
    $response->assertSessionHas('message', 'Identitas terverifikasi.');
    expect(Auth::check())->toBeFalse();
});

// =========================================================================
// UT.LOG.STORE.003: Menguji login dengan identitas benar, password salah
// =========================================================================
test('UT.LOG.STORE.003 - Menguji proses login jika inputan sudah diisi namun password tidak sesuai', function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['password' => Hash::make('password_yang_benar')]);

    UserDetail::factory()->create([
        'fullName'         => 'Muhammad Dzulfiqar',
        'userName'         => 'dzulfiqar03',
        'id_rt'            => $this->rt2->id,
        'telephone_number' => '08991234567',
        'id_user'          => $user->id,
        'id_gender'        => $this->gender->id,
        'id_roles'         => $this->roleWarga->id,
        'status'           => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
    ]);

    $payload = [
        'nama_bank' => 'Muhammad Dzulfiqar',
        'id_rt'     => $this->rt2->id,
        'phone'     => '08991234567',
        'password'  => 'password_yang_salah'
    ];

    expect(fn() => $this->post(route('login'), $payload))
        ->toThrow(ValidationException::class, 'Password yang Anda masukkan salah.');

    expect(Auth::check())->toBeFalse();
});

// =========================================================================
// UT.LOG.STORE.004: Menguji login sukses dengan role Bank Sampah
// =========================================================================
test('UT.LOG.STORE.004 - Menguji proses login sebagai bank sampah', function () {
    $user = User::factory()->create(['password' => Hash::make('secret123')]);

    UserDetail::factory()->create([
        'fullName'         => 'Petugas Bank Sampah',
        'userName'         => 'petugas_bs',
        'id_rt'            => $this->rt1->id,
        'telephone_number' => '08111111111',
        'id_user'          => $user->id,
        'id_gender'        => $this->gender->id,
        'id_roles'         => $this->roleBankSampah->id,
        'status'           => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
    ]);

    $payload = [
        'nama_bank' => 'Petugas Bank Sampah',
        'id_rt'     => $this->rt1->id,
        'phone'     => '08111111111',
        'password'  => 'secret123'
    ];

    $response = $this->post(route('login'), $payload);

    $response->assertRedirect(route('dashboard'));
    expect(Auth::check())->toBeTrue();
    expect(session('user')->id)->toBe($user->id);
});

// =========================================================================
// UT.LOG.STORE.005: Menguji login sukses dengan role Ketua RW
// =========================================================================
test('UT.LOG.STORE.005 - Menguji proses login sebagai ketua rw', function () {
    $user = User::factory()->create(['password' => Hash::make('secret123')]);

    UserDetail::factory()->create([
        'fullName'         => 'Ketua RW Tokoh',
        'userName'         => 'ketuarw01',
        'id_rt'            => $this->rt1->id,
        'telephone_number' => '08222222222',
        'id_user'          => $user->id,
        'id_gender'        => $this->gender->id,
        'id_roles'         => $this->roleKetuaRW->id,
        'status'           => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
    ]);

    $payload = [
        'nama_bank' => 'Ketua RW Tokoh',
        'id_rt'     => $this->rt1->id,
        'phone'     => '08222222222',
        'password'  => 'secret123'
    ];

    $response = $this->post(route('login'), $payload);

    $response->assertRedirect(route('rw.dashboard'));
    expect(Auth::check())->toBeTrue();
});

// =========================================================================
// UT.LOG.STORE.006: Menguji login sukses dengan role Warga
// =========================================================================
test('UT.LOG.STORE.006 - Menguji proses login sebagai warga', function () {
    $user = User::factory()->create(['password' => Hash::make('secret123')]);

    UserDetail::factory()->create([
        'fullName'         => 'Slamet Warga',
        'userName'         => 'slamet_warga',
        'id_rt'            => $this->rt3->id,
        'telephone_number' => '08333333333',
        'id_user'          => $user->id,
        'id_gender'        => $this->gender->id,
        'id_roles'         => $this->roleWarga->id,
        'status'           => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
    ]);

    $payload = [
        'nama_bank' => 'Slamet Warga',
        'id_rt'     => $this->rt3->id,
        'phone'     => '08333333333',
        'password'  => 'secret123'
    ];

    $response = $this->post(route('login'), $payload);

    $response->assertRedirect(route('warga.dashboard'));
    expect(Auth::check())->toBeTrue();
});
