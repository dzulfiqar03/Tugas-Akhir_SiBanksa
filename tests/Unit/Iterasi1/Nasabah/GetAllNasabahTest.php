<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\Transaction\Bank;
use App\Services\BankSampah\NasabahServices;
use Illuminate\Support\Arr;

beforeEach(function () {
    $this->gender = Gender::factory()->create(['id' => 1]);
    $this->rt     = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);
    $this->roleWarga = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);
    $this->roleBankSampah = Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);

    $this->admin = User::factory()->create();
    UserDetail::factory()->create([
        'fullName'         => 'Admin Bank',
        'userName'         => 'admin01',
        'telephone_number' => '08123456789',
        'id_user'          => $this->admin->id,
        'id_roles'         => $this->roleBankSampah->id,
        'id_rt'            => $this->rt->id,
        'id_gender'        => $this->gender->id,
        'status'           => 'Disetujui',
        'pencairan_via'    => 'Tunai',
        'status_transaction' => 'Belum Disetujui',
    ]);

    $this->actingAs($this->admin);

    $this->repository = app(NasabahServices::class);
});

/**
 * Helper untuk membuat nasabah lengkap dengan relasi User-nya.
 */
function createNasabahWithUserAndStatus(string $status, array $overrides = [])
{
    $user = User::factory()->create();

    $bank = Bank::factory()->create([
        'name'          => 'Bank ABC',
        'short_name'    => 'ABC',
        'transfer_code' => (string) rand(100, 999) . uniqid(),
    ]);

    $dataDetail = array_merge([
        'id_user'            => $user->id,
        'fullName'           => 'Nasabah ' . uniqid(),
        'userName'           => 'nasabah_' . uniqid(),
        'telephone_number'   => '0899999999',
        'address'            => 'Jl. Contoh No.123',
        'id_rt'              => 1,
        'id_gender'          => 1,
        'id_roles'           => 3, // Warga
        'status'             => $status,
        'pencairan_via'      => 'Tunai',
        'status_transaction' => 'Belum Disetujui',
    ], Arr::except($overrides, ['nomor_rekening']));

    $detail = UserDetail::factory()->create($dataDetail);

    \App\Models\UserBank::factory()->create([
        'id_userdetail'  => $detail->id,
        'id_bank'        => $bank->id,
        'nomor_rekening' => (string) rand(1000000000, 9999999999),
    ]);

    return ['user' => $user, 'detail' => $detail];
}


test('UT.NSBH.GET.001 - Nasabah dengan status Pengajuan Verifikasi berada di urutan teratas', function () {
    $disetujui = createNasabahWithUserAndStatus('Disetujui');

    $pengajuan = createNasabahWithUserAndStatus('Pengajuan Verifikasi');

    $result = $this->repository->getAllNasabah();

    $indexPengajuan = $result->search(function ($item) use ($pengajuan) {
        return $item->id === $pengajuan['user']->id;
    });

    $indexDisetujui = $result->search(function ($item) use ($disetujui) {
        return $item->id === $disetujui['user']->id;
    });

    expect($indexPengajuan)->toBeLessThan($indexDisetujui);
});

test('UT.NSBH.GET.002 - Nasabah dengan status Disetujui berada di urutan bawah', function () {
    $pengajuan = createNasabahWithUserAndStatus('Pengajuan Verifikasi');

    $disetujui = createNasabahWithUserAndStatus('Disetujui');

    $result = $this->repository->getAllNasabah();

    $indexDisetujui = $result->search(function ($item) use ($disetujui) {
        return $item->id === $disetujui['user']->id;
    });

    $indexPengajuan = $result->search(function ($item) use ($pengajuan) {
        return $item->id === $pengajuan['user']->id;
    });

    expect($indexDisetujui)->toBeGreaterThan($indexPengajuan);
});
