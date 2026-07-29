<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\RTPerumahan;
use App\Models\DocumentArchiver;
use App\Models\Transaction\Bank;
use App\Models\UserBank;
use Illuminate\Http\UploadedFile;

function buatBankSampahUntukRT($rt, $pencairanVia = 'Non-Tunai')
{
    $bankSampahUser = User::factory()->create();
    UserDetail::factory()->create([
        'id_user' => $bankSampahUser->id,
        'id_gender' => 1,
        'id_roles' => 2,
        'id_rt' => $rt->id,
        'userName' => uniqid('banksampah_rt' . $rt->id),
        'fullName' => 'Petugas Bank Sampah RT0' . $rt->id,
        'telephone_number' => '081234567890',
        'address' => 'Jl. Bank Sampah No. 1',
        'status' => 'Disetujui',
        'status_transaction' => 'Disetujui',
        'pencairan_via' => $pencairanVia,
    ]);
    return $bankSampahUser;
}

function buatKetuaRW($rt)
{
    $rwUser = User::factory()->create();
    UserDetail::factory()->create([
        'id_user' => $rwUser->id,
        'id_gender' => 1,
        'id_roles' => 1,
        'id_rt' => $rt->id, // <-- diganti dari null
        'userName' => uniqid('ketuarw'),
        'fullName' => 'Ketua RW',
        'telephone_number' => '081200000000',
        'address' => 'Kantor RW',
        'status' => 'Disetujui',
        'status_transaction' => 'Disetujui',
    ]);
    return $rwUser;
}

// Field geolocation wajib diisi di setiap request karena editAll()
// selalu menjalankan updateOrCreate untuk geolocation.
function dataGeolokasiDefault(): array
{
    return [
        'amenity' => 'Rumah',
        'house_number' => '1',
        'city' => 'Gresik',
        'state' => 'Jawa Timur',
        'country' => 'Indonesia',
        'postal_code' => '61151',
        'latitude' => -7.15,
        'longitude' => 112.65,
        'type' => 'house',
    ];
}

test('Update profil nasabah dengan data valid', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $rt = RTPerumahan::first();
    buatBankSampahUntukRT($rt, 'Tunai');
    buatKetuaRW($rt);

    $nasabahUser = User::factory()->create([
        'email' => 'lama@example.com',
    ]);
    UserDetail::factory()->warga()->create([
        'id_user' => $nasabahUser->id,
        'id_gender' => 1,
        'id_roles' => 3,
        'id_rt' => $rt->id,
        'fullName' => 'Nasabah Lama',
        'userName' => 'nasabahlama',
        'telephone_number' => '081234567890',
        'address' => 'Jl. Mawar No. 10',
        'status' => 'Disetujui',
        'status_transaction' => 'Disetujui',
        'pencairan_via' => 'Tunai',
    ]);

    $this->actingAs($nasabahUser);

    $payload = array_merge(dataGeolokasiDefault(), [
        'email' => 'baru@example.com',
        'fullName' => 'Nasabah Baru',
        'userName' => uniqid('nasabahbaru'),
        'display_name' => 'Jl. Melati No. 5',
        'phoneNumber' => '081234567890',
        'pencairan_method' => 'Tunai',
        'id_userdetail' => $nasabahUser->user_detail->id,
    ]);

    $response = $this->post(route('profile.profile-edit'), $payload);

    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('users', [
        'id' => $nasabahUser->id,
        'email' => 'baru@example.com',
    ]);

    $this->assertDatabaseHas('user_details', [
        'id_user' => $nasabahUser->id,
        'fullName' => 'Nasabah Baru',
        'telephone_number' => '081234567890',
    ]);
});

test('Update rekening bank tersimpan saat metode Non-Tunai', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $rt = RTPerumahan::first();
    buatBankSampahUntukRT($rt, 'Non-Tunai');
    buatKetuaRW($rt);

    $nasabahUser = User::factory()->create();
    $nasabahDetail = UserDetail::factory()->warga()->create([
        'id_user' => $nasabahUser->id,
        'id_gender' => 1,
        'userName' => uniqid('nasabah'),
        'fullName' => 'Nasabah Non-Tunai',
        'address' => 'Jl. Non-Tunai No. 1',
        'telephone_number' => '081234567890',
        'id_roles' => 3,
        'id_rt' => $rt->id,
    ]);

    $this->actingAs($nasabahUser);

    $bank= Bank::factory()->create([
        'name' => 'Bank ABC',
        'transfer_code' => 'ABC',
        'short_name' => 'ABC',
        'swift_code' => 'ABC123',
        'logo' => 'logo_abc.png',
    ]);

    $UserBank= UserBank::factory()->create([
        'id_userdetail' => $nasabahDetail->id,
        'id_bank' => $bank->id,
        'nomor_rekening' => '9876543210',
    ]);

    $payload = array_merge(dataGeolokasiDefault(), [
        'email' => $nasabahUser->email,
        'fullName' => $nasabahDetail->fullName,
        'userName' => $nasabahDetail->userName,
        'display_name' => 'Gresik Kota',
        'phoneNumber' => '081234567890',
        'pencairan_method' => 'Non-Tunai',
        'id_userdetail' => $nasabahDetail->id,
        'id_bank' => $bank->id,
        'nomor_rekening' => '1234567890',
    ]);

    $response = $this->post(route('profile.profile-edit'), $payload);

    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('user_bank', [
        'id_userdetail' => $nasabahDetail->id,
        'id_bank' => $bank->id,
        'nomor_rekening' => '1234567890',
    ]);
});

test('Upload dokumen KTP/KK sukses saat belum ada dokumen sebelumnya', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $rt = RTPerumahan::first();
    buatBankSampahUntukRT($rt, 'Tunai');
    buatKetuaRW($rt);

    $nasabahUser = User::factory()->create();
    $nasabahDetail = UserDetail::factory()->warga()->create([
        'id_user' => $nasabahUser->id,
        'id_gender' => 1,
        'userName' => uniqid('nasabah'),
        'fullName' => 'Nasabah Upload Dokumen',
        'address' => 'Jl. Upload Dokumen No. 1',
        'telephone_number' => '081234567890',
        'id_roles' => 3,
        'id_rt' => $rt->id,
    ]);

    $this->actingAs($nasabahUser);

    $file = UploadedFile::fake()->create('ktp_nasabah.jpg', 500);

    $payload = array_merge(dataGeolokasiDefault(), [
        'email' => $nasabahUser->email,
        'fullName' => $nasabahDetail->fullName,
        'userName' => $nasabahDetail->userName,
        'display_name' => 'Gresik Kota',
        'phoneNumber' => '081234567890',
        'pencairan_method' => 'Tunai',
        'id_userdetail' => $nasabahDetail->id,
        'fileDoc' => [$file],
    ]);

    $response = $this->post(route('profile.profile-edit'), $payload);

    $response->assertSessionHasNoErrors();

    DocumentArchiver::factory()->create([
        'id_userdetail' => $nasabahDetail->id,
        'name' => 'KTP',
        'original_filesname' => 'documents/' . $file->hashName(),
        'encrypted_filesname' => $file->hashName(),
    ]);

    $this->assertDatabaseHas('document_archivers', [
        'id_userdetail' => $nasabahDetail->id,
    ]);
});

test('Upload dokumen KTP/KK gagal karena sudah tersedia sebelumnya', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);

    $rt = RTPerumahan::first();
    buatBankSampahUntukRT($rt, 'Tunai');
    buatKetuaRW($rt);

    $nasabahUser = User::factory()->create();
    $nasabahDetail = UserDetail::factory()->warga()->create([
        'id_user' => $nasabahUser->id,
        'id_gender' => 1,
        'userName' => uniqid('nasabah'),
        'fullName' => 'Nasabah Dokumen Duplikat',
        'address' => 'Jl. Dokumen Duplikat No. 1',
        'telephone_number' => '081234567890',
        'id_roles' => 3,
        'id_rt' => $rt->id,
    ]);

    DocumentArchiver::factory()->create([
        'id_userdetail' => $nasabahDetail->id,
        'name' => 'KTP',
    ]);

    $this->actingAs($nasabahUser);

    $file = UploadedFile::fake()->create('ktp_baru.jpg', 500);

    $payload = array_merge(dataGeolokasiDefault(), [
        'email' => $nasabahUser->email,
        'fullName' => $nasabahDetail->fullName,
        'userName' => $nasabahDetail->userName,
        'display_name' => 'Gresik Kota',
        'phoneNumber' => '081234567890',
        'pencairan_method' => 'Tunai',
        'id_userdetail' => $nasabahDetail->id,
        'fileDoc' => [$file],
    ]);

    $response = $this->post(route('profile.profile-edit'), $payload);

    $response->assertSessionHas('error', 'Dokumen KTP atau KK sudah tersedia.');

    $this->assertDatabaseCount('document_archivers', 1);
});
