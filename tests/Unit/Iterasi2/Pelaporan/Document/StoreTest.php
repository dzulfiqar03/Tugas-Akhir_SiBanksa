<?php

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\BankSampah\Sampah;
use App\Models\DocumentArchiver;
use App\Services\BankSampah\SampahServices;
use App\Services\DocumentArchiversServices;

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

    $this->jadwal = JadwalPelaksanaan::factory()->create([
        'id_userdetail' => $this->currentUserDetail->id,
        'tanggal_setoran' => '2027-12-05'
    ]);
    $this->payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'id_jadwal'     => $this->jadwal->id,
        'name'          => 'KTP', // Sesuaikan dengan logika validator Anda
        'fileDoc'       => [
            // Menggunakan UploadedFile untuk mensimulasikan file
            \Illuminate\Http\UploadedFile::fake()->create('dokumen_ktp.pdf', 1024, 'application/pdf'),
        ],
    ];

    $this->documentService = new DocumentArchiversServices(new DocumentArchiver(), new JadwalPelaksanaan());

    $data = [
        'id_userdetail' => $this->payload['id_userdetail'],
        'id_jadwal' => $this->payload['id_jadwal'],
        'name' => $this->payload['name'],
    ];

    $fileDoc = $this->payload['fileDoc'];


});

test('UT.DOC.STORE.001 - Menyimpan Data Dokumen dengan inputan valid', function () {


    $response = $this->post(route('add-document'), $this->payload);

    // Memastikan user diarahkan kembali dan session sukses ada
    $response->assertRedirect();


    $response->assertSessionHas('message', 'Dokumen berhasil ditambahkan');

    // Memastikan data masuk ke database
    $this->assertDatabaseHas('document_archivers', ['name' => 'KTP']);
});

test('UT.DOC.STORE.002 - Menyimpan Data Dokumen dengan inputan error', function () {

    $this->mock(DocumentArchiversServices::class, function ($mock) {
        $mock->shouldReceive('createDocument')
            ->once()
            ->andThrow(new \Exception("Database error"));
    });

    $response = $this->post(route('add-document'), $this->payload);


    $response->assertSessionHas('error', 'Gagal mendaftar: Database error');
});
