<?php

use App\Http\Controllers\Admin\Developer\DashboardController;
use App\Http\Controllers\Admin\Developer\DeveloperChatController;
use App\Http\Controllers\Admin\Developer\MasterJadwalController;
use App\Http\Controllers\Admin\Developer\MasterKepengurusanController;
use App\Http\Controllers\Admin\Developer\MasterSampahController;
use App\Http\Controllers\Admin\Developer\MasterUserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['roles:Developer'])->prefix('Developer')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('developer.dashboard');

    Route::get('/user', [MasterUserController::class, 'index'])->name('developer.user');
    Route::get('/user/detail/{id}', [MasterUserController::class, 'show'])->name('developer.show-user');


    Route::get('/master-sampah', [MasterSampahController::class, 'index'])->name('developer.sampah');


    Route::get('/master-kepengurusan', [MasterKepengurusanController::class, 'index'])->name('developer.kepengurusan');
    Route::get('/kepengurusan/detail/{id}', [MasterKepengurusanController::class, 'show'])->name('developer.show-kepengurusan');


    Route::get('/master-jadwal', [MasterJadwalController::class, 'index'])->name('developer.jadwal');


    Route::controller(DeveloperChatController::class)
        ->prefix('chat')
        ->group(function () {

            Route::get('/', 'index')->name('developer.chat');
            Route::post('/create{id}', 'store')->name('developer.add-chat');
            Route::put('/update/{id}', 'update')->name('developer.update-chat');
            Route::delete('/delete/{id}', 'destroy')->name('developer.delete-chat');
            Route::delete('/deleteChat/{id}', 'deleteRoomChat')->name('developer.delete-roomChat');

            Route::put('/read{id}', 'readChat')->name('developer.read-chat');
        });
});
