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

function buatUserChatRW(int $idRoles, string $userName, string $fullName): UserDetail
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

function updateChatRW(UserDetail $user, int|string $targetId, int|string $chatId, string $message)
{
    return test()->actingAs($user->user)
        ->put(route('rw.update-chat', $targetId), [
            'id'      => $chatId,
            'message' => $message,
        ]);
}

/**
 * Helper: kirim pesan ke "AI Banksa" (chatbot).
 * Route sebenarnya: POST /bank-sampah/chatbot/create{id} -> name('bs.add-chatbot')
 */
function kirimPesanChatBotBRW(UserDetail $pengirimDetail, int|string $targetId, string $message)
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

test('UT.RW.UPDCHAT.001 - target user tidak ditemukan', function () {

    $pengirim = buatUserChatRW(1, 'pengirimupdate', 'Pengirim Update');

    $response = updateChatRW(
        $pengirim,
        999999,
        1,
        'Pesan baru'
    );

    $response
        ->assertSessionHas('error', 'Penerima tidak ditemukan.');

});

test('UT.RW.UPDCHAT.002 - update chat berhasil', function () {

    $pengirim = buatUserChatRW(1, 'pengirim2', 'Pengirim');

    $penerima = buatUserChatRW(1, 'penerima2', 'Penerima');

    $chat = app(\App\Services\ChatServices::class)->createChat([
        'id_userdetail' => $penerima->id,
        'sender_id'     => $pengirim->user->id,
        'message'       => 'Pesan Lama',
        'time'          => now()->format('H:i'),
        'read_at'       => now()->format('H:i'),
        'is_read'       => false,
    ]);

    updateChatRW(
        $pengirim,
        $penerima->user->id,
        $chat->id,
        'Pesan Baru'
    )->assertRedirect();

    $this->assertDatabaseHas('user_chats', [
        'id' => $chat->id,
        'message' => 'Pesan Baru',
    ]);

});

test('UT.RW.UPDCHAT.003 - update chat gagal', function () {

    $pengirim = buatUserChatRW(1, 'pengirim3', 'Pengirim');

    $penerima = buatUserChatRW(1, 'penerima3', 'Penerima');

    $chat = app(\App\Services\ChatServices::class)->createChat([
        'id_userdetail' => $penerima->id,
        'sender_id'     => $pengirim->user->id,
        'message'       => 'Pesan Lama',
        'time'          => now()->format('H:i'),
        'read_at'       => now()->format('H:i'),
        'is_read'       => false,
    ]);

    $mock = Mockery::mock(\App\Services\ChatServices::class);

    $mock->shouldReceive('updateChat')
        ->once()
        ->andReturn(false);

    $this->app->instance(
        \App\Services\ChatServices::class,
        $mock
    );

    updateChatRW(
        $pengirim,
        $penerima->user->id,
        $chat->id,
        'Pesan Baru'
    )
    ->assertSessionHas(
        'error',
        'Gagal memperbarui chat.'
    );

});
