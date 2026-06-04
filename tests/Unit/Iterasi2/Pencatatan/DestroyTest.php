<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\BankSampah\Sampah;
use App\Services\BankSampah\JadwalServices;
use App\Services\ChatServices;
use App\Models\UserChat;
use App\Services\BankSampah\PencatatanServices;

beforeEach(function () {
    // Setup environment data
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

    $this->setoran = PencatatanSetoran::factory()->create([
        'id_jadwal' => $this->jadwal->id,
        'id_userdetail' => $this->currentUserDetail->id,
        'total_setoran' => 20000
    ]);

    $this->pencatatanItems = PencatatanSetoranItems::create([
        'pencatatan_setoran_id' => $this->setoran->id,
        'sampah_id' => $this->sampah->id,
        'harga_satuan' => $this->sampah->harga,
        'jumlah' => 2,
        'subtotal' => 20000
    ]);
});

// =========================================================================
// UT.STRN.DESTROY.001: Sukses Update
// =========================================================================
test('UT.STRN.DESTROY.001 - Menghapus Data Pencatatan Setoran per jadwal yang ada di database', function () {


    $payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'id_jadwal' => $this->jadwal->id,
        'items' => [
            [
                'sampah_id' => $this->sampah->id,
                'harga_satuan' => $this->sampah->harga,
                'jumlah' => 2,
            ]
        ]
    ];


    $response = $this->post(route('add-setoran'), $payload);

    $this->assertDatabaseHas('pencatatan_setoran', ['id' => $this->setoran->id]);

    $this->assertDatabaseHas('pencatatan_setoran_items', ['pencatatan_setoran_id' => $this->setoran->id]);

    $response = $this->delete(route('delete-pencatatan', $this->pencatatanItems->id));

    $response->assertRedirect();
    $response->assertSessionHas('message', 'Data berhasil dihapus');

    // Pastikan data hilang dari database
    $this->assertDatabaseMissing('pencatatan_setoran_items', ['id' => $this->pencatatanItems->id]);
});

// =========================================================================
// UT.STRN.DESTROY.002: Error Validasi
// =========================================================================
test('UT.STRN.DESTROY.002 - Menghapus Data Pencatatan Setoran per jadwal yang tidak ada di database', function () {


    $this->mock(PencatatanServices::class, function ($mock) {
        $mock->shouldReceive('deletePencatatan')
            ->once()
            ->andThrow(new \Exception("Database error"));
    });

    $response = $this->delete(route('delete-pencatatan', $this->pencatatanItems->id));

    $response->assertSessionHas('error', 'Gagal menghapus: Database error');
});
