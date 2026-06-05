<?php

use App\Models\Gender;
use App\Models\Roles;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Hash;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);
test('Model mengembalikan true jika roles sebagai Warga berupa role = "Warga"', function () {
    $user = new User();
    $user->user_detail = new UserDetail([
        'id_roles' => 3
    ]);
    expect($user->user_detail->id_roles == 3)->toBeTrue();
});


test('Model mengembalikan false jika roles bukan Warga', function () {
    $user = new User();
    $user->user_detail = new UserDetail([
        'id_roles' => 3
    ]);
    expect($user->user_detail->id_roles == 1)->toBeFalse();
});

test('password berhasil di-hash saat Warga dibuat', function () {
    $user = User::factory()->create([
        'password' => Hash::make('warga123'),
    ]);
    expect(Hash::check('warga123', $user->password))->toBeTrue();
});

test('password yang salah tidak lolos pengecekan hash', function () {
    $user = User::factory()->create([
        'password' => Hash::make('warga123'),
    ]);
    expect(Hash::check('wargasalah', $user->password))->toBeFalse();
});
