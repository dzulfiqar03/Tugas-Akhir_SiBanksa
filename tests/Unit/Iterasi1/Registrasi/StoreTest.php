<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Services\Auth\AuthServices;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;

// Di Unit Test murni, kita TIDAK MENGGUNAKAN RefreshDatabase agar tidak memicu pembuatan tabel asli.
// Kita gunakan TestCase standar Laravel untuk menangani HTTP request jalurnya.
uses(Tests\TestCase::class)->in('Unit');

// Fungsi bantuan untuk menyiapkan data payload utama agar formatnya lolos RegisterRequest
function createFormPayload(array $overrides = []): array
{
    return array_merge([
        'username' => 'warga_digital',
        'email' => 'warga@sibanksa.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'phone' => '081234567890',
        'address' => 'Jl. Kebersihan No. 10',
        'id_gender' => 1,
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via' => 'Tunai',
    ], $overrides);
}

// ==========================================
// JALUR 1: Menguji Operator Ternary $key & $payload (Role Bank Sampah)
// ==========================================
test('UT.REG.STORE.001 - Menguji proses registrasi dengan roles bank sampah', function () {
    Event::fake();

    // MOCKING: Kita palsukan AuthServices agar tidak benar-benar menyimpan ke database
    $mockUser = Mockery::mock(User::class);
    $this->instance(AuthServices::class, Mockery::mock(AuthServices::class, function ($mock) use ($mockUser) {
        $mock->shouldReceive('registerUser')->once()->andReturn($mockUser);
    }));

    $payload = [
        'id_roles' => 2,
        'id_gender' => 1,
        'status' => 'Aktif',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via' => 'Tunai',
        'bankSampah' => [
            'userName' => 'petugas_bs01',
            'fullName' => 'Petugas RT 01',
            'email' => 'petugas_bs01@sibanksa.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'id_rt' => 1,
            'phoneNumber' => '081234567890',
            'address' => 'Kantor Bank Sampah'
        ]
    ];

    $response = $this->post(route('register'), $payload);
    $response->assertRedirect(route('login'));
});

// ==========================================
// JALUR 2: Menguji Kondisional IF (Nasabah + RT Ditemukan)
// ==========================================
test('UT.REG.STORE.002 - Menguji proses registrasi dengan roles warga pada bank sampah yang telah terdaftar pada RT yang sama', function () {
    Event::fake();

    // MOCKING DATA PETUGAS: Kita buat objek palsu mirip keluaran database
    $mockPetugas = (object) [
        'status_transaction' => 'Tutup',
        'pencairan_via' => 'Tunai'
    ];

    // MOCKING QUERY: Saat controller memanggil UserDetail::where(), berikan objek palsu di atas
    $mockQuery = Mockery::mock('alias:' . UserDetail::class);
    $mockQuery->shouldReceive('where')->with('id_rt', 1)->andReturnSelf();
    $mockQuery->shouldReceive('where')->with('id_roles', 2)->andReturnSelf();
    $mockQuery->shouldReceive('where')->with(Mockery::any())->andReturnSelf();
    $mockQuery->shouldReceive('first')->andReturn($mockPetugas);

    // MOCKING SERVICE LOGIC
    $mockUser = Mockery::mock(User::class);
    $this->instance(AuthServices::class, Mockery::mock(AuthServices::class, function ($mock) use ($mockUser) {
        $mock->shouldReceive('registerUser')->once()->andReturn($mockUser);
    }));

    $payload = [
        'id_roles' => 3,
        'status' => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via' => 'Tunai',
        'nasabah' => [
            'userName' => 'dzulfiqar_nasabah',
            'fullName' => 'Muhammad Dzulfiqar',
            'email' => 'dzulfiqar@sibanksa.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'id_rt' => 1,
            'id_gender' => 1,
            'phoneNumber' => '089876543210',
            'address' => 'Jl. Kebangsaan No. 45'
        ]
    ];

    $response = $this->post(route('register'), $payload);
    $response->assertRedirect(route('login'));
});

// ==========================================
// JALUR 3: Menguji Kondisional IF (Validation Error Khusus Kolom RT)
// ==========================================
test('UT.REG.STORE.003 - Menguji proses registrasi sebagai nasabah jika RT bernilai null', function () {
    $payload = [
        'id_roles' => 3,
        'status' => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via' => 'Tunai',
        'nasabah' => [
            'userName' => 'nasabah_tanpa_rt',
            'fullName' => 'Nasabah Tanpa RT',
            'email' => 'tanpart@sibanksa.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'id_rt' => null, // Memicu aturan required di RegisterRequest
            'id_gender' => 1,
            'phoneNumber' => '085612345678',
            'address' => 'Alamat Umum'
        ]
    ];

    $response = $this->post(route('register'), $payload);
    $response->assertStatus(302);
    $response->assertSessionHasErrors(['nasabah.id_rt']);
});

// ==========================================
// JALUR 4: Menguji Kondisional IF (Nasabah + RT Diisi tapi Petugas Tidak Ada)
// ==========================================
test('UT.REG.STORE.004 - Menguji proses registrasi sebagai nasabah dengan RT yang tidak memiliki bank sampah', function () {
    Event::fake();

    // MOCKING QUERY: Paksa query mengembalikan nilai NULL (seolah petugas tidak ada di DB)
    $mockQuery = Mockery::mock('alias:' . UserDetail::class);
    $mockQuery->shouldReceive('where')->with('id_rt', 99)->andReturnSelf();
    $mockQuery->shouldReceive('where')->with('id_roles', 2)->andReturnSelf();
    $mockQuery->shouldReceive('where')->with(Mockery::any())->andReturnSelf();
    $mockQuery->shouldReceive('first')->andReturn(null);

    // MOCKING SERVICE LOGIC
    $mockUser = Mockery::mock(User::class);
    $this->instance(AuthServices::class, Mockery::mock(AuthServices::class, function ($mock) use ($mockUser) {
        $mock->shouldReceive('registerUser')->once()->andReturn($mockUser);
    }));

    $payload = [
        'id_roles' => 3,
        'status' => 'Pengajuan Verifikasi',
        'status_transaction' => 'Belum Disetujui',
        'pencairan_via' => 'Tunai',
        'nasabah' => [
            'userName' => 'nasabah_rt_kosong',
            'fullName' => 'Nasabah RT Kosong',
            'email' => 'rtkosong@sibanksa.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'id_rt' => 99,
            'id_gender' => 2,
            'phoneNumber' => '087812345678',
            'address' => 'Masyarakat RT 99'
        ]
    ];

    $response = $this->post(route('register'), $payload);
    $response->assertRedirect(route('login'));
});

// ==========================================
// JALUR 5: Menguji Blok Try-Catch (Pemicu Exception/Error)
// ==========================================
test('UT.REG.STORE.005 - Menguji proses registrasi jika gagal atau crash', function () {
    $payload = [
        'id_roles' => 2,
        'bankSampah' => [
            'userName' => '' // Memicu validation error agar mental ke catch/back
        ]
    ];

    $response = $this->post(route('register'), $payload);
    $response->assertStatus(302);
    $response->assertSessionHasErrors();
});
