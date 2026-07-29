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

function buatUserChatBS(int $idRoles, string $userName, string $fullName): UserDetail
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

function readChatBS(UserDetail $user, int|string $targetId, string $message = '')
{
    return test()->actingAs($user->user)
        ->put(route('bs.read-chat', $targetId), [
            'message' => $message,
        ]);
}

/**
 * Helper: kirim pesan ke "AI Banksa" (chatbot).
 * Route sebenarnya: POST /bank-sampah/chatbot/create{id} -> name('bs.add-chatbot')
 */
function kirimPesanChatBotBS(UserDetail $pengirimDetail, int|string $targetId, string $message)
{
    return test()->actingAs($pengirimDetail->user)
        ->post(route('bs.add-chatbot', $targetId), [
            'message' => $message,
            'name'    => 'AI Banksa',
        ]);
}

beforeEach(function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);
});

test('UT.BS.READCHAT.001 - target user tidak ditemukan', function () {

    $pengirim = buatUserChatBS(3, 'reader1', 'Reader Satu');

    readChatBS(
        $pengirim,
        999999
    )
    ->assertSessionHas(
        'error',
        'Penerima tidak ditemukan.'
    );

});

test('UT.BS.READCHAT.002 - read chat berhasil', function () {

    $pengirim = buatUserChatBS(3, 'reader2', 'Reader Dua');
    $penerima = buatUserChatBS(3, 'reader3', 'Reader Tiga');

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

    readChatBS(
        $pengirim,
        $penerima->user->id
    )
    ->assertRedirect();

});

test('UT.READ.003 - read chat gagal', function () {

    $pengirim = buatUserChatBS(3, 'reader4', 'Reader Empat');
    $penerima = buatUserChatBS(3, 'reader5', 'Reader Lima');

    $mock = Mockery::mock(\App\Services\ChatServices::class);

    $mock->shouldReceive('readChat')
        ->once()
        ->andReturn(false);

    $this->app->instance(
        \App\Services\ChatServices::class,
        $mock
    );

    readChatBS(
        $pengirim,
        $penerima->user->id
    )
    ->assertSessionHas(
        'error',
        'Gagal membaca chat.'
    );

});
