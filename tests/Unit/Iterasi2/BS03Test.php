<?php

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\Kepengurusan;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\BankSampah\Sampah;
use App\Models\DocumentArchiver;
use App\Models\EvidenceArchiver;
use App\Models\Gender;
use App\Models\Roles;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\BankSampah\KepengurusanServices;
use App\Services\BankSampah\PencatatanServices;
use App\Services\DocumentArchiversServices;
use App\Services\EvidenceArchiversServices;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

test('Model User Detail memiliki relasi yang valid dengan model Document Archiver', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    // 1. Buat User induk terlebih dahulu
    $user = User::factory()->create([
        'email' => 'banksampah@gmail.com',
        'password' => Hash::make('banksampah123')
    ]);

    // 2. Buat Role
    $role = Roles::factory()->create(['role' => 'Bank Sampah']);

    $rt = \App\Models\RTPerumahan::first();

    $userDetail = UserDetail::create([
        'id_user'  => $user->id, // Ini yang tadi kosong/null
        'userName' => 'banksampah',
        'fullName' => 'Bank Sampah',
        'id_gender' => 1,
        'id_rt' => $rt->id,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',
        'id_roles' => $role->id,
        'pencairan_via' => 'Non-Tunai',
        'status' => 'Disetujui',
        'status_transaction' => 'Disetujui'
    ]);

    Storage::fake('public');

    $file = UploadedFile::fake()->create('dokumen_nasabah.pdf', 500);

    $jadwal = JadwalPelaksanaan::create([
        'id_userdetail' => $userDetail->id,
        'tanggal_setoran' => '2026-12-05'
    ]);

    $upload = DocumentArchiver::create([
        'id_userdetail' => $userDetail->id,
        'id_jadwal'     => $jadwal->id,
        'name'          => 'Hasil Setoran',
        'original_filesname'       => $file,
        'encrypted_filesname' => 'smsmssksmsksm'
    ]);


    $userWithDocument = UserDetail::with('document')->find($userDetail->id);


    expect($userWithDocument->document->first())->toBeInstanceOf(DocumentArchiver::class);
});

test('Model Kepengurusan memiliki relasi yang valid dengan model Evidence Archiver', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    // 1. Buat User induk terlebih dahulu
    $user = User::factory()->create([
        'email' => 'banksampah@gmail.com',
        'password' => Hash::make('banksampah123')
    ]);

    // 2. Buat Role
    $role = Roles::factory()->create(['role' => 'Bank Sampah']);

    $rt = \App\Models\RTPerumahan::first();

    $userDetail = UserDetail::create([
        'id_user'  => $user->id, // Ini yang tadi kosong/null
        'userName' => 'banksampah',
        'fullName' => 'Bank Sampah',
        'id_gender' => 1,
        'id_rt' => $rt->id,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',
        'id_roles' => $role->id,
        'pencairan_via' => 'Non-Tunai',
        'status' => 'Disetujui',
        'status_transaction' => 'Disetujui'
    ]);

    Storage::fake('public');

    $file = UploadedFile::fake()->create('dokumen_nasabah.jpg', 500);

    $jadwal = JadwalPelaksanaan::create([
        'id_userdetail' => $userDetail->id,
        'tanggal_setoran' => '2026-12-05'
    ]);

    $upload = EvidenceArchiver::create([
        'id_userdetail' => $userDetail->id,
        'id_jadwal'     => $jadwal->id,
        'name'          => 'haakaolo',
        'original_photoname'       => $file,
        'encrypted_photoname' => 'smsmssksmsksm'
    ]);


    $userWithEvidence = UserDetail::with('image')->find($userDetail->id);


    expect($userWithEvidence->image->first())->toBeInstanceOf(EvidenceArchiver::class);
});

test('Unggah dokumen bisa lebih dari 1', function () {
    // 1. Setup Lingkungan
    $this->loginAsBankSampah();


    // 1. Buat User induk terlebih dahulu
    $user = User::factory()->create([
        'email' => 'banksampah@gmail.com',
        'password' => Hash::make('banksampah123')
    ]);

    // 2. Buat Role
    $role = Roles::where('role', 'Bank Sampah')->first();

    $rt = \App\Models\RTPerumahan::first();

    $userDetail = UserDetail::create([
        'id_user'  => $user->id, // Ini yang tadi kosong/null
        'userName' => 'banksampah',
        'fullName' => 'Bank Sampah',
        'id_gender' => 1,
        'id_rt' => $rt->id,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',
        'id_roles' => $role->id,
        'pencairan_via' => 'Non-Tunai',
        'status' => 'Disetujui',
        'status_transaction' => 'Disetujui'
    ]);

    $files = [
        UploadedFile::fake()->create('ktp_nasabah.pdf', 500), // 500kb
        UploadedFile::fake()->create('bukti_domisili.pdf', 800),
    ];


    $jadwal = JadwalPelaksanaan::create([
        'id_userdetail' => $userDetail->id,
        'tanggal_setoran' => '2026-12-05'
    ]);

    $payload = [
        'id_userdetail' => $userDetail->id,
        'id_jadwal'     => $jadwal->id,
        'name'          => 'Hasil Setoran',
    ];

    // 3. Eksekusi Service
    $service = app(DocumentArchiversServices::class);
    $result = $service->createDocument($payload, $files);

    expect($result)->toHaveCount(2);
});

test('Unggah evidence bisa lebih dari 1', function () {
    // 1. Setup Lingkungan
    $this->loginAsBankSampah();


    // 1. Buat User induk terlebih dahulu
    $user = User::factory()->create([
        'email' => 'banksampah@gmail.com',
        'password' => Hash::make('banksampah123')
    ]);

    // 2. Buat Role
    $role = Roles::where('role', 'Bank Sampah')->first();

    $rt = \App\Models\RTPerumahan::first();

    $userDetail = UserDetail::create([
        'id_user'  => $user->id, // Ini yang tadi kosong/null
        'userName' => 'banksampah',
        'fullName' => 'Bank Sampah',
        'id_gender' => 1,
        'id_rt' => $rt->id,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',
        'id_roles' => $role->id,
        'pencairan_via' => 'Non-Tunai',
        'status' => 'Disetujui',
        'status_transaction' => 'Disetujui'
    ]);

    $files = [
        UploadedFile::fake()->create('ktp_nasabah.jpg', 500), // 500kb
        UploadedFile::fake()->create('bukti_domisili.jpg', 800),
    ];


    $jadwal = JadwalPelaksanaan::create([
        'id_userdetail' => $userDetail->id,
        'tanggal_setoran' => '2026-12-05'
    ]);

    $payload = [
        'id_userdetail' => $userDetail->id,
        'id_jadwal'     => $jadwal->id,
        'name'          => 'Bukti Pelaksanaan',
    ];

    // 3. Eksekusi Service
    $service = app(EvidenceArchiversServices::class);
    $result = $service->createEvidence($payload, $files);

    expect($result)->toHaveCount(2);
});
