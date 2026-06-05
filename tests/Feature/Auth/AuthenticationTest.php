<?php

use App\Models\User;
use App\Models\UserDetail;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('email and password cannot be empty', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => '',
        'password' => '',
    ]);

    $this->assertGuest();
});

test('password must 8 character', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => '12345',
    ]);

    $this->assertGuest();
});

test('users with role ketua RW can access dashboard', function () {


    // 2. Buat User
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);
    $user = User::factory()->create();

    $rt = \App\Models\RTPerumahan::first(); // Ambil RT pertama
    $gender = \App\Models\Gender::where('gender', 'Laki-Laki')->first();
    UserDetail::factory()->ketuaRW()->create([
        'id_user' => $user->id,
        'id_rt'     => $rt->id,
        'id_gender' => $gender->id,
        'id_roles' => 1
    ]);

    // 4. Bertindak sebagai user ini
    $response = $this->actingAs($user)->get('KetuaRW/dashboard');

    $response->assertStatus(200);
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $this->seed([\Database\Seeders\RTSeeder::class, \Database\Seeders\RolesSeeder::class, \Database\Seeders\GenderSeeder::class]);
    $user = User::factory()->create();

    $rt = \App\Models\RTPerumahan::first(); // Ambil RT pertama
    $gender = \App\Models\Gender::where('gender', 'Laki-Laki')->first();
    UserDetail::factory()->ketuaRW()->create([
        'id_user' => $user->id,
        'id_rt'     => $rt->id,
        'id_gender' => $gender->id,
        'id_roles' => 1
    ]);

    // 4. Bertindak sebagai user ini
    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/login');
});
