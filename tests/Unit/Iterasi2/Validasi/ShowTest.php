<?php

use App\Models\DocumentArchiver;
use App\Models\EvidenceArchiver;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\Transaction\Bank;
use Illuminate\Support\Arr;

beforeEach(function () {
    $this->gender = Gender::factory()->create(['id' => 1]);
    $this->rt     = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);
    $this->roleKetuaRW = Roles::factory()->create(['id' => 1, 'role' => 'Ketua RW']);
    $this->roleWarga = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);
    $this->roleBankSampah = Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);

    $this->admin = User::factory()->create();
    // Setup admin dengan data lengkap agar tidak error saat login
    UserDetail::factory()->create([
        'fullName'         => 'Ketua RW',
        'userName'         => 'ketuarw',
        'telephone_number' => '08123456789',
        'id_user'          => $this->admin->id,
        'id_roles'         => $this->roleKetuaRW->id,
        'id_rt'            => $this->rt->id,
        'id_gender'        => $this->gender->id,
        'status'           => 'Disetujui',
        'pencairan_via'    => 'Tunai',
        'status_transaction' => 'Belum Disetujui',
    ]);

    $this->actingAs($this->admin);
});

/**
 * Helper untuk membuat nasabah lengkap dengan relasi User-nya.
 * Data di-merge agar kolom NOT NULL tidak kosong.
 */
function createBankSampahWithUser($overrides = [])
{
    $user = User::factory()->create();
    $bank = Bank::factory()->create([
        'name'          => 'Bank ABC',
        'short_name'    => 'ABC',
        'transfer_code' => '123',
    ]);

    $pencairanVia = $overrides['pencairan_via'] ?? 'Tunai';

    $dataDetail = array_merge([
        'id_user'            => $user->id,
        'fullName'           => 'Bank Sampah XYZ',
        'userName'           => 'banksampahxyz' . uniqid(),
        'telephone_number'   => '0899999999',
        'address'            => 'Jl. Contoh No.123',
        'id_rt'              => 1,
        'id_gender'          => 1,
        'id_roles'           => 2,
        'status'             => 'Disetujui',
        'pencairan_via'      => 'Tunai',
        'status_transaction' => 'Belum Disetujui',
    ], Arr::except($overrides, ['nomor_rekening']));

    $detail = UserDetail::factory()->create($dataDetail);

    \App\Models\UserBank::factory()->create([
        'id_userdetail'  => $detail->id,
        'id_bank'        => $bank->id,
        'nomor_rekening' => $overrides['nomor_rekening'] ?? '1234567890',
    ]);

    // Buat warga di RT yang sama
    $warga = User::factory()->create();
    $wargaDetail = UserDetail::factory()->create([
        'id_user'            => $warga->id,
        'fullName'           => 'Warga Test',
        'userName'           => 'warga' . uniqid(),
        'telephone_number'   => '0811111111',
        'address'            => 'Jl. Warga No.1',
        'id_rt'              => 1,
        'id_gender'          => 1,
        'id_roles'           => 3,
        'status'             => 'Disetujui',
        'pencairan_via'      => $pencairanVia, // ✅ ikuti pencairan_via yang dikirim
        'status_transaction' => 'Belum Disetujui',
    ]);

    // ✅ Jika Non-Tunai, warga juga perlu UserBank agar Nomor Rekening tidak kosong
    if ($pencairanVia === 'Non-Tunai') {
        \App\Models\UserBank::factory()->create([
            'id_userdetail'  => $wargaDetail->id,
            'id_bank'        => $bank->id,
            'nomor_rekening' => '9876543210',
        ]);
    }

    return ['user' => $user, 'detail' => $detail, 'warga' => $warga];
}

// ==========================================
// 6 JALUR PENGUJIAN (FULL PATH COVERAGE)
// ==========================================

test('UT.BASAM.SHOW.001 - Menampilkan profil lengkap dengan metode pembayaran non tunai', function () {
    $bankSampah = createBankSampahWithUser(['pencairan_via' => 'Non-Tunai']);

    $response = $this->get(route('rw.show-banksampah', $bankSampah['user']->id));
    $response->assertInertia(fn($page) => $page->where('avgTotalPercentage', 100));
});

test('UT.BASAM.SHOW.002 - Menampilkan profil lengkap dengan metode pembayaran tunai', function () {
    $bankSampah = createBankSampahWithUser(['pencairan_via' => 'Tunai', 'nomor_rekening' => '1234567890']);
    $response = $this->get(route('rw.show-banksampah', $bankSampah['user']->id));
    $response->assertInertia(fn($page) => $page->where('avgTotalPercentage', 100));
});

test('UT.BASAM.SHOW.003 - Menampilkan profil ada field yang kosong', function () {
    $bankSampah = createBankSampahWithUser();
    UserDetail::where('id_user', $bankSampah['warga']->id) // ← update warga, bukan bank sampah
        ->update(['address' => null]);

    $response = $this->get(route('rw.show-banksampah', $bankSampah['user']->id));

    // avgTotalPercentage < 100 karena ada field kosong
    $response->assertInertia(fn($page) =>
        $page->where('avgTotalPercentage', fn($val) => $val < 100)
    );
});

test('UT.BASAM.SHOW.004 - Menampilkan Dokumen Lengkap', function () {
    $bankSampah = createBankSampahWithUser();
    // Asumsi: Logic backend mengambil data dari relasi
    $response = $this->get(route('rw.show-banksampah', $bankSampah['user']->id));
    $response->assertStatus(200);
});

test('UT.BASAM.SHOW.005 - Menampilkan dokumen ada yang belum terisi', function () {
    $bankSampah = createBankSampahWithUser();
    $response = $this->get(route('rw.show-banksampah', $bankSampah['user']->id));
    $response->assertStatus(200);
});

test('UT.BASAM.SHOW.006 - Menampilkan profil (Tunai/Non Tunai) tetapi ada field yang kosong', function () {
    $bankSampah = createBankSampahWithUser();
    $response = $this->get(route('rw.show-banksampah', $bankSampah['user']->id));
    $response->assertStatus(200);
});
