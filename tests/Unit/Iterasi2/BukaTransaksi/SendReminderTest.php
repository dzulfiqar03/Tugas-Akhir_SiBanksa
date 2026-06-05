<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Notifications\Admin\ReminderVerification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->gender        = Gender::factory()->create();
    $this->rt            = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);
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

test('UT.BASAM.REMIND.001 - Mengirim notifikasi dengan id user tidak ditemukan', function () {
    // TAMBAHKAN BARIS INI
    Notification::fake();

    $response = $this->actingAs($this->bankSampah)
        ->post(route('nasabah.send-reminder', $this->bankSampah->id), [
            'missing_info' => 'KTP belum diunggah'
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Pengingat verifikasi berhasil dikirim ke nasabah!');

    // Sekarang ini akan berfungsi
    Notification::assertSentTo($this->bankSampah, ReminderVerification::class);
});

test('UT.BASAM.REMIND.002 - Mengirim notifikasi dengan id user ditemukan', function () {

    Notification::shouldReceive('send')
        ->once()
        ->andThrow(new \Exception("Koneksi server gagal"));

    $response = $this->actingAs($this->bankSampah)
        ->post(route('nasabah.send-reminder', $this->bankSampah->id), [
            'missing_info' => 'Data tidak lengkap'
        ]);

    $response->assertSessionHas('error', 'Gagal mengirim pengingat: Koneksi server gagal');
});
