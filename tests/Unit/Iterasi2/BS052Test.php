<?php

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\BankSampah\Sampah;
use App\Models\Gender;
use App\Models\Roles;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\BankSampah\PencatatanServices;
use Illuminate\Support\Facades\Hash;


test('Model User Detail memiliki relasi yang valid dengan model Pencatatan Setoran', function () {
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


    $userWithSetoran = UserDetail::with('pencatatan')->find($userDetail->id);


    expect($userWithSetoran->pencatatan->first())->toBeInstanceOf(PencatatanSetoran::class);
});

test('Model Pencatatan Setoran memiliki relasi yang valid dengan model Pencatatan Setoran Items', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

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

    $jadwal = JadwalPelaksanaan::create([
        'id_userdetail' => $userDetail->id,
        'tanggal_setoran' => '2026-12-05'
    ]);
    $setoran = PencatatanSetoran::create([
        'id_jadwal' => $jadwal->id,
        'id_userdetail' => $userDetail->id,
        'total_setoran' => 20000
    ]);

    $sampah = Sampah::create([
        'id_userdetail' => $userDetail->id,
        'nama_sampah' => 'Plastik',
        'satuan' => 'kg',
        'harga' => 2000,
        'saldo' => 20000,
        'kategori' => 'Non Daur Ulang'
    ]);

    $pencatatanItems = PencatatanSetoranItems::create([
        'pencatatan_setoran_id' => $setoran->id,
        'sampah_id' => $sampah->id,
        'harga_satuan' => $sampah->harga,
        'jumlah' => 2,
        'subtotal' => 20000
    ]);


    $userWithSetoranItems = PencatatanSetoran::with('pencatatan_items')->find($setoran->id);

    expect($userWithSetoranItems->pencatatan_items->first())->toBeInstanceOf(PencatatanSetoranItems::class);
});

test('Model Pencatatan Setoran Items memiliki relasi yang valid dengan model Sampah', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

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

    $jadwal = JadwalPelaksanaan::create([
        'id_userdetail' => $userDetail->id,
        'tanggal_setoran' => '2026-12-05'
    ]);

    $setoran = PencatatanSetoran::create([
        'id_jadwal' => $jadwal->id,
        'id_userdetail' => $userDetail->id,
        'total_setoran' => 20000
    ]);

    $sampah = Sampah::create([
        'id_userdetail' => $userDetail->id,
        'nama_sampah' => 'Plastik',
        'satuan' => 'kg',
        'harga' => 2000,
        'saldo' => 20000,
        'kategori' => 'Non Daur Ulang'
    ]);

    $pencatatanItems = PencatatanSetoranItems::create([
        'pencatatan_setoran_id' => $setoran->id,
        'sampah_id' => $sampah->id,
        'harga_satuan' => $sampah->harga,
        'jumlah' => 2,
        'subtotal' => 20000
    ]);

    expect($pencatatanItems->sampah)->toBeInstanceOf(Sampah::class);
});

test('Setoran nasabah langsung terkalkulasi secara otomatis per setoran sampah', function () {
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


    $sampah = Sampah::create([
        'id_userdetail' => $userDetail->id,
        'nama_sampah' => 'Plastik',
        'satuan' => 'kg',
        'harga' => 2000,
        'saldo' => 20000,
        'kategori' => 'Non Daur Ulang'
    ]);

    $sampah2 = Sampah::create([
        'id_userdetail' => $userDetail->id,
        'nama_sampah' => 'Gelas',
        'satuan' => 'kg',
        'harga' => 2000,
        'saldo' => 20000,
        'kategori' => 'Non Daur Ulang'
    ]);
    $jadwal = JadwalPelaksanaan::create([
        'id_userdetail' => $userDetail->id,
        'tanggal_setoran' => '2026-12-05'
    ]);

    $payload = [
        'id_userdetail' => $userDetail->id,
        'id_jadwal'     => $jadwal->id,
        'items' => [
            [
                'sampah_id'    => $sampah->id,
                'jumlah'       => 2, // 2kg
                'harga_satuan' => 5000
            ],
            [
                'sampah_id'    => $sampah2->id,
                'jumlah'       => 1, // 1kg
                'harga_satuan' => 5000
            ]
        ]
    ];

    // 2. Eksekusi Service
    $service = app(PencatatanServices::class);
    $result = $service->createPencatatanSetoran($payload, '127.0.0.1', 'Pest-Agent');


    $item = PencatatanSetoranItems::where('pencatatan_setoran_id', $result->id)->where('sampah_id', $sampah2->id)->first();
    expect($item->subtotal)->toEqual(5000);
});

test('Setoran nasabah langsung terkalkulasi secara otomatis per jadwal pelaksanaan', function () {
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


    $sampah = Sampah::create([
        'id_userdetail' => $userDetail->id,
        'nama_sampah' => 'Plastik',
        'satuan' => 'kg',
        'harga' => 2000,
        'saldo' => 20000,
        'kategori' => 'Non Daur Ulang'
    ]);
    $jadwal = JadwalPelaksanaan::create([
        'id_userdetail' => $userDetail->id,
        'tanggal_setoran' => '2026-12-05'
    ]);

    $payload = [
        'id_userdetail' => $userDetail->id,
        'id_jadwal'     => $jadwal->id,
        'items' => [
            [
                'sampah_id'    => $sampah->id,
                'jumlah'       => 2, // 2kg
                'harga_satuan' => 5000
            ],
            [
                'sampah_id'    => $sampah->id,
                'jumlah'       => 1, // 1kg
                'harga_satuan' => 5000
            ]
        ]
    ];

    // 2. Eksekusi Service
    $service = app(PencatatanServices::class);
    $result = $service->createPencatatanSetoran($payload, '127.0.0.1', 'Pest-Agent');

    // 3. Verifikasi Hasil
    // Pastikan $result adalah model, bukan null atau redirect
    expect($result)->toBeInstanceOf(PencatatanSetoran::class);

    // Pastikan total_setoran adalah (2*5000) + (1*5000) = 15000
    expect($result->total_setoran)->toEqual(15000);

    // Ambil item pertama untuk cek subtotal
    $item = PencatatanSetoranItems::where('pencatatan_setoran_id', $result->id)->first();
    expect($item->subtotal)->toEqual(10000);
});
