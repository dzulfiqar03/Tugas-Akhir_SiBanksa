<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Warga\DataTransaksiController as WargaDataTransaksiController;
use App\Http\Controllers\Admin\Warga\JadwalPenyetoranController;
use App\Http\Controllers\Admin\Warga\TrackingSetoranController as WargaTrackingSetoranController;
use App\Http\Controllers\Admin\Warga\WargaChatController;


Route::middleware(['roles:Warga'])->prefix('Warga')->name('warga.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/transaksi', [WargaDataTransaksiController::class, 'index'])->name('data-transaksi');

    Route::controller(JadwalPenyetoranController::class)
        ->prefix('JanjiSetor')
        ->group(function () {

            Route::get('/', 'index')->name('janji-setor');
            Route::post('/Create', 'store')->name('add-janjiSetor');
            Route::put('/Update/{janjiSetor}', 'update')->name('update-janjiSetor');
            Route::delete('/Delete/{janjiSetor}', 'destroy')->name('delete-janjiSetor');
        });

    Route::get('/tracking', [WargaTrackingSetoranController::class, 'index'])->name('tracking-setoran');

    Route::controller(WargaChatController::class)
        ->prefix('chat')
        ->group(function () {

            Route::get('/', 'index')->name('chat');
            Route::post('/create{id}', 'store')->name('add-chat');
            Route::put('/update/{id}', 'update')->name('update-chat');
            Route::delete('/delete/{id}', 'destroy')->name('delete-chat');
            Route::delete('/deleteChat/{id}', 'deleteRoomChat')->name('delete-roomChat');

            Route::put('/chat/read{id}', 'readChat')->name('read-chat');
        });

    Route::post('/chatbot/create{id}', [WargaChatController::class, 'store'])->name('add-chatbot');
});
