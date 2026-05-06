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
use App\Models\Transaction\UserTransaction;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\BankSampah\KepengurusanServices;
use App\Services\BankSampah\PencatatanServices;
use App\Services\BankSampah\TransactionServices;
use App\Services\DocumentArchiversServices;
use App\Services\EvidenceArchiversServices;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

test('Model User Detail memiliki relasi yang valid dengan User Transaction', function () {
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

    $jadwal = JadwalPelaksanaan::create([
        'id_userdetail' => $userDetail->id,
        'tanggal_setoran' => '2026-12-05'
    ]);

    $setoran = PencatatanSetoran::create([
        'id_jadwal' => $jadwal->id,
        'id_userdetail' => $userDetail->id,
        'total_setoran' => 20000
    ]);

    Storage::fake('public');

    $file = UploadedFile::fake()->create('dokumen_nasabah.pdf', 500);


    UserTransaction::factory()->create([
        'id_userdetail' => $userDetail->id,
        'pencatatan_setoran_id' => $setoran->id,
        'bukti_pembayaran'       => $file
    ]);

    // Ambil user dengan relasinya
    $userWithTransaction = UserDetail::with('user_transaction')->find($userDetail->id);

    expect($userWithTransaction->user_transaction)->toBeInstanceOf(UserTransaction::class);
});

test('Model  User Transaction memiliki relasi yang valid dengan Pencatatan Setoran', function () {
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

    $setoran = PencatatanSetoran::create([
        'id_jadwal' => $jadwal->id,
        'id_userdetail' => $userDetail->id,
        'total_setoran' => 20000
    ]);

    Storage::fake('public');

    $file = UploadedFile::fake()->create('dokumen_nasabah.pdf', 500);


    UserTransaction::factory()->create([
        'id_userdetail' => $userDetail->id,
        'pencatatan_setoran_id' => $setoran->id,
        'bukti_pembayaran'       => $file
    ]);

    // Ambil user dengan relasinya
    $userTransactionWithSetoran = UserTransaction::with('setoran')->find($userDetail->id);
    expect($userTransactionWithSetoran->setoran)->toBeInstanceOf(PencatatanSetoran::class);
});

test('Unggah bukti pembayaran', function () {
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

    $file = [
        UploadedFile::fake()->create('bukti_pencairan.pdf', 800),
    ];


    $jadwal = JadwalPelaksanaan::create([
        'id_userdetail' => $userDetail->id,
        'tanggal_setoran' => '2026-12-05'
    ]);

    $setoran = PencatatanSetoran::create([
        'id_jadwal' => $jadwal->id,
        'id_userdetail' => $userDetail->id,
        'total_setoran' => 20000
    ]);


    $bank = \App\Models\Transaction\Bank::factory()->create([
        'transfer_code' => '200',
        'name' => 'Bank Mandiri',
        'short_name' => 'Mandiri',
        'swift_code' => 'mnd',
        'logo' => 'Mandiri.jpg',
    ]);

    $userbank = \App\Models\UserBank::factory()->create([
        'id_userdetail' => $userDetail->id,
        'id_bank' => $bank->id,
        'nomor_rekening' => '21020101919110'
    ]);

    $payload = [
        'id' => $user->id,
        'id_userdetail' => $userDetail->id,
        'fullName' => 'Bank Sampah Basmi',
        'id_userbank' => $userbank->id,
        'pencatatan_setoran_id' => $setoran->id,
        'id_jadwal' => $jadwal->id,
        'fileDoc'       => [$file]
    ];

    // 3. Eksekusi Service
    $service = app(TransactionServices::class);
    $result = $service->createTransaction($payload, $file);

    expect($result)->toBeArray()->not->toBeEmpty();
});
