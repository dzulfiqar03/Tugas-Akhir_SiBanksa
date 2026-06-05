<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\BankSampah\Sampah;
use App\Services\BankSampah\SampahServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

beforeEach(function () {
    $this->gender = Gender::factory()->create(['id' => 1]);
    $this->rt     = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);

    $this->roleKetuaRW = Roles::factory()->create(['id' => 1, 'role' => 'Ketua RW']);
    $this->roleWarga = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);
    $this->roleBankSampah = Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);

    $this->adminUser = User::factory()->create();
    UserDetail::factory()->create([
        'fullName'           => 'Muhammad Dzulfiqar',
        'userName'           => 'dzulfiqar02',
        'telephone_number'   => '08333333333',
        'id_user'            => $this->adminUser->id,
        'id_roles'           => $this->roleWarga->id,
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui', // Pastikan field ini aman
    ]);

    $this->currentUser = User::factory()->create();
    $this->currentUserDetail = UserDetail::factory()->create([
        'fullName'           => 'Bank Sampah XYZ',
        'userName'           => 'banksampah03',
        'telephone_number'   => '08333333333',
        'id_user'            => $this->currentUser->id,
        'id_roles'           => $this->roleBankSampah->id,
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
    ]);

    $this->actingAs($this->currentUser);

    // 4. Fake Notification & Log
    Notification::fake();
    Log::spy();
});

// =========================================================================
// UT.SMPH2.UPDATE.001: ID Sampah Tidak Ditemukan
// =========================================================================
test('UT.SMPH2.UPDATE.001 - Memperbarui Sampah dengan id sampah yang belum ada di database', function () {
    $payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Plastik',
        'harga'         => 3000,
        'kategori'      => 'Daur Ulang',
        'satuan'        => 'kg',
        'saldo'         => 5
    ];

    $service = new SampahServices(new Sampah());

    expect(fn() => $service->updateSampah(99999, $payload))
        ->toThrow(ModelNotFoundException::class);
});

// =========================================================================
// UT.SMPH2.UPDATE.002: Saldo Mengalami Kenaikan
// =========================================================================
test('UT.SMPH2.UPDATE.002 - Memperbarui Sampah dengan nilai inputan saldo sampah lebih besar dari saldo yang telah disimpan', function () {
    $sampah = Sampah::factory()->create([
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Kertas HVS',
        'harga'         => 2000,
        'kategori'      => 'Daur Ulang',
        'satuan'        => 'kg',
        'saldo'         => 10,
    ]);

    $payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Kertas HVS',
        'harga'         => 2500,
        'satuan'        => 'kg',
        'saldo'         => 5 // 10 + 5 = 15 (Memicu Kenaikan)
    ];

    $service = new SampahServices(new Sampah());
    $result = $service->updateSampah($sampah->id, $payload);

    expect($result)->toBeTrue();
    $this->assertDatabaseHas('sampah', [
        'id'    => $sampah->id,
        'saldo' => 15
    ]);

    Notification::assertSentTo(
        [$this->adminUser],
        \App\Notifications\Admin\SampahUpdate::class
    );
});

// =========================================================================
// UT.SMPH2.UPDATE.003: Saldo Mengalami Penurunan
// =========================================================================
test('UT.SMPH2.UPDATE.003 - Memperbarui Sampah dengan nilai inputan saldo sampah lebih kecil dari saldo yang telah disimpan', function () {
    $sampah = Sampah::factory()->create([
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Botol Plastik',
        'harga'         => 1500,
        'kategori'      => 'Daur Ulang', // Ditambahkan field kategori wajib
        'satuan'        => 'kg',
        'saldo'         => 10,
    ]);

    $payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Botol Plastik',
        'harga'         => 1500,
        'kategori'      => 'Daur Ulang',
        'satuan'        => 'kg',
        'saldo'         => -4 // 10 + (-4) = 6 (Memicu Penurunan)
    ];

    $service = new SampahServices(new Sampah());
    $result = $service->updateSampah($sampah->id, $payload);

    expect($result)->toBeTrue();
    $this->assertDatabaseHas('sampah', [
        'id'    => $sampah->id,
        'saldo' => 6
    ]);

    Notification::assertSentTo(
        [$this->adminUser],
        \App\Notifications\Admin\SampahUpdate::class
    );
});

// =========================================================================
// UT.SMPH2.UPDATE.004: Return False dari Method Update
// =========================================================================
test('UT.SMPH2.UPDATE.004 - Memperbarui sampah dengan return false', function () {
    $sampah = Sampah::factory()->create([
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Besi Tua',
        'harga'         => 5000,
        'kategori'      => 'Daur Ulang', // Ditambahkan field kategori wajib
        'satuan'        => 'kg',
        'saldo'         => 10,
    ]);

    $payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Besi Tua',
        'harga'         => 5000,
        'kategori'      => 'Daur Ulang',
        'satuan'        => 'kg',
        'saldo'         => 2
    ];

    $mockModelSampah = mock(Sampah::class)->makePartial();
    $mockModelSampah->shouldReceive('update')->andReturn(false);
    $mockModelSampah->shouldReceive('findOrFail')->andReturn($mockModelSampah);

    $mockModelSampah->id = $sampah->id;
    $mockModelSampah->saldo = 10;

    $service = new SampahServices($mockModelSampah);
    $result = $service->updateSampah($sampah->id, $payload);

    expect($result)->toBeFalse();

    Log::shouldHaveReceived('warning')
        ->once()
        ->with("Update sampah gagal, tidak ada perubahan yang disimpan.");

    Notification::assertNothingSent();
});

// Pastikan role ID di beforeEach konsisten
// $this->roleKetuaRW = Roles::factory()->create(['id' => 1, 'role' => 'Ketua RW']);

test('UT.SMPH2.UPDATE.005 - Memperbarui sampah dengan pengiriman notifikasi berulang', function () {
    $secondAdmin = User::factory()->create();

    UserDetail::factory()->create([
              'fullName'           => 'Admin Kedua',
        'userName'           => 'admin02',
        'telephone_number'   => '08333333333',
        'id_user'            => $secondAdmin->id,
        'id_roles'           => 3, // Harus 3 sesuai query Service
        'id_rt'              => $this->rt->id, // Harus sama dengan RT sampah
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'id_gender'          => $this->gender->id
    ]);

    $sampah = Sampah::factory()->create([
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Kertas Bekas',
        'harga'         => 2000,
        'kategori'      => 'Daur Ulang',
        'satuan'        => 'kg',
        'saldo'         => 10,
    ]);

    $payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Kertas Bekas',
        'harga'         => 2000,
        'kategori'      => 'Daur Ulang',
        'satuan'        => 'kg',
        'saldo'         => 15
    ];

    $service = new SampahServices(new Sampah());
    $service->updateSampah($sampah->id, $payload);

    // Pastikan adminUser (dari beforeEach) dan secondAdmin masuk dalam klaim
    Notification::assertSentTo([$this->adminUser, $secondAdmin], \App\Notifications\Admin\SampahUpdate::class);
});

// =========================================================================
// UT.SMPH2.UPDATE.006: Crash Ketika Kirim Notifikasi (Catch Exception)
// =========================================================================
test('UT.SMPH2.UPDATE.006 - Memperbarui sampah dengan pengiriman notifikasi jika crash', function () {
    $sampah = Sampah::factory()->create([
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Tembaga',
        'harga'         => 50000,
        'kategori'      => 'Daur Ulang',
        'satuan'        => 'kg',
        'saldo'         => 5
    ]);

    $payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah'   => 'Tembaga',
        'harga'         => 50000,
        'kategori'      => 'Daur Ulang',
        'satuan'        => 'kg',
        'saldo'         => 2
    ];

    // Mengabaikan Notification::fake() khusus untuk test ini dan membuat mock murni
    // agar melempar exception saat metode send() internal Laravel dipanggil
    Notification::shouldReceive('send')
        ->once()
        ->andThrow(new \Exception("SMTP Server Connection Timeout"));

    $service = new SampahServices(new Sampah());
    $result = $service->updateSampah($sampah->id, $payload);

    // Jalur utama database update harus TETAP berhasil/true meski notifikasinya crash
    expect($result)->toBeTrue();

    // Log harus mencatat error exception yang terjadi saat pengiriman notifikasi
    Log::shouldHaveReceived('error')
        ->once()
        ->with("Gagal kirim notif registrasi: SMTP Server Connection Timeout");
});
