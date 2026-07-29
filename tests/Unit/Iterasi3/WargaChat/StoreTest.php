<?php

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\BankSampah\PencatatanSetoran;
use App\Models\BankSampah\PencatatanSetoranItems;
use App\Models\BankSampah\Sampah;
use App\Models\Roles;
use App\Models\User;
use App\Models\UserDetail;
use App\Notifications\Admin\ChatSendNotif;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

function buatUserChatWW(int $idRoles, string $userName, string $fullName): UserDetail
{
    $user = User::factory()->create([
        'email'    => $userName . '@gmail.com',
        'password' => Hash::make('password123'),
    ]);

    $rt = \App\Models\RTPerumahan::first();

    return UserDetail::create([
        'id_user'            => $user->id,
        'userName'           => $userName,
        'fullName'           => $fullName,
        'id_gender'          => 1,
        'id_rt'              => $rt->id,
        'telephone_number'   => '081234567890',
        'address'            => 'Gresik Kota',
        'id_roles'           => $idRoles,
        'pencairan_via'      => 'Non-Tunai',
        'status'             => 'Disetujui',
        'status_transaction' => 'Disetujui',
    ]);
}

/**
 * Helper: kirim pesan chat antar user biasa (bukan ke AI Banksa).
 * Route sebenarnya: POST /bank-sampah/chat/create{id} -> name('bs.add-chat')
 */
function kirimPesanChatWW(UserDetail $pengirimDetail, int|string $targetId, string $message, string $name)
{
    return test()->actingAs($pengirimDetail->user)
        ->post(route('warga.add-chat', $targetId), [
            'message' => $message,
            'name'    => $name,
        ]);
}

/**
 * Helper: kirim pesan ke "AI Banksa" (chatbot).
 * Route sebenarnya: POST /bank-sampah/chatbot/create{id} -> name('bs.add-chatbot')
 */
function kirimPesanChatBotWW(UserDetail $pengirimDetail, int|string $targetId, string $message)
{
    return test()->actingAs($pengirimDetail->user)
        ->post(route('warga.add-chatbot', $targetId), [
            'message' => $message,
            'name'    => 'AI Banksa',
        ]);
}

beforeEach(function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);
});

/** =========================================================
 *  UT.BS.CHAT.001
 *  Penerima pesan bukan "AI Banksa"
 *  -> Pesan tersimpan sebagai chat antar pengguna + notifikasi terkirim
 * ========================================================= */
test('UT.BS.CHAT.001 - penerima pesan bukan AI Banksa, chat tersimpan dan notifikasi terkirim', function () {
    Notification::fake();

    $pengirim = buatUserChatWW(3, 'pengirim', 'Pengirim Warga');
    $target   = buatUserChatWW(2, 'penerima', 'Penerima Warga');

    kirimPesanChatWW($pengirim, $target->user->id, 'Halo, apa kabar?', 'Penerima Warga')
        ->assertRedirect();

    $this->assertDatabaseHas('user_chats', [
        'id_userdetail' => $target->id,
        'sender_id'     => $pengirim->user->id,
        'message'       => 'Halo, apa kabar?',
    ]);

    Notification::assertSentTo($target->user, ChatSendNotif::class);
});

/** =========================================================
 *  UT.BS.CHAT.002
 *  Pesan ke "AI Banksa" tanpa kata kunci apapun
 *  -> Bot merespon "Maaf saya tidak bisa memahami anda"
 * ========================================================= */
test('UT.WW.CHAT.002 - pesan ke AI Banksa tanpa kata kunci', function () {
    $user = buatUserChatWW(1, 'ketuaRW', 'Ketua RW');

    kirimPesanChatBotWW($user, $user->user->id, 'Selamat pagi semuanya');

    $this->assertDatabaseHas('user_bots', [
        'id_userdetail' => $user->id,
        'bot_response'  => 'Maaf saya tidak bisa memahami anda',
    ]);
});

/** =========================================================
 *  UT.WW.CHAT.003
 *  Kata kunci "rekening"
 *  -> Bot menampilkan nomor rekening dari angka pada pesan
 * ========================================================= */
test('UT.WW.CHAT.003 - kata kunci rekening', function () {
    $user = buatUserChatWW(3, 'warga3', 'Warga Tiga');

    kirimPesanChatBotWW($user, $user->user->id, 'Berapa nomor rekening 1234567890 saya?');

    $chat = \DB::table('user_bots')
        ->where('id_userdetail', $user->id)
        ->latest('id')
        ->first();

    expect($chat->bot_response)
        ->toContain('Rekening Anda:')
        ->toContain('1.234.567.890');
});

/** =========================================================
 *  UT.WW.CHAT.004
 *  Kata kunci "setoran", role Petugas Bank Sampah (id_roles = 2)
 *  -> Bot menampilkan total setoran nasabah bersangkutan
 * ========================================================= */
test('UT.WW.CHAT.004 - kata kunci setoran, role Petugas Bank Sampah', function () {
    $petugas = buatUserChatWW(2, 'petugas1', 'Petugas Bank Sampah');

    $jadwal = JadwalPelaksanaan::create([
        'id_userdetail'   => $petugas->id,
        'tanggal_setoran' => '2026-12-05',
    ]);

    $setoran = PencatatanSetoran::create([
        'id_jadwal'     => $jadwal->id,
        'id_userdetail' => $petugas->id,
        'total_setoran' => 50000,
    ]);

    PencatatanSetoranItems::factory()->create([
        'pencatatan_setoran_id' => $setoran->id,
        'harga_satuan'          => 1000,
        'jumlah'                => 50,
        'subtotal'              => 50000,
        'sampah_id'             => Sampah::factory()->create([
            'id_userdetail' => $petugas->id,
            'nama_sampah'   => 'Plastik',
            'harga'         => 1000,
            'satuan'        => 'kg',
            'kategori'      => 'Daur Ulang',
        ])->id,
    ]);

    kirimPesanChatBotWW($petugas, $petugas->user->id, 'Berapa total setoran saya?');

    $this->assertDatabaseHas('user_bots', [
        'id_userdetail' => $petugas->id,
        'bot_response'  => 'Total setoran Anda sampai saat ini adalah: Rp 50.000',
    ]);
});

/** =========================================================
 *  UT.WW.CHAT.005
 *  Kata kunci "setoran", role Warga/Nasabah (id_roles != 2)
 *  -> Bot menampilkan total setoran nasabah bersangkutan
 * ========================================================= */
test('UT.WW.CHAT.005 - kata kunci setoran, role Warga atau Nasabah', function () {
    $warga = buatUserChatWW(3, 'warga5', 'Warga Lima');

    $jadwal = JadwalPelaksanaan::create([
        'id_userdetail'   => $warga->id,
        'tanggal_setoran' => '2026-12-05',
    ]);

    $setoran = PencatatanSetoran::create([
        'id_jadwal'     => $jadwal->id,
        'id_userdetail' => $warga->id,
        'total_setoran' => 75000,
    ]);

    PencatatanSetoranItems::factory()->create([
        'pencatatan_setoran_id' => $setoran->id,
        'subtotal'              => 75000,
        'harga_satuan'          => 1500,
        'jumlah'                => 50,
        'sampah_id'             => Sampah::factory()->create([
            'id_userdetail' => $warga->id,
            'nama_sampah'   => 'Kertas',
            'harga'         => 1000,
            'satuan'        => 'kg',
            'kategori'      => 'Daur Ulang',
        ])->id,
    ]);

    kirimPesanChatBotWW($warga, $warga->user->id, 'Cek setoran saya dong');

    $this->assertDatabaseHas('user_bots', [
        'id_userdetail' => $warga->id,
        'bot_response'  => 'Total setoran Anda sampai saat ini adalah: Rp 75.000',
    ]);
});

/** =========================================================
 *  UT.WW.CHAT.006
 *  Kombinasi kata "setoran"+"bulan"+"ini/sekarang"
 *  -> Predicate "setoran" saja sudah tertangkap di elseif sebelumnya,
 *     sehingga blok kombinasi bulan ini/sekarang TIDAK PERNAH
 *     tereksekusi (dead code). Respons tetap berupa total setoran
 *     keseluruhan, sama seperti skenario 004/005.
 * ========================================================= */
test('UT.WW.CHAT.006 - kombinasi bulan ini atau sekarang adalah dead code', function () {
    $warga = buatUserChatWW(3, 'warga4', 'Warga Empat');

    $jadwal = JadwalPelaksanaan::create([
        'id_userdetail'   => $warga->id,
        'tanggal_setoran' => '2026-12-05',
    ]);

    $setoran = PencatatanSetoran::create([
        'id_jadwal'     => $jadwal->id,
        'id_userdetail' => $warga->id,
        'total_setoran' => 30000,
    ]);

    PencatatanSetoranItems::factory()->create([
        'pencatatan_setoran_id' => $setoran->id,
        'subtotal'              => 30000,
        'harga_satuan'          => 1000,
        'jumlah'                => 30,
        'sampah_id'             => Sampah::factory()->create([
            'id_userdetail' => $warga->id,
            'nama_sampah'   => 'Kertas',
            'harga'         => 1000,
            'satuan'        => 'kg',
            'kategori'      => 'Daur Ulang',
        ])->id,
    ]);

    kirimPesanChatBotWW($warga, $warga->user->id, 'Setoran bulan ini berapa ya?');

    $this->assertDatabaseHas('user_bots', [
        'id_userdetail' => $warga->id,
        'bot_response'  => 'Total setoran Anda sampai saat ini adalah: Rp 30.000',
    ]);
});

/** =========================================================
 *  UT.WW.CHAT.007
 *  Kata kunci "jumlah" + "rw"
 *  -> Bot menampilkan response jumlah jenis sampah di RW
 * ========================================================= */
test('UT.WW.CHAT.007 - kata kunci jumlah dan rw', function () {
    $user = buatUserChatWW(1, 'ketuaRW', 'Ketua RW');

    UserDetail::factory()
        ->count(3)
        ->sequence(fn () => [
            'id_user' => User::factory()->create([
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('password123'),
            ])->id,
            'userName' => fake()->unique()->userName(),
            'fullName' => fake()->name(),
        ])
        ->create([
            'id_gender' => 1,
            'id_rt' => $user->id_rt,
            'id_roles' => 3,
            'telephone_number' => '081234567890',
            'address' => 'Gresik Kota',
            'pencairan_via' => 'Non-Tunai',
            'status' => 'Disetujui',
            'status_transaction' => 'Disetujui',
        ]);

    kirimPesanChatBotWW($user, $user->user->id, 'Jumlah nasabah di RW berapa?');

    $chat = \DB::table('user_bots')
        ->where('id_userdetail', $user->id)
        ->latest('id')
        ->first();

    expect($chat->bot_response)
        ->toContain('Jumlah RW di wilayah anda ada ')
        ->toContain('Jumlah RW di wilayah anda ada ');
});
/** =========================================================
 *  UT.WW.CHAT.008
 *  Kata kunci "jumlah" + "sampah" (tanpa "rw")
 *  -> Bot menampilkan jumlah jenis sampah milik user
 * ========================================================= */
test('UT.WW.CHAT.008 - kata kunci jumlah dan sampah', function () {
    $user = buatUserChatWW(3, 'warga6', 'Warga Enam');

    Sampah::factory()->count(2)->create([
        'id_userdetail' => $user->id,
        'nama_sampah'   => 'Plastik',
        'harga'         => 1000,
        'satuan'        => 'kg',
        'kategori'      => 'Daur Ulang',
    ]);

    kirimPesanChatBotWW($user, $user->user->id, 'Jumlah jenis sampah saya ada berapa?');

    $chat = \DB::table('user_bots')
        ->where('id_userdetail', $user->id)
        ->latest('id')
        ->first();

    expect($chat->bot_response)
        ->toContain('Jumlah Jenis Sampah di RT anda ada')
        ->toContain('2');
});

/** =========================================================
 *  UT.WW.CHAT.009
 *  Kata kunci "jumlah" tanpa "rw"/"sampah"/"nasabah"/"setoran"
 *  -> Log tersimpan dengan bot_response kosong
 * ========================================================= */
test('UT.WW.CHAT.009 - kata kunci jumlah tanpa sub kata kunci', function () {
    $user = buatUserChatWW(3, 'warga3', 'Warga Tiga');

    kirimPesanChatBotWW($user, $user->user->id, 'Berapa jumlah nya ya?');

    $this->assertDatabaseHas('user_bots', [
        'id_userdetail' => $user->id,
        'bot_response'  => '',
    ]);
});

/** =========================================================
 *  UT.WW.CHAT.010
 *  Kata kunci lain di luar rekening/setoran/jumlah
 *  -> Bot merespon "Maaf saya tidak bisa memahami anda"
 * ========================================================= */
test('UT.WW.CHAT.010 - kata kunci di luar cakupan rekening, setoran, jumlah', function () {
    $user = buatUserChatWW(3, 'warga4', 'Warga Empat');

    // "Nasabah" ada di daftar keyword tapi tidak masuk kondisi
    // rekening/setoran/jumlah, sehingga jatuh ke else terakhir.
    kirimPesanChatBotWW($user, $user->user->id, 'Saya bank sampah tertinggi ya?');

    $this->assertDatabaseHas('user_bots', [
        'id_userdetail' => $user->id,
        'bot_response'  => 'Maaf saya tidak bisa memahami anda',
    ]);
});
