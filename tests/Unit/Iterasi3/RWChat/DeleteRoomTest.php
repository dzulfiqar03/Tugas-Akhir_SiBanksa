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

function deleteRoomChatRW(UserDetail $user, int|string $roomId)
{
    return test()->actingAs($user->user)
        ->delete(route('rw.delete-roomChat', $roomId));
}
/**
 * Helper: kirim pesan ke "AI Banksa" (chatbot).
 * Route sebenarnya: POST /bank-sampah/chatbot/create{id} -> name('rw.add-chatbot')
 */
function kirimPesanChatBotRW(UserDetail $pengirimDetail, int|string $targetId, string $message)
{
    return test()->actingAs($pengirimDetail->user)
        ->post(route('rw.add-chatbot', $targetId), [
            'message' => $message,
            'name'    => 'AI Banksa',
        ]);
}

beforeEach(function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);
});

test('UT.RW.DELCHAT.001 - delete room chat berhasil', function () {

    $user = buatUserChatRW(3, 'hapus1', 'User Hapus');

    $mock = Mockery::mock(\App\Services\ChatServices::class);

    $mock->shouldReceive('deleteRoomChat')
        ->once()
        ->andReturn(true);

    $this->app->instance(
        \App\Services\ChatServices::class,
        $mock
    );

    deleteRoomChatRW($user, 1)
        ->assertRedirect()
        ->assertSessionHas(
            'message',
            'Data berhasil dihapus'
        );

});

test('UT.RW.DELCHAT.002 - delete room chat gagal', function () {

    $user = buatUserChatRW(1, 'hapus2', 'User Hapus');

    $mock = Mockery::mock(\App\Services\ChatServices::class);

    $mock->shouldReceive('deleteRoomChat')
        ->once()
        ->andReturn(false);

    $this->app->instance(
        \App\Services\ChatServices::class,
        $mock
    );

    deleteRoomChatRW($user, 1)
        ->assertSessionHas(
            'error',
            'Gagal menghapus'
        );

});
