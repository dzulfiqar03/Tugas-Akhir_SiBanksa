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

function readChatWW(UserDetail $user, int|string $targetId, string $message = '')
{
    return test()->actingAs($user->user)
        ->put(route('warga.read-chat', $targetId), [
            'message' => $message,
        ]);
}

/**
 * Helper: kirim pesan ke "AI Banksa" (chatbot).
 * Route sebenarnya: POST /bank-sampah/chatbot/create{id} -> name('warga.add-chatbot')
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

test('UT.WW.READCHAT.001 - target user tidak ditemukan', function () {

    $pengirim = buatUserChatWW(1, 'reader1', 'Reader Satu');

    readChatWW(
        $pengirim,
        999999
    )
    ->assertSessionHas(
        'error',
        'Penerima tidak ditemukan.'
    );

});

test('UT.WW.READCHAT.002 - read chat berhasil', function () {

    $pengirim = buatUserChatWW(1, 'reader2', 'Reader Dua');
    $penerima = buatUserChatWW(1, 'reader3', 'Reader Tiga');

    $mock = Mockery::mock(\App\Services\ChatServices::class);

    $mock->shouldReceive('readChat')
        ->once()
        ->withArgs(function ($id, $data) use ($penerima, $pengirim) {
            return $id == $penerima->user->id
                && $data['id_userdetail'] == $penerima->id
                && $data['sender_id'] == $pengirim->user->id
                && $data['is_read'] === true;
        })
        ->andReturn(true);

    $this->app->instance(
        \App\Services\ChatServices::class,
        $mock
    );

    readChatWW(
        $pengirim,
        $penerima->user->id
    )
    ->assertRedirect();

});

test('UT.WW.READCHAT.003 - read chat gagal', function () {

    $pengirim = buatUserChatWW(1, 'reader4', 'Reader Empat');
    $penerima = buatUserChatWW(1, 'reader5', 'Reader Lima');

    $mock = Mockery::mock(\App\Services\ChatServices::class);

    $mock->shouldReceive('readChat')
        ->once()
        ->andReturn(false);

    $this->app->instance(
        \App\Services\ChatServices::class,
        $mock
    );

    readChatWW(
        $pengirim,
        $penerima->user->id
    )
    ->assertSessionHas(
        'error',
        'Gagal membaca chat.'
    );

});
