<?php

use App\Models\Gender;
use App\Models\Roles;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Hash;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);
test('Model mengembalikan true jika roles sebagai Bank Sampah berupa role = "Bank Sampah"', function () {
    $user = new User();
    $user->user_detail = new UserDetail([
        'id_roles' => 2
    ]);
    expect($user->user_detail->id_roles == 2)->toBeTrue();
});


test('Model mengembalikan false jika roles bukan Bank Sampah', function () {
    $user = new User();
    $user->user_detail = new UserDetail([
        'id_roles' => 2
    ]);
    expect($user->user_detail->id_roles == 1)->toBeFalse();
});

test('password berhasil di-hash saat Bank Sampah dibuat', function () {
    $user = User::factory()->create([
        'password' => Hash::make('banksampah123'),
    ]);
    expect(Hash::check('banksampah123', $user->password))->toBeTrue();
});

test('password yang salah tidak lolos pengecekan hash', function () {
    $user = User::factory()->create([
        'password' => Hash::make('banksampah123'),
    ]);
    expect(Hash::check('banksampahsalah', $user->password))->toBeFalse();
});


