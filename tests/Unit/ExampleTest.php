<?php

test('that true is true', function () {
    expect(true)->toBeTrue();
});



use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('isManager mengembalikan true jika role adalah manager', function () {
    $user = new User(['role' => 'manager']);
    expect($user->isManager())->toBeTrue();
});

test('isManager mengembalikan false jika role adalah karyawan', function () {
    $user = new User(['role' => 'karyawan']);
    expect($user->isManager())->toBeFalse();
});

test('isKaryawan mengembalikan true jika role adalah karyawan', function () {
    $user = new User(['role' => 'karyawan']);
    expect($user->isKaryawan())->toBeTrue();
});

test('isKaryawan mengembalikan false jika role adalah manager', function () {
    $user = new User(['role' => 'manager']);
    expect($user->isKaryawan())->toBeFalse();
});

test('password berhasil di-hash saat user dibuat', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

test('password yang salah tidak lolos pengecekan hash', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);
    expect(Hash::check('passwordsalah', $user->password))->toBeFalse();
});
