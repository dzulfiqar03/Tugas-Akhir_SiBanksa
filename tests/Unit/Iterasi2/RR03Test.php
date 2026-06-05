<?php

use App\Models\User;
use App\Models\Roles;
use App\Models\UserDetail;
use App\Models\RTPerumahan;
use App\Services\BankSampah\NasabahServices;
use App\Services\KetuaRW\KelolaBankSampahServices;
use Illuminate\Support\Facades\Hash;



test('Status transaksi nasabah dapat berubah dan terbuka menjadi Disetujui setelah diverifikasi admin', function () {
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
        'status_transaction'   => 'Disetujui'
    ];

    // 3. Eksekusi Update (Urutan: ID, Payload)
    $updatedData = $service->updateBankSampah($result->id, $updatePayload);

    // 4. Cek perubahan di database
    $updatedData = UserDetail::where('fullName', 'Petugas Bank Sampah Melati')->first();
    expect($updatedData->status_transaction)->toBe('Disetujui');
});

