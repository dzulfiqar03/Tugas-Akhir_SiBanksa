<?php

use App\Models\User;
use App\Models\Roles;
use App\Models\UserDetail;
use App\Models\RTPerumahan;
use App\Services\BankSampah\NasabahServices;
use App\Services\KetuaRW\KelolaBankSampahServices;
use Illuminate\Support\Facades\Hash;


/**
 * SKENARIO 1: PENDAFTARAN & GENERATE AKUN
 * Menguji apakah service berhasil membuat User, men-hash password,
 * dan menghasilkan username otomatis.
 */
test('Bernilai true jika bank sampah berhasil dibuat dengan status awal pengajuan', function () {
    // 1. Setup lingkungan (Auth & Seed)
    $this->loginAsKetuaRW();

    $roleBankSampah = Roles::factory()->create(['role' => 'Bank Sampah']);


    $rt = \App\Models\RTPerumahan::first();
    $payload = [
        'fullName'    => 'Bank Sampah Melati',
        'id_gender'   => 2,
        'id_rt'       => $rt->id,
        'id_roles'    => $roleBankSampah->id,
        'phoneNumber' => '081234567890',
        'status'      => 'Pengajuan Verifikasi',
    ];

    // 2. Jalankan Service
    $service = app(KelolaBankSampahServices::class);
    $result = $service->createBankSampah($payload);


    $userDetail = UserDetail::where('fullName', 'Petugas Bank Sampah Melati')->first();

    expect($userDetail)->not->toBeNull();
    expect($userDetail->status)->toBe('Pengajuan Verifikasi');
    expect($userDetail->userName)->toBe('banksampahmelati_rt01'); // Contoh logic auto-username

    // Pastikan User induknya juga tercipta
    expect($userDetail->user->email)->toContain('@');
    expect(Hash::check('12345678', $userDetail->user->password))->toBeTrue();
});


test('Status bank sampah dapat berubah menjadi Disetujui setelah diverifikasi admin', function () {
    $this->loginAsKetuaRW();

    $roleBankSampah = Roles::factory()->create(['role' => 'Bank Sampah']);


    $rt = \App\Models\RTPerumahan::first();
    $payload = [
        'fullName'    => 'Bank Sampah Melati',
        'id_gender'   => 1,
        'id_rt'       => $rt->id,
        'id_roles'    => $roleBankSampah->id,
        'phoneNumber' => '081234567890',
        'status'      => 'Pengajuan Verifikasi',
    ];

    // 2. Jalankan Service
    $service = app(KelolaBankSampahServices::class);
    $result = $service->createBankSampah($payload);


    // 2. Data untuk update status
    $updatePayload = [
        'status'   => 'Disetujui'
    ];

    // 3. Eksekusi Update (Urutan: ID, Payload)
    $updatedData = $service->updateBankSampah($result->id, $updatePayload);

    // 4. Cek perubahan di database
    $updatedData = UserDetail::where('fullName', 'Petugas Bank Sampah Melati')->first();
    expect($updatedData->status)->toBe('Disetujui');
});

/**
 * SKENARIO 3: NEGATIVE TEST (VALIDASI)
 * Menguji sistem menolak pendaftaran jika field wajib tidak diisi.
 */
test('Sistem melempar error jika pendaftaran nasabah tidak disertakan identitas', function () {
    $this->loginAsKetuaRW();
    $service = app(KelolaBankSampahServices::class);

    $payload = [
        'fullName' => null,
        'userName' => null,
        'telephone_number' => null,
        'id_rt'    => null,
        'id_gender' => null
    ];

    expect(fn() => $service->createBankSampah($payload))
        ->toThrow(\Exception::class);
});
