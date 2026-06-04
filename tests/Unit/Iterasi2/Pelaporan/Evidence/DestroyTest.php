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
    $this->payload = [
        'id_userdetail' => $this->currentUserDetail->id,
        'id_jadwal'     => $this->jadwal->id,
        'name'          => 'KTP', // Sesuaikan dengan logika validator Anda
        'fileDoc'       => [
            // Menggunakan UploadedFile untuk mensimulasikan file
            \Illuminate\Http\UploadedFile::fake()->create('dokumen_ktp.pdf', 1024, 'application/pdf'),
            \Illuminate\Http\UploadedFile::fake()->image('foto_ktp.jpg'),
        ],
    ];

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
    $uploadedResult = $this->evidenceService->createEvidence($data, $fileImg);
    $this->evidence = $uploadedResult[0];
});

// =========================================================================
// UT.EVID.DESTROY.001: Sukses Update
// =========================================================================
test('UT.EVID.DESTROY.001 - Menghapus data evidence yang ada di database', function () {


    $this->assertDatabaseHas('evidence_archivers', ['id' => $this->evidence->id]);

    $response = $this->delete(route('delete-evidence', $this->evidence->id));

    $response->assertRedirect();
    $response->assertSessionHas('message', 'Evidence berhasil dihapus');

    // Pastikan data hilang dari database
    $this->assertDatabaseMissing('evidence_archivers', ['id' => $this->evidence->id]);
});

// =========================================================================
// UT.EVID.DESTROY.002: Error Validasi
// =========================================================================
test('UT.EVID.DESTROY.002 - Menghapus data evidence yang tidak ada di database', function () {


    $this->mock(EvidenceArchiversServices::class, function ($mock) {
        $mock->shouldReceive('deleteEvidence')
            ->once()
            ->andThrow(new \Exception("Database error"));
    });

    $response = $this->delete(route('delete-evidence', $this->evidence->id));

    $response->assertSessionHas('error', 'Gagal menghapus: Database error');
});
