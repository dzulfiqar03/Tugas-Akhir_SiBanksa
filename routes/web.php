<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\PreferenceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\System\InternetConnController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->user_detail->id_roles;

        if ($role ==1) {
            return redirect()->route('rw.dashboard');
        } elseif ($role == 2) {
            return redirect()->route('dashboard');
        } elseif ($role == 3) {
            return redirect()->route('warga.dashboard');
        }
    }
});

Route::get('/testInternet', [InternetConnController::class, 'checkConnection'])->name('check-internet');

Route::middleware(['conn'])->group(function () {

    Route::middleware(['auth'])->group(function () {

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

    require __DIR__ . '/auth.php';

    Route::redirect('/', 'login');
});
