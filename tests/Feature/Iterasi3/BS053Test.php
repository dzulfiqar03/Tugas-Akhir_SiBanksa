<?php

use App\Models\BankSampah\Sampah;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('Unggah Bukti Pencairan Sukses', function () {
    // 1. Setup: Login
    $this->loginAsBankSampah();


    $user = User::factory()->create([
        'email' => 'muhammaddzulfiqar03@gmail.com',
        'password' => bcrypt('julll123')
    ]);

    $payload = [
        'id_user' => $user->id,
        'id_gender' => 1,
        'userName' => 'muhammaddzulfiqar03',
        'fullName' => 'Muhammad Dzulfiqar',
        'id_roles' => 3,
        'id_rt' => 7,
        'telephone_number' => '081234567890',
        'address' => 'Gresik Kota',
        'status' => 'Disetujui',
        'status_transaction' => 'Disetujui',
        'pencairan_via' => 'Non-Tunai'
    ];

    $userDetail = UserDetail::factory()->bankSampah()->create($payload);


    $jadwal = \App\Models\BankSampah\JadwalPelaksanaan::factory()->create([
        'id_userdetail' => $userDetail->id,
        'tanggal_setoran' => '2027-01-01'
    ]);
    $sampah = Sampah::factory()->create([
        'id_userdetail' => $userDetail->id,
        'nama_sampah' => 'Plastik',
        'satuan' => 'Lusin',
        'kategori' => 'Non Daur Ulang',
        'harga' => 124000,
        'saldo' => 2000
    ]);

    $responseCreateSetoran = \App\Models\BankSampah\PencatatanSetoran::factory()->create([
        'id_jadwal' => $jadwal->id,
        'id_userdetail' => $userDetail->id,
        'total_setoran' => 200000
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


    Storage::fake('public');

    $file = UploadedFile::fake()->create('dokumen_nasabah.pdf', 500);

    $unggahResponse = $this->put("bank-sampah/transaksi/create", [

        'id' => 2,
        'id_userdetail' => $userDetail->id,
        'fullName' => 'Bank Sampah Basmi',
        'id_userbank' => $userbank->id,
        'pencatatan_setoran_id' => $responseCreateSetoran->id,
        'id_jadwal' => $jadwal->id,
        'fileDoc'       => [$file]

    ]);

    $unggahResponse->assertSessionHasNoErrors();

});
