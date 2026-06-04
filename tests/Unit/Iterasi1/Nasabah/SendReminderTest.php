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
    $this->roleWarga     = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);
    $this->roleBankSampah = Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);

    $this->nasabah = User::factory()->create();
    $this->nasabahDetail = UserDetail::factory()->create([
        'fullName'           => 'Bank Sampah XYZ',
        'userName'           => 'banksampah03',
        'telephone_number'   => '08333333333',
        'id_user'            => $this->nasabah->id,
        'id_roles'           => $this->roleBankSampah->id, // ← id = 2
        'id_rt'              => $this->rt->id,
        'id_gender'          => $this->gender->id,
        'status'             => 'Disetujui',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via'      => 'Tunai', // ← tambahkan ini jika nullable tidak diset
    ]);


    $this->actingAs($this->nasabah);
});

test('UT.NSBH.REMIND.001 - Mengirim notifikasi dengan id user tidak ditemukan', function () {
    // TAMBAHKAN BARIS INI
    Notification::fake();

    $response = $this->actingAs($this->nasabah)
        ->post(route('nasabah.send-reminder', $this->nasabah->id), [
            'missing_info' => 'KTP belum diunggah'
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Pengingat verifikasi berhasil dikirim ke nasabah!');

    // Sekarang ini akan berfungsi
    Notification::assertSentTo($this->nasabah, ReminderVerification::class);
});

test('UT.NSBH.REMIND.002 - Mengirim notifikasi dengan id user ditemukan', function () {

    Notification::shouldReceive('send')
        ->once()
        ->andThrow(new \Exception("Koneksi server gagal"));

    $response = $this->actingAs($this->nasabah)
        ->post(route('nasabah.send-reminder', $this->nasabah->id), [
            'missing_info' => 'Data tidak lengkap'
        ]);

    $response->assertSessionHas('error', 'Gagal mengirim pengingat: Koneksi server gagal');
});
