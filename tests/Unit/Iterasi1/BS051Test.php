<?php

use App\Models\User;
use App\Models\Roles;
use App\Models\UserDetail;
use App\Models\RTPerumahan;
use App\Services\BankSampah\NasabahServices;
use Illuminate\Support\Facades\Hash;


/**
 * SKENARIO 1: PENDAFTARAN & GENERATE AKUN
 * Menguji apakah service berhasil membuat User, men-hash password,
 * dan menghasilkan username otomatis.
 */
test('Bernilai true jika nasabah berhasil dibuat dengan status awal pengajuan', function () {
    // 1. Setup lingkungan (Auth & Seed)
    $this->loginAsBankSampah();

    $roleNasabah = Roles::factory()->create(['role' => 'Warga']);


    $rt = \App\Models\RTPerumahan::first();
    $payload = [
        'fullName'    => 'Muhammad Dzulfiqar',
        'id_gender'   => 1,
        'id_rt'       => $rt->id,
        'id_roles'    => $roleNasabah->id,
        'phoneNumber' => '081234567890',
        'status'      => 'Pengajuan Verifikasi',
    ];

    // 2. Jalankan Service
    $service = app(NasabahServices::class);
    $result = $service->createNasabah($payload);

    // 3. Verifikasi Database & Logika
    $userDetail = UserDetail::where('fullName', 'Muhammad Dzulfiqar')->first();

    expect($userDetail)->not->toBeNull();
    expect($userDetail->status)->toBe('Pengajuan Verifikasi');
    expect($userDetail->userName)->toBe('dzulfiqar_rt01'); // Contoh logic auto-username

    // Pastikan User induknya juga tercipta
    expect($userDetail->user->email)->toContain('@');
    expect(Hash::check('12345678', $userDetail->user->password))->toBeTrue();
});


test('Status nasabah dapat berubah menjadi Disetujui setelah diverifikasi admin', function () {
    $this->loginAsBankSampah();

    $roleNasabah = Roles::factory()->create(['role' => 'Warga']);


    $rt = \App\Models\RTPerumahan::first();
    $payload = [
        'fullName'    => 'Muhammad Dzulfiqar',
        'id_gender'   => 1,
        'id_rt'       => $rt->id,
        'id_roles'    => $roleNasabah->id,
        'phoneNumber' => '081234567890',
        'status'      => 'Pengajuan Verifikasi',
    ];

    // 2. Jalankan Service
    $service = app(NasabahServices::class);
    $result = $service->createNasabah($payload);


    // 2. Data untuk update status
    $updatePayload = [
        'status'   => 'Disetujui'
    ];

    // 3. Eksekusi Update (Urutan: ID, Payload)
    $updatedData = $service->updateNasabah($result->id, $updatePayload);

    // 4. Cek perubahan di database
    $updatedData = UserDetail::where('fullName', 'Muhammad Dzulfiqar')->first();
    expect($updatedData->status)->toBe('Disetujui');
});

/**
 * SKENARIO 3: NEGATIVE TEST (VALIDASI)
 * Menguji sistem menolak pendaftaran jika field wajib tidak diisi.
 */
test('Sistem melempar error jika pendaftaran nasabah tidak disertakan identitas', function () {
    $this->loginAsBankSampah();
    $service = app(NasabahServices::class);

    $payload = [
        'fullName' => null,
        'userName' => null,
        'telephone_number' => null,
        'id_rt'    => null,
        'id_gender' => null
    ];

    expect(fn() => $service->createNasabah($payload))
        ->toThrow(\Exception::class);
});
