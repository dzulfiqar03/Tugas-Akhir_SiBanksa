<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Notifications\Admin\JadwalBlasting;
use App\Services\BankSampah\JadwalServices;
use App\Services\ChatServices;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    // 1. Setup Data Environment
    $this->gender = Gender::factory()->create();
    $this->rt     = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);
    Roles::factory()->create(['id' => 3, 'role' => 'Warga']);
    Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);

    // 2. Setup User & Detail
    $this->admin = User::factory()->create();
    $this->userDetail = UserDetail::factory()->create([

        'fullName'         => 'Bank Sampah XYZ',
        'userName'         => 'banksampah03',
        'telephone_number' => '08333333333',
        'id_user' => $this->admin->id,
        'id_roles' => 3,
        'id_rt' => 1,
        'status' => 'Disetujui',
        'id_gender' => $this->gender->id,
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via' => 'Tunai',
    ]);

    // 3. Mock ChatServices karena dibutuhkan di constructor
    $this->chatServiceMock = Mockery::mock(ChatServices::class);
    $this->chatServiceMock->shouldReceive('createChat')->zeroOrMoreTimes();

    // 4. Inisialisasi Service
    $this->jadwalService = new JadwalServices(new JadwalPelaksanaan(), $this->chatServiceMock);

    // 5. Setup Auth
    $this->actingAs($this->admin);

    $this->data = [
        'id_userdetail' => $this->userDetail->id,
        'tanggal_setoran' => '2026-06-15'
    ];
});

// UT.JDWL.CREATE.001 - Sukses
test('UT.JDWL.CREATE.001 - Notifikasi sukses terkirim ke semua nasabah', function () {
    Notification::fake();

    $this->jadwalService->createJadwal($this->data);

    Notification::assertSentTo($this->admin, JadwalBlasting::class);
    $this->assertDatabaseHas('jadwal_pelaksanaan', ['tanggal_setoran' => '2026-06-15']);
});

test('UT.JDWL.CREATE.002 - Notifikasi gagal terkirim', function () {
    Notification::fake();
    Notification::shouldReceive('send')->andThrow(new \Exception("Gagal koneksi"));

    Log::shouldReceive('error')->once();

    $result = $this->jadwalService->createJadwal($this->data);

    $this->assertNotNull($result); // Jadwal tetap tersimpan
});

test('UT.JDWL.CREATE.003 - Data Nasabah tidak ditemukan', function () {
    Notification::fake();

    $this->userDetail->update(['status' => 'Pending']);

    $result = $this->jadwalService->createJadwal($this->data);

    $this->assertNotNull($result);

    notification::assertNothingSent();
});
