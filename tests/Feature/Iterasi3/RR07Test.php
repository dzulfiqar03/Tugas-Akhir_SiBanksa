<?php

use App\Models\BankSampah\JadwalPelaksanaan;
use App\Models\User;
use App\Models\UserChat;
use App\Models\UserDetail;
use App\Notifications\Admin\ChatSendNotif;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

function buatPenerimaChatRR(string $userName, string $fullName): UserDetail
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
        'id_roles'           => 3,
        'pencairan_via'      => 'Non-Tunai',
        'status'             => 'Disetujui',
        'status_transaction' => 'Disetujui',
    ]);
}

test('TC.IT3.RRChat.001.001 - mengirim pesan kepada pengguna lain', function () {
    $pengirim = $this->loginAsKetuaRW();
    $penerima = buatPenerimaChatRR('penerima1', 'Penerima Satu');

    $response = $this->post(route('rw.add-chat', $penerima->user->id), [
        'message' => 'Selamat pagi',
        'name'    => 'Penerima Satu',
    ]);

    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('user_chats', [
        'id_userdetail' => $penerima->id,
        'sender_id'     => $pengirim->user->id,
        'message'       => 'Selamat pagi',
    ]);
});

test('TC.IT3.RRChat.001.002 - memperbarui isi pesan', function () {
    $pengirim = $this->loginAsKetuaRW();
    $penerima = buatPenerimaChatRR('penerima2', 'Penerima Dua');

    $chat = UserChat::create([
        'id_userdetail' => $penerima->id,
        'sender_id'     => $pengirim->user->id,
        'message'       => 'Pesan awal',
        'time'          => now()->format('H:i'),
    ]);


    $response = $this->put(route('rw.update-chat', $penerima->user->id), [
        'id'      => $chat->id,
        'message' => 'Pesan telah diperbarui',
    ]);

    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('user_chats', [
        'id'      => $chat->id,
        'message' => 'Pesan telah diperbarui',
    ]);
});

test('TC.IT3.RRChat.001.003 - menghapus pesan', function () {
    $pengirim = $this->loginAsKetuaRW();
    $penerima = buatPenerimaChatRR('penerima3', 'Penerima Tiga');

    $chat = UserChat::create([
        'id_userdetail' => $penerima->id,
        'sender_id'     => $pengirim->user->id,
        'message'       => 'Pesan yang akan dihapus',
        'time'          => now()->format('H:i'),
    ]);

    $response = $this->delete(route('rw.delete-chat', $chat->id));

    $response->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('user_chats', [
        'id' => $chat->id,
    ]);
});


test('TC.IT3.RRChat.001.004 - mengirim notifikasi kepada penerima', function () {
    Notification::fake();

    $pengirim = $this->loginAsKetuaRW();
    $penerima = buatPenerimaChatRR('penerima4', 'Penerima Empat');

    $response = $this->post(route('rw.add-chat', $penerima->user->id), [
        'message' => 'Halo',
        'name'    => 'Penerima Empat',
    ]);

    $response->assertSessionHasNoErrors();

    Notification::assertSentTo($penerima->user, ChatSendNotif::class);
});


test('TC.IT3.RRChat.002.001 - penerima tidak ditemukan', function () {
    $this->loginAsKetuaRW();

    $response = $this->post(route('rw.add-chat', 9999), [
        'message' => 'Halo',
        'name'    => 'User Tidak Ada',
    ]);

    $response->assertSessionHas('error');

    $this->assertDatabaseMissing('user_chats', [
        'message' => 'Halo',
    ]);
});
