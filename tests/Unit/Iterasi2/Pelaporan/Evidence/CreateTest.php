<?php

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\EvidenceArchiver;
use App\Services\EvidenceArchiversServices;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use Illuminate\Foundation\Testing\RefreshDatabase; // Tambahkan ini

uses(RefreshDatabase::class); // Tambahkan ini di file tes Anda

beforeEach(function () {
    Storage::fake('public');

    Storage::fake('local');
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



test('UT.EVID.CREATE.001 - Mengunggah evidence nama valid', function () {
    $image = [\Illuminate\Http\UploadedFile::fake()->image('evidence.jpg')];
    $data = ['id_userdetail' => $this->currentUserDetail->id, 'name' => 'Evidence Pelaksanaan'];

    $this->evidenceService->createEvidence($data, $image);

    $this->assertDatabaseHas('evidence_archivers', ['name' => 'Evidence Pelaksanaan']);

    // GANTI INI: Gunakan allFiles() untuk melihat apa yang sebenarnya tersimpan
    $files = Storage::disk('local')->allFiles('public/photo/evidenceUser/BankSampah/RT01');

    // Assert bahwa folder tersebut tidak kosong
    $this->assertNotEmpty($files, 'File tidak ditemukan di disk local!');

    // Assert file ada di dalam array hasil allFiles
    $this->assertTrue(count($files) > 0);
});


test('UT.EVID.CREATE.002 - Mengunggah evidence berdasarkan role', function () {


    $photo = [\Illuminate\Http\UploadedFile::fake()->create('evidence.jpg', 500)];
    $data = [
        'id_userdetail' => $this->currentUserDetail->id,
        'name'          => 'Evidence Pelaksanaan',
    ];

    // 2. Eksekusi Service
    $this->evidenceService->createEvidence($data, $photo);


    $expectedPath = 'public/photo/evidenceUser/BankSampah/RT01/Evidence_Evidence_Pelaksanaan_BankSampahRT01_0.jpg';


    Storage::disk('local')->assertExists($expectedPath);
});

test('UT.EVID.CREATE.003 - Mengunggah evidence kosong', function () {
    $image = [];
    $data = ['id_userdetail' => $this->currentUserDetail->id, 'name' => 'Evidence Pelaksanaan'];

    $result = $this->evidenceService->createEvidence($data, $image);

    $this->assertEmpty($result);
});
