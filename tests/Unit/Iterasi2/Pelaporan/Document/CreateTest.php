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

    $this->jadwal = JadwalPelaksanaan::factory()->create([
        'id_userdetail' => $this->currentUserDetail->id,
        'tanggal_setoran' => '2026-06-15'
    ]);

    $this->documentService = new DocumentArchiversServices(new DocumentArchiver(), new JadwalPelaksanaan());
});

test('UT.DOC.001 - Mengunggah dokumen (KTP/KK)', function () {
    $file = [\Illuminate\Http\UploadedFile::fake()->create('ktp.pdf', 500)];
    $data = ['id_userdetail' => $this->currentUserDetail->id, 'id_jadwal' => $this->jadwal->id, 'name' => 'KTP'];

    $this->documentService->createDocument($data, $file);

    // Sesuaikan dengan nama yang dihasilkan oleh kode Anda (menggunakan fullName dari UserDetail)
    $expectedName = "KTP_Bank_Sampah_XYZ_RT01.pdf";

    $this->assertDatabaseHas('document_archivers', ['original_filesname' => $expectedName]);

    Storage::disk('local')->assertExists('public/files/documentUser/BankSampah/RT01/' . $expectedName);
});

test('UT.DOC.002 - Mengunggah dokumen selain (KTP/KK)', function () {
    $file = [\Illuminate\Http\UploadedFile::fake()->create('bukti.pdf', 500)];
    $data = ['id_userdetail' => $this->currentUserDetail->id, 'id_jadwal' => $this->jadwal->id, 'name' => 'Bukti Bayar'];

    $this->documentService->createDocument($data, $file);

    $this->assertDatabaseHas('document_archivers', ['name' => 'Bukti Bayar']);
    Storage::disk('local')->assertExists('public/files/documentUser/BankSampah/RT01/Dokumen_Bukti_Bayar_Tanggal_2026-06-15_BankSampahRT01_0.pdf');
});

test('UT.DOC.003 - Mengunggah dokumen berdasarkan role', function () {


    $file = [\Illuminate\Http\UploadedFile::fake()->create('dokumen_nasabah.pdf', 500)];
    $data = [
        'id_userdetail' => $this->currentUserDetail->id,
        'id_jadwal'     => $this->jadwal->id,
        'name'          => 'Dokumen Hasil Setoran'
    ];

    // 2. Eksekusi Service
    $this->documentService->createDocument($data, $file);


    $expectedPath = 'public/files/documentUser/BankSampah/RT01/Dokumen_Dokumen_Hasil_Setoran_Tanggal_2026-06-15_BankSampahRT01_0.pdf';

    Storage::disk('local')->assertExists($expectedPath);
});

test('UT.DOC.004 - Mengunggah dokumen kosong', function () {
    $file = [];
    $data = ['id_userdetail' => $this->currentUserDetail->id, 'id_jadwal' => $this->jadwal->id, 'name' => 'KTP'];

    $result = $this->documentService->createDocument($data, $file);

    $this->assertEmpty($result);
});
