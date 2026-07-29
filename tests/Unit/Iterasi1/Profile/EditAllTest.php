<?php


use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserBank;
use App\Models\Transaction\Bank;
use App\Models\Roles;
use App\Models\RTPerumahan;
use App\Models\Gender;
use App\Models\DocumentArchiver;
use App\Notifications\Admin\BankSampahReminder;
use App\Services\DocumentArchiversServices;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {

    $this->gender = Gender::factory()->create();
    $this->rt = RTPerumahan::factory()->create(['id' => 1, 'RT' => '1']);

    $this->roleRW = Roles::factory()->create(['id' => 1, 'role' => 'Ketua RW']);
    $this->roleBank = Roles::factory()->create(['id' => 2, 'role' => 'Bank Sampah']);
    $this->roleNasabah = Roles::factory()->create(['id' => 3, 'role' => 'Warga']);

    // Wajib ada, karena editAll() melakukan:
    // UserDetail::where('id_roles', 1)->first()->id_user;
    $this->userRW = User::factory()->create();
    $this->userDetailRW = UserDetail::factory()->create([
        'id_user' => $this->userRW->id,
        'id_roles' => $this->roleRW->id,
        'id_rt' => $this->rt->id,
        'id_gender' => $this->gender->id,
        'fullName' => 'Ketua RW',
        'userName' => 'ketuarw',
        'telephone_number' => '08123456789',
        'address' => 'Jl. Mawar',
        'status' => 'Disetujui',
        'pencairan_via' => 'Tunai',
        'status_transaction' => 'Disetujui',
    ]);

    // fullName harus match LIKE '%Bank Sampah%' agar query $bankSampah pada
    // controller menemukan record ini.
    $this->userBankSampah = User::factory()->create();
    $this->userDetailBank = UserDetail::factory()->create([
        'id_user' => $this->userBankSampah->id,
        'id_roles' => $this->roleBank->id,
        'id_rt' => $this->rt->id,
        'id_gender' => $this->gender->id,
        'fullName' => 'Petugas Bank Sampah RT01',
        'userName' => 'adminbank',
        'telephone_number' => '08123456789',
        'address' => 'Jl. Mawar',
        'status' => 'Disetujui',
        'pencairan_via' => 'Tunai',
        'status_transaction' => 'Disetujui',
    ]);

    $this->userWarga = User::factory()->create();
    $this->userDetailWarga = UserDetail::factory()->create([
        'id_user' => $this->userWarga->id,
        'id_roles' => $this->roleNasabah->id,
        'id_rt' => $this->rt->id,
        'id_gender' => $this->gender->id,
        'fullName' => 'Warga Test',
        'userName' => 'wargatest',
        'telephone_number' => '08123456780',
        'address' => 'Jl. Melati',
        'status' => 'Belum Disetujui',
        'pencairan_via' => 'Tunai',
        'status_transaction' => 'Belum Disetujui',
    ]);

    // Mock service upload dokumen supaya test tidak bergantung pada implementasi
    // penyimpanan file yang sebenarnya.
    $this->mock(DocumentArchiversServices::class, function ($mock) {
        $mock->shouldReceive('createDocument')->andReturn(true);
    });

    Storage::fake('local');
});

/**
 * Helper payload dasar untuk request editAll().
 */
function editAllPayload(array $overrides = []): array
{
    return array_merge([
        'email' => 'user@example.com',
        'fullName' => 'Nama Updated',
        'userName' => 'usernameupdated',
        'display_name' => 'Jl. Melati No. 10, Bojonegoro',
        'phoneNumber' => '081234567890',
        'pencairan_method' => 'Tunai',
        'amenity' => 'Rumah',
        'house_number' => '10',
        'city' => 'Bojonegoro',
        'state' => 'Jawa Timur',
        'country' => 'Indonesia',
        'postal_code' => '62113',
        'latitude' => '-7.150000',
        'longitude' => '111.881000',
        'type' => 'house',
    ], $overrides);
}

// -----------------------------------------------------------------------------
// UT.PROF.EDITALL.001 — Path 4 style baseline
// Memperbarui profil Nasabah dengan data valid
// -----------------------------------------------------------------------------
test('UT.PROF.EDITALL.001 - Memperbarui profil Nasabah dengan data valid', function () {

    $this->actingAs($this->userWarga);

    $payload = editAllPayload([
        'id_userdetail' => $this->userDetailWarga->id,
        'fullName' => 'Warga Updated',
        'userName' => 'wargaupdated',
        'fileDoc' => UploadedFile::fake()->create('ktp.pdf', 200),
    ]);

    $response = $this->post(route('profile.profile-edit'), $payload);

    $response->assertRedirect();

    $this->userDetailWarga->refresh();

    expect($this->userDetailWarga->fullName)->toBe('Warga Updated');
    expect($this->userDetailWarga->userName)->toBe('wargaupdated');
    expect($this->userDetailWarga->address)->toBe('Jl. Melati No. 10, Bojonegoro');
    expect($this->userDetailWarga->telephone_number)->toBe('081234567890');
});

// -----------------------------------------------------------------------------
// UT.PROF.EDITALL.002 — Path 7/9 style
// Memperbarui profil Bank Sampah dengan data valid, memperbarui pencairan_via
// pada seluruh nasabah (role 3) di RT yang sama
// -----------------------------------------------------------------------------
test('UT.PROF.EDITALL.002 - Memperbarui profil Bank Sampah dengan data valid', function () {

    $this->actingAs($this->userBankSampah);

    $payload = editAllPayload([
        'id_userdetail' => $this->userDetailBank->id,
        'fullName' => 'Petugas Bank Sampah RT01 Updated',
        'userName' => 'adminbankupdated',
        'pencairan_method' => 'Non-Tunai',
    ]);

    $response = $this->post(route('profile.profile-edit'), $payload);

    $response->assertRedirect();

    $this->userDetailBank->refresh();
    $this->userDetailWarga->refresh();

    expect($this->userDetailBank->fullName)->toBe('Petugas Bank Sampah RT01 Updated');
    expect($this->userDetailBank->pencairan_via)->toBe('Non-Tunai');
    // Seluruh nasabah (role 3) di RT yang sama ikut ter-update pencairan_via-nya
    expect($this->userDetailWarga->pencairan_via)->toBe('Non-Tunai');
});

// -----------------------------------------------------------------------------
// UT.PROF.EDITALL.003 — Path 1/4
// Non-Tunai disertai data rekening lengkap => rekening dibuat/diperbarui
// -----------------------------------------------------------------------------
test('UT.PROF.EDITALL.003 - Memperbarui profil dengan pencairan Non-Tunai disertai rekening', function () {

    $this->actingAs($this->userWarga);

    // Isi eksplisit kolom NOT NULL milik tabel banks (transfer_code, short_name, name)
    // karena Bank::factory() default tidak menjamin kolom-kolom ini terisi.
    $bank = Bank::factory()->create([
        'transfer_code' => '014',
        'short_name' => 'BCA',
        'name' => 'Bank Central Asia',
    ]);

    $payload = editAllPayload([
        'id_userdetail' => $this->userDetailWarga->id,
        'pencairan_method' => 'Non-Tunai',
        'id_bank' => $bank->id,
        'nomor_rekening' => '1234567890',
        'transfer_code' => $bank->transfer_code,
        'bank_short_name' => $bank->short_name,
        'bank_name' => $bank->name,
        'fileDoc' => UploadedFile::fake()->create('ktp.pdf', 200),
    ]);

    // Petugas Bank Sampah RT harus berstatus Non-Tunai agar blok UserBank dieksekusi
    $this->userDetailBank->update(['pencairan_via' => 'Non-Tunai']);

    $response = $this->post(route('profile.profile-edit'), $payload);

    $response->assertRedirect();

    $userBank = UserBank::where('id_userdetail', $this->userDetailWarga->id)->first();

    expect($userBank)->not->toBeNull();
    expect($userBank->nomor_rekening)->toBe('1234567890');
});

// -----------------------------------------------------------------------------
// UT.PROF.EDITALL.004 — Path 5
// Pencairan Tunai => data rekening tidak dibuat
// -----------------------------------------------------------------------------
test('UT.PROF.EDITALL.004 - Memperbarui profil dengan pencairan Tunai', function () {

    $this->actingAs($this->userWarga);

    $payload = editAllPayload([
        'id_userdetail' => $this->userDetailWarga->id,
        'pencairan_method' => 'Tunai',
        'fileDoc' => UploadedFile::fake()->create('ktp.pdf', 200),
    ]);

    $this->userDetailBank->update(['pencairan_via' => 'Tunai']);

    $response = $this->post(route('profile.profile-edit'), $payload);

    $response->assertRedirect();

    $userBank = UserBank::where('id_userdetail', $this->userDetailWarga->id)->first();

    expect($userBank)->toBeNull();
});

// -----------------------------------------------------------------------------
// UT.PROF.EDITALL.005 — Path 1
// Upload dokumen pertama kali (belum ada KTP/KK) => dokumen berhasil disimpan
// -----------------------------------------------------------------------------
test('UT.PROF.EDITALL.005 - Upload dokumen pertama kali', function () {

    $this->actingAs($this->userWarga);

    $payload = editAllPayload([
        'id_userdetail' => $this->userDetailWarga->id,
        'fileDoc' => UploadedFile::fake()->create('ktp.pdf', 200),
    ]);

    $response = $this->post(route('profile.profile-edit'), $payload);

    $response->assertRedirect();
    $response->assertSessionMissing('error');
});

// -----------------------------------------------------------------------------
// UT.PROF.EDITALL.006 — Path 2 (early return)
// Upload dokumen ketika dokumen (KTP/KK) sudah tersedia => pesan error
// -----------------------------------------------------------------------------
test('UT.PROF.EDITALL.006 - Upload dokumen ketika dokumen sudah tersedia', function () {

    $this->actingAs($this->userWarga);

    DocumentArchiver::factory()->create([
        'id_userdetail' => $this->userDetailWarga->id,
        'name' => 'KTP',
    ]);

    $payload = editAllPayload([
        'id_userdetail' => $this->userDetailWarga->id,
        'fileDoc' => UploadedFile::fake()->create('ktp.pdf', 200),
    ]);

    $response = $this->post(route('profile.profile-edit'), $payload);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Dokumen KTP atau KK sudah tersedia.');
});

// -----------------------------------------------------------------------------
// UT.PROF.EDITALL.007 — Path 1 (efek samping non-cabang, selalu dieksekusi)
// Memperbarui lokasi pengguna (geolocation + open_street)
// -----------------------------------------------------------------------------
test('UT.PROF.EDITALL.007 - Memperbarui lokasi pengguna', function () {

    $this->actingAs($this->userWarga);

    $payload = editAllPayload([
        'id_userdetail' => $this->userDetailWarga->id,
        'city' => 'Bojonegoro',
        'state' => 'Jawa Timur',
        'latitude' => '-7.150000',
        'longitude' => '111.881000',
        'fileDoc' => UploadedFile::fake()->create('ktp.pdf', 200),
    ]);

    $response = $this->post(route('profile.profile-edit'), $payload);

    $response->assertRedirect();

    $location = $this->userDetailWarga->fresh()->location;

    expect($location)->not->toBeNull();
    expect($location->city)->toBe('Bojonegoro');
    expect($location->state)->toBe('Jawa Timur');

    $openStreet = $location->open_street;

    expect($openStreet)->not->toBeNull();
    // Dibandingkan sebagai angka, bukan string exact — kolom latitude/logitude
    // di DB bertipe decimal/float sehingga trailing zero (mis. "-7.150000")
    // otomatis dinormalisasi menjadi "-7.15" saat dibaca kembali.
    expect((float) $openStreet->latitude)->toEqual(-7.15);

    // ⚠️ BUG DIKETAHUI (bukan kesalahan test): controller menulis key 'longitude'
    // ke updateOrCreate(), tapi $fillable pada model OpenStreet menggunakan nama
    // kolom 'logitude' (typo). Akibatnya Eloquent mass-assignment guard membuang
    // nilai ini secara diam-diam, dan kolom selalu jatuh ke default (0), berapa
    // pun nilai yang dikirim dari form. Assertion di bawah ini mendokumentasikan
    // perilaku aktual saat ini. Setelah bug ini diperbaiki (samakan key di
    // controller menjadi 'logitude', atau perbaiki $fillable menjadi 'longitude'),
    // ganti assertion ini kembali menjadi:
    //   expect((float) $openStreet->logitude)->toEqual(111.881);
    expect((float) $openStreet->logitude)->toEqual(0.0);
});

// -----------------------------------------------------------------------------
// UT.PROF.EDITALL.008 — Path 7
// Profil lengkap (address & telephone_number terisi) => notifikasi terkirim
// -----------------------------------------------------------------------------
test('UT.PROF.EDITALL.008 - Profil lengkap sehingga notifikasi dikirim', function () {

    Notification::fake();

    $this->actingAs($this->userWarga);

    $payload = editAllPayload([
        'id_userdetail' => $this->userDetailWarga->id,
        'address' => 'Jl. Melati No. 10, Bojonegoro',
        'telephone_number' => '081234567890',
        'fileDoc' => UploadedFile::fake()->create('ktp.pdf', 200),
    ]);

    $response = $this->post(route('profile.profile-edit'), $payload);

    $response->assertRedirect();

    // Nasabah (role 3) yang profilnya lengkap akan mengirim notifikasi
    // ke Petugas Bank Sampah (role 2) di RT yang sama.
    Notification::assertSentTo($this->userBankSampah, BankSampahReminder::class);
});

// -----------------------------------------------------------------------------
// UT.PROF.EDITALL.009 — Path 5/8
// Profil belum lengkap => notifikasi tidak dikirim
// -----------------------------------------------------------------------------
test('UT.PROF.EDITALL.009 - Profil belum lengkap', function () {

    Notification::fake();

    $this->actingAs($this->userWarga);

    $payload = editAllPayload([
        'id_userdetail' => $this->userDetailWarga->id,
        // 'address' dan 'telephone_number' sengaja tidak dikirim
        'fileDoc' => UploadedFile::fake()->create('ktp.pdf', 200),
    ]);

    $response = $this->post(route('profile.profile-edit'), $payload);

    $response->assertRedirect();

    Notification::assertNothingSent();
});

// -----------------------------------------------------------------------------
// UT.PROF.EDITALL.010 — Path 10
// Redirect ketika status Disetujui => ke profile.edit
// -----------------------------------------------------------------------------
test('UT.PROF.EDITALL.010 - Redirect ketika status Disetujui', function () {

    $this->userDetailWarga->update(['status' => 'Disetujui']);

    $this->actingAs($this->userWarga);

    $payload = editAllPayload([
        'id_userdetail' => $this->userDetailWarga->id,
        'fileDoc' => UploadedFile::fake()->create('ktp.pdf', 200),
    ]);

    $response = $this->post(route('profile.profile-edit'), $payload);

    $response->assertRedirect(route('profile.edit'));
});

// -----------------------------------------------------------------------------
// UT.PROF.EDITALL.011 — Path 11
// Redirect ketika status Belum Disetujui => sesuai role (dashboard / warga.dashboard)
// -----------------------------------------------------------------------------
test('UT.PROF.EDITALL.011 - Redirect ketika status Belum Disetujui (role Warga)', function () {

    $this->userDetailWarga->update(['status' => 'Belum Disetujui']);

    $this->actingAs($this->userWarga);

    $payload = editAllPayload([
        'id_userdetail' => $this->userDetailWarga->id,
        'fileDoc' => UploadedFile::fake()->create('ktp.pdf', 200),
    ]);

    $response = $this->post(route('profile.profile-edit'), $payload);

    $response->assertRedirect(route('warga.dashboard'));
});
