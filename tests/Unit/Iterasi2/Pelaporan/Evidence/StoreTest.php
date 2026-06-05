<?php

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\BankSampah\Sampah;
use App\Models\DocumentArchiver;
use App\Models\EvidenceArchiver;
use App\Services\BankSampah\SampahServices;
use App\Services\DocumentArchiversServices;
use App\Services\EvidenceArchiversServices;

beforeEach(function () {
    // Setup data dasar
    $this->gender = Gender::factory()->create(['id' => 1]);
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

    $this->payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'name'          => 'Evidence Pelaksanaan', // Sesuaikan dengan logika validator Anda
        'imgEvidence'       => [
            // Menggunakan UploadedFile untuk mensimulasikan file
            \Illuminate\Http\UploadedFile::fake()->image('evidence.jpg'),
        ],
    ];

    $this->evidenceService = new EvidenceArchiversServices(new EvidenceArchiver());

    $data = [
        'id_userdetail' => $this->payload['id_userdetail'],
        'name' => $this->payload['name'],
    ];

    $fileImg = $this->payload['imgEvidence'];


});

test('UT.EVID.STORE.001 - Menyimpan data evidence dengan inputan valid', function () {


    $response = $this->post(route('add-evidence'), $this->payload);

    // Memastikan user diarahkan kembali dan session sukses ada
    $response->assertRedirect();


    $response->assertSessionHas('message', 'Dokumen berhasil ditambahkan');

    // Memastikan data masuk ke database
    $this->assertDatabaseHas('evidence_archivers', ['name' => 'Evidence Pelaksanaan']);
});

test('UT.EVID.STORE.002 - Menyimpan data evidence dengan inputan error', function () {

    $this->mock(EvidenceArchiversServices::class, function ($mock) {
        $mock->shouldReceive('createEvidence')
            ->once()
            ->andThrow(new \Exception("Database error"));
    });

    $response = $this->post(route('add-evidence'), $this->payload);


    $response->assertSessionHas('error', 'Gagal mendaftar: Database error');
});
