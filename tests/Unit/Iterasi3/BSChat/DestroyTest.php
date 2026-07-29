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

function destroyChatBS(UserDetail $user, int|string $chatId)
{
    return test()->actingAs($user->user)
        ->delete(route('bs.delete-chat', $chatId));
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

test('UT.BS.DESCHAT.001 - hapus chat berhasil', function () {

    $user = buatUserChatBS(3, 'destroy1', 'User Destroy');

    $mock = Mockery::mock(\App\Services\ChatServices::class);

    $mock->shouldReceive('deleteChat')
        ->once()
        ->with(1)
        ->andReturn(true);

    $this->app->instance(
        \App\Services\ChatServices::class,
        $mock
    );

    destroyChatBS($user, 1)
        ->assertRedirect()
        ->assertSessionHas(
            'message',
            'Data berhasil dihapus'
        );

});

test('UT.BS.DESCHAT.002 - hapus chat gagal', function () {

    $user = buatUserChatBS(3, 'destroy2', 'User Destroy');

    $mock = Mockery::mock(\App\Services\ChatServices::class);

    $mock->shouldReceive('deleteChat')
        ->once()
        ->with(1)
        ->andReturn(false);

    $this->app->instance(
        \App\Services\ChatServices::class,
        $mock
    );

    destroyChatBS($user, 1)
        ->assertSessionHas(
            'error',
            'Gagal menghapus'
        );

});
