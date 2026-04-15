<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\PreferenceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\System\InternetConnController;
use App\Http\Controllers\WelcomePageController;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::get('/testInternet', [InternetConnController::class, 'checkConnection'])->name('check-internet');
Route::get('/session-expired', [App\Http\Controllers\SessionExpiredController::class, 'index'])->name('session.expired');

require __DIR__ . '/auth.php';

Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->user_detail->id_roles;

        return match ($role) {
            1 => redirect()->route('rw.dashboard'),
            2 => redirect()->route('dashboard'),
            3 => redirect()->route('warga.dashboard'),
            default => redirect('/login'),
        };
    }
    return redirect('/login');
});

Route::get('/welcome', [WelcomePageController::class, 'index'])->name('welcome');
Route::middleware(['auth', 'session'])->group(function () {

    Route::post('/notifications/{id}/read', [NotificationController::class, 'readNotif'])->name('notifications.read');
    Route::post('/notifications/readAll', [NotificationController::class, 'readAllNotif'])->name('notifications.readAll');

    Route::controller(ProfileController::class)
        ->prefix('/profile')
        ->name('profile.')
        ->group(function () {

            Route::get('/', 'edit')->name('edit');
            Route::patch('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
            Route::post('/edit', 'editAll')->name('profile-edit');
        });


    Route::middleware(['verified'])->group(function () {
        Route::get('/preference', [PreferenceController::class, 'index'])->name('preference');

        require __DIR__ . '/KetuaRW/ketuarw.php';
        require __DIR__ . '/BankSampah/banksampah.php';
        require __DIR__ . '/Warga/warga.php';
    });
});

Route::fallback([PageController::class, 'pageNotFound'])->name('fallback');
