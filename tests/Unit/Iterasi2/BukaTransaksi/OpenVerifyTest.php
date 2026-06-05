<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Notifications\Admin\BankSampahReminder;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    // Setup environment data
    $this->gender = Gender::factory()->create();
    $this->rt     = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);
    $this->roleKetuaRW = Roles::factory()->create(['id' => 1, 'role' => 'Ketua RW']);
    $this->roleWarga = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);
    $this->roleBankSampah = Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);

    // Setup User Nasabah yang akan diupdate
    $this->ketuaRW = User::factory()->create();

    UserDetail::factory()->create([
        'fullName'         => 'Ketua RW',
        'userName'         => 'ketuarw01',
        'telephone_number' => '08123456789',
        'id_user'            => $this->ketuaRW->id,
        'id_roles'           => $this->roleKetuaRW->id,
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);

    $this->actingAs($this->ketuaRW);

    $this->bankSampah = User::factory()->create();
    $this->bankSampahDetail = UserDetail::factory()->create([
         'fullName'         => 'Bank Sampah XYZ',
        'userName'         => 'banksampah03',
        'telephone_number' => '08333333333',
        'id_user'            => $this->bankSampah->id,
        'id_roles'           => $this->roleBankSampah->id,
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai',
    ]);


});

test('UT.RW.VERIF.001 - Memperbarui Status Transaksi dengan inputan valid', function () {
    Notification::fake();

    $response = $this->post(route('rw.open-transaction', $this->bankSampahDetail->id), [
        'message' => 'Halo Bank Sampah'
    ]);

    Notification::assertSentTo(
        $this->bankSampah,
        BankSampahReminder::class,
        function ($notification) {
            return $notification->message === 'Halo Bank Sampah';
        }
    );
});

test('UT.RW.VERIF.002 - Memperbarui Status Transaksi dengan inputan error', function () {
    Notification::fake();
    $invalidPayload = [
        'message' => '', // kosong = error
    ];

    $this->bankSampah->delete();

    $response = $this->post(route('rw.open-transaction', $this->bankSampahDetail->id), [
                         'message' => 'Pesan gagal'
                     ]);

    // Verifikasi notifikasi tidak terkirim sama sekali
    Notification::assertNothingSent();
});
