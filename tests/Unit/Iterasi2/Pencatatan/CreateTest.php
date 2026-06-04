<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\Sampah;
use App\Services\BankSampah\PencatatanServices;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Http\Controllers\UserLogController;



beforeEach(function () {
    // Mocking Logger agar tidak menulis ke file log asli

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


    $this->jadwal = JadwalPelaksanaan::factory()->create([
        'id_userdetail' => $this->currentUserDetail->id,
        'tanggal_setoran' => '2027-12-05'
    ]);


    $this->sampah = Sampah::factory()->create([
        'id_userdetail' => $this->currentUserDetail->id,
        'nama_sampah' => 'Plastik',
        'satuan' => 'kg',
        'harga' => 2000,
        'saldo' => 20000,
        'kategori' => 'Non Daur Ulang'
    ]);


    $this->logMock = Mockery::mock(UserLogController::class);
    $this->logMock->shouldReceive('log')->zeroOrMoreTimes();
    $this->app->instance(UserLogController::class, $this->logMock);

    $this->service = new PencatatanServices(new PencatatanSetoran(), new PencatatanSetoranItems());
});

// UT.STRN.CREATE.001 - Input Lengkap
test('UT.STRN.CREATE.001 - Menyimpan data dengan inputan lengkap', function () {


    $data = [
        'id_jadwal' => $this->jadwal->id,
        'id_userdetail' => $this->currentUserDetail->id,
        'items' => [
            ['sampah_id' => $this->sampah->id, 'jumlah' => 2, 'harga_satuan' => 1000], // Subtotal 2000
        ]
    ];

    $result = $this->service->createPencatatanSetoran($data, '127.0.0.1', 'Mozilla');

    $this->assertDatabaseHas('pencatatan_setoran', ['total_setoran' => 2000]);
    $this->assertDatabaseHas('pencatatan_setoran_items', ['subtotal' => 2000]);
});

// UT.STRN.CREATE.002 - Input Kosong/Error
test('UT.STRN.CREATE.002 - Menyimpan data dengan inputan kosong', function () {
    $data = [];

    $response = $this->service->createPencatatanSetoran($data, '127.0.0.1', 'Mozilla');

    $this->assertDatabaseCount('pencatatan_setoran', 0);
});

// UT.STRN.CREATE.003 - Items Kosong
test('UT.STRN.CREATE.003 - Menyimpan data dengan items kosong', function () {
    $data = [
        'id_jadwal' => $this->jadwal->id,
        'id_userdetail' => $this->currentUserDetail->id,
        'items' => []
    ];

    $result = $this->service->createPencatatanSetoran($data, '127.0.0.1', 'Mozilla');

    // Berhasil tersimpan dengan grand total 0
    $this->assertDatabaseHas('pencatatan_setoran', ['total_setoran' => 0]);
    $this->assertDatabaseCount('pencatatan_setoran_items', 0);
});
